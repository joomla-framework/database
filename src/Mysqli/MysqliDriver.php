<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Mysqli;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseEvents;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\Event\ConnectionEvent;
use Joomla\Database\Exception\ConnectionFailureException;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Exception\UnsupportedAdapterException;
use Joomla\Database\Query\PreparableInterface;
use Joomla\Database\Query\LimitableInterface;
use Joomla\Database\UTF8MB4SupportInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * MySQLi Database Driver
 *
 * @link   https://secure.php.net/manual/en/book.mysqli.php
 * @since  1.0
 */
class MysqliDriver extends DatabaseDriver implements UTF8MB4SupportInterface
{
	/**
	 * The database connection resource.
	 *
	 * @var    \mysqli
	 * @since  1.0
	 */
	protected $connection;

	/**
	 * The default SQL mode used by the driver, this matches the sql_mode for MySQL 5.7.8+ default strict mode.
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 * @link   https://dev.mysql.com/doc/relnotes/mysql/5.7/en/news-5-7-8.html#mysqld-5-7-8-sql-mode
	 */
	protected $defaultSqlModes = [
		'ONLY_FULL_GROUP_BY',
		'STRICT_TRANS_TABLES',
		'ERROR_FOR_DIVISION_BY_ZERO',
		'NO_AUTO_CREATE_USER',
		'NO_ENGINE_SUBSTITUTION',
	];

	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $name = 'mysqli';

	/**
	 * The character(s) used to quote SQL statement names such as table names or field names, etc.
	 *
	 * If a single character string the same character is used for both sides of the quoted name, else the first character will be used for the
	 * opening quote and the second for the closing quote.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $nameQuote = '`';

	/**
	 * The null or zero representation of a timestamp for the database driver.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $nullDate = '0000-00-00 00:00:00';

	/**
	 * True if the database engine supports UTF-8 Multibyte (utf8mb4) character encoding.
	 *
	 * @var    boolean
	 * @since  1.4.0
	 */
	protected $utf8mb4 = false;

	/**
	 * The prepared statement.
	 *
	 * @var    \mysqli_stmt
	 * @since  1.5.0
	 */
	protected $prepared;

	/**
	 * Contains the current query execution status
	 *
	 * @var    array
	 * @since  1.5.0
	 */
	protected $executed = false;

	/**
	 * The minimum supported database version.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $dbMinimum = '5.5.3';

	/**
	 * Destructor.
	 *
	 * @since   1.0
	 */
	public function __destruct()
	{
		$this->disconnect();
	}

	/**
	 * Resolve the options for the database driver.
	 *
	 * @param   OptionsResolver  $resolver  The options resolver.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function configureOptions(OptionsResolver $resolver)
	{
		parent::configureOptions($resolver);

		$resolver->setDefaults(
			[
				'socket'   => null,
				'utf8mb4'  => false,
				'sqlModes' => $this->defaultSqlModes,
			]
		);

		$resolver->setAllowedTypes('socket', ['null', 'string']);
		$resolver->setAllowedTypes('utf8mb4', ['bool']);
		$resolver->setAllowedTypes('sqlModes', ['array']);
	}

	/**
	 * Connects to the database if needed.
	 *
	 * @return  void  Returns void if the database connected successfully.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function connect()
	{
		if ($this->connection)
		{
			return;
		}

		// Make sure the MySQLi extension for PHP is installed and enabled.
		if (!static::isSupported())
		{
			throw new \RuntimeException('The MySQLi extension is not available');
		}

		/*
		 * Unlike mysql_connect(), mysqli_connect() takes the port and socket as separate arguments. Therefore, we
		 * have to extract them from the host string.
		 */
		$port = $this->options['port'] ?: 3306;

		if (preg_match(
			'/^(?P<host>((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))(:(?P<port>.+))?$/',
			$this->options['host'],
			$matches
			))
		{
			// It's an IPv4 address with or without port
			$this->options['host'] = $matches['host'];

			if (!empty($matches['port']))
			{
				$port = $matches['port'];
			}
		}
		elseif (preg_match('/^(?P<host>\[.*\])(:(?P<port>.+))?$/', $this->options['host'], $matches))
		{
			// We assume square-bracketed IPv6 address with or without port, e.g. [fe80:102::2%eth1]:3306
			$this->options['host'] = $matches['host'];

			if (!empty($matches['port']))
			{
				$port = $matches['port'];
			}
		}
		elseif (preg_match('/^(?P<host>(\w+:\/{2,3})?[a-z0-9\.\-]+)(:(?P<port>[^:]+))?$/i', $this->options['host'], $matches))
		{
			// Named host (e.g example.com or localhost) with or without port
			$this->options['host'] = $matches['host'];

			if (!empty($matches['port']))
			{
				$port = $matches['port'];
			}
		}
		elseif (preg_match('/^:(?P<port>[^:]+)$/', $this->options['host'], $matches))
		{
			// Empty host, just port, e.g. ':3306'
			$this->options['host'] = 'localhost';
			$port = $matches['port'];
		}

		// ... else we assume normal (naked) IPv6 address, so host and port stay as they are or default

		// Get the port number or socket name
		if (is_numeric($port))
		{
			$this->options['port'] = (int) $port;
		}
		else
		{
			$this->options['socket'] = $port;
		}

		// Make sure the MySQLi extension for PHP is installed and enabled.
		if (!static::isSupported())
		{
			throw new UnsupportedAdapterException('The MySQLi extension is not available');
		}

		$this->connection = mysqli_init();

		// Attempt to connect to the server, use error suppression to silence warnings and allow us to throw an Exception separately.
		$connected = @$this->connection->real_connect(
			$this->options['host'], $this->options['user'], $this->options['password'], null, $this->options['port'], $this->options['socket']
		);

		if (!$connected)
		{
			throw new ConnectionFailureException(
				'Could not connect to MySQL: ' . $this->connection->connect_error,
				$this->connection->connect_errno
			);
		}

		// If needed, set the sql modes.
		if ($this->options['sqlModes'] !== [])
		{
			$this->connection->query('SET @@SESSION.sql_mode = \'' . implode(',', $this->options['sqlModes']) . '\';');
		}

		// If auto-select is enabled select the given database.
		if ($this->options['select'] && !empty($this->options['database']))
		{
			$this->select($this->options['database']);
		}

		$this->utf8mb4 = $this->serverClaimsUtf8mb4Support();

		// Set charactersets (needed for MySQL 4.1.2+).
		$this->utf = $this->setUtf();

		$this->dispatchEvent(new ConnectionEvent(DatabaseEvents::POST_CONNECT, $this));
	}

	/**
	 * Automatically downgrade a CREATE TABLE or ALTER TABLE query from utf8mb4 (UTF-8 Multibyte) to plain utf8.
	 *
	 * Used when the server doesn't support UTF-8 Multibyte.
	 *
	 * @param   string  $query  The query to convert
	 *
	 * @return  string  The converted query
	 *
	 * @since   1.4.0
	 */
	public function convertUtf8mb4QueryToUtf8($query)
	{
		if ($this->utf8mb4)
		{
			return $query;
		}

		// If it's not an ALTER TABLE or CREATE TABLE command there's nothing to convert
		$beginningOfQuery = substr($query, 0, 12);
		$beginningOfQuery = strtoupper($beginningOfQuery);

		if (!in_array($beginningOfQuery, array('ALTER TABLE ', 'CREATE TABLE'), true))
		{
			return $query;
		}

		// Replace utf8mb4 with utf8
		return str_replace('utf8mb4', 'utf8', $query);
	}

	/**
	 * Disconnects the database.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function disconnect()
	{
		// Close the connection.
		if (is_callable($this->connection, 'close'))
		{
			$this->connection->close();
		}

		$this->connection = null;

		$this->dispatchEvent(new ConnectionEvent(DatabaseEvents::POST_DISCONNECT, $this));
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 *
	 * @since   1.0
	 */
	public function escape($text, $extra = false)
	{
		$this->connect();

		$result = $this->connection->real_escape_string($text);

		if ($extra)
		{
			$result = addcslashes($result, '%_');
		}

		return $result;
	}

	/**
	 * Test to see if the MySQLi connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   1.0
	 */
	public static function isSupported()
	{
		// At the moment we depend on mysqlnd extension, so we additionally test for mysqli_stmt_get_result
		return function_exists('mysqli_connect') && function_exists('mysqli_stmt_get_result');
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return  boolean  True if connected to the database engine.
	 *
	 * @since   1.0
	 */
	public function connected()
	{
		if (is_object($this->connection))
		{
			return $this->connection->ping();
		}

		return false;
	}

	/**
	 * Drops a table from the database.
	 *
	 * @param   string   $tableName  The name of the database table to drop.
	 * @param   boolean  $ifExists   Optionally specify that the table must exist before it is dropped.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function dropTable($tableName, $ifExists = true)
	{
		$this->connect();

		$this->setQuery('DROP TABLE ' . ($ifExists ? 'IF EXISTS ' : '') . $this->quoteName($tableName))
			->execute();

		return $this;
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return  integer  The number of affected rows.
	 *
	 * @since   1.0
	 */
	public function getAffectedRows()
	{
		$this->connect();

		return $this->connection->affected_rows;
	}

	/**
	 * Return the query string to alter the database character set.
	 *
	 * @param   string  $dbName  The database name
	 *
	 * @return  string  The query that alter the database query string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getAlterDbCharacterSet($dbName)
	{
		$charset = $this->utf8mb4 ? 'utf8mb4' : 'utf8';

		return 'ALTER DATABASE ' . $this->quoteName($dbName) . ' CHARACTER SET `' . $charset . '`';
	}

	/**
	 * Method to get the database collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database (string) or boolean false if not supported.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getCollation()
	{
		$this->connect();

		return $this->setQuery('SELECT @@collation_database;')->loadResult();
	}

	/**
	 * Method to get the database connection collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database connection (string) or boolean false if not supported.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function getConnectionCollation()
	{
		$this->connect();

		return $this->setQuery('SELECT @@collation_connection;')->loadResult();
	}

	/**
	 * Return the query string to create new Database.
	 *
	 * @param   stdClass  $options  Object used to pass user and database name to database driver. This object must have "db_name" and "db_user" set.
	 * @param   boolean   $utf      True if the database supports the UTF-8 character set.
	 *
	 * @return  string  The query that creates database
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getCreateDatabaseQuery($options, $utf)
	{
		if ($utf)
		{
			$charset   = $this->utf8mb4 ? 'utf8mb4' : 'utf8';
			$collation = $charset . '_unicode_ci';

			return 'CREATE DATABASE ' . $this->quoteName($options->db_name) . ' CHARACTER SET `' . $charset . '` COLLATE `' . $collation . '`';
		}

		return 'CREATE DATABASE ' . $this->quoteName($options->db_name);
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource  $cursor  An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 * @since   1.0
	 */
	public function getNumRows($cursor = null)
	{
		return mysqli_num_rows($cursor ?: $this->cursor);
	}

	/**
	 * Shows the table CREATE statement that creates the given tables.
	 *
	 * @param   mixed  $tables  A table name or a list of table names.
	 *
	 * @return  array  A list of the create SQL for the tables.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getTableCreate($tables)
	{
		$this->connect();

		$result = [];

		// Sanitize input to an array and iterate over the list.
		$tables = (array) $tables;

		foreach ($tables as $table)
		{
			// Set the query to get the table CREATE statement.
			$row = $this->setQuery('SHOW CREATE TABLE ' . $this->quoteName($this->escape($table)))->loadRow();

			// Populate the result array based on the create statements.
			$result[$table] = $row[1];
		}

		return $result;
	}

	/**
	 * Retrieves field information about a given table.
	 *
	 * @param   string   $table     The name of the database table.
	 * @param   boolean  $typeOnly  True to only return field types.
	 *
	 * @return  array  An array of fields for the database table.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$this->connect();

		$result = [];

		// Set the query to get the table fields statement.
		$fields = $this->setQuery('SHOW FULL COLUMNS FROM ' . $this->quoteName($this->escape($table)))->loadObjectList();

		// If we only want the type as the value add just that to the list.
		if ($typeOnly)
		{
			foreach ($fields as $field)
			{
				$result[$field->Field] = preg_replace('/[(0-9)]/', '', $field->Type);
			}
		}
		else
		// If we want the whole field data object add that to the list.
		{
			foreach ($fields as $field)
			{
				$result[$field->Field] = $field;
			}
		}

		return $result;
	}

	/**
	 * Get the details list of keys for a table.
	 *
	 * @param   string  $table  The name of the table.
	 *
	 * @return  array  An array of the column specification for the table.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getTableKeys($table)
	{
		$this->connect();

		// Get the details columns information.
		return $this->setQuery('SHOW KEYS FROM ' . $this->quoteName($table))->loadObjectList();
	}

	/**
	 * Method to get an array of all tables in the database.
	 *
	 * @return  array  An array of all the tables in the database.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getTableList()
	{
		$this->connect();

		// Set the query to get the tables statement.
		return $this->setQuery('SHOW TABLES')->loadColumn();
	}

	/**
	 * Get the version of the database connector.
	 *
	 * @return  string  The database connector version.
	 *
	 * @since   1.0
	 */
	public function getVersion()
	{
		$this->connect();

		return $this->connection->server_info;
	}

	/**
	 * Determine whether the database engine support the UTF-8 Multibyte (utf8mb4) character encoding.
	 *
	 * @return  boolean  True if the database engine supports UTF-8 Multibyte.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function hasUTF8mb4Support()
	{
		return $this->utf8mb4;
	}

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return  mixed  The value of the auto-increment field from the last inserted row.
	 *                 If the value is greater than maximal int value, it will return a string.
	 *
	 * @since   1.0
	 */
	public function insertid()
	{
		$this->connect();

		return $this->connection->insert_id;
	}

	/**
	 * Locks a table in the database.
	 *
	 * @param   string  $table  The name of the table to unlock.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function lockTable($table)
	{
		$this->executeUnpreparedQuery($this->replacePrefix('LOCK TABLES ' . $this->quoteName($table) . ' WRITE'));

		return $this;
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function execute()
	{
		$this->connect();

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->replacePrefix((string) $this->sql);

		// Increment the query counter.
		$this->count++;

		// If there is a monitor registered, let it know we are starting this query
		if ($this->monitor)
		{
			$this->monitor->startQuery($sql);
		}

		// Reset the error values.
		$this->errorNum = 0;
		$this->errorMsg = '';

		// Execute the query.
		$this->executed = false;

		if ($this->prepared instanceof \mysqli_stmt)
		{
			// Bind the variables:
			if ($this->sql instanceof PreparableInterface)
			{
				$bounded =& $this->sql->getBounded();

				if (count($bounded))
				{
					$params     = [];
					$typeString = '';

					foreach ($bounded as $key => $obj)
					{
						// Add the type to the type string
						$typeString .= $obj->dataType;

						// And add the value as an additional param
						$params[] = $obj->value;
					}

					// Make everything references for call_user_func_array()
					$bindParams = array();
					$bindParams[] = &$typeString;

					for ($i = 0, $iMax = count($params); $i < $iMax; $i++)
					{
						$bindParams[] = &$params[$i];
					}

					call_user_func_array([$this->prepared, 'bind_param'], $bindParams);
				}
			}

			$this->executed = $this->prepared->execute();
			$this->cursor   = $this->prepared->get_result();

			// If the query was successful and we did not get a cursor, then set this to true (mimics mysql_query() return)
			if ($this->executed && !$this->cursor)
			{
				$this->cursor = true;
			}
		}

		// If there is a monitor registered, let it know we have finished this query
		if ($this->monitor)
		{
			$this->monitor->stopQuery();
		}

		// If an error occurred handle it.
		if (!$this->executed)
		{
			$this->errorNum = (int) $this->connection->errno;
			$this->errorMsg = (string) $this->connection->error;

			// Check if the server was disconnected.
			if (!$this->connected())
			{
				try
				{
					// Attempt to reconnect.
					$this->connection = null;
					$this->connect();
				}
				catch (ConnectionFailureException $e)
				// If connect fails, ignore that exception and throw the normal exception.
				{
					throw new ExecutionFailureException($sql, $this->errorMsg, $this->errorNum);
				}

				// Since we were able to reconnect, run the query again.
				return $this->execute();
			}

			// The server was not disconnected.
			throw new ExecutionFailureException($sql, $this->errorMsg, $this->errorNum);
		}

		return $this->cursor;
	}

	/**
	 * Renames a table in the database.
	 *
	 * @param   string  $oldTable  The name of the table to be renamed
	 * @param   string  $newTable  The new name for the table.
	 * @param   string  $backup    Not used by MySQL.
	 * @param   string  $prefix    Not used by MySQL.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
	{
		$this->setQuery('RENAME TABLE ' . $oldTable . ' TO ' . $newTable)->execute();

		return $this;
	}

	/**
	 * Select a database for use.
	 *
	 * @param   string  $database  The name of the database to select for use.
	 *
	 * @return  boolean  True if the database was successfully selected.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function select($database)
	{
		$this->connect();

		if (!$database)
		{
			return false;
		}

		if (!$this->connection->select_db($database))
		{
			throw new ConnectionFailureException('Could not connect to database.');
		}

		return true;
	}

	/**
	 * Sets the SQL statement string for later execution.
	 *
	 * @param   DatabaseQuery|string  $query   The SQL statement to set either as a DatabaseQuery object or a string.
	 * @param   integer               $offset  The affected row offset to set.
	 * @param   integer               $limit   The maximum affected rows to set.
	 *
	 * @return  $this
	 *
	 * @since   1.5.0
	 */
	public function setQuery($query, $offset = null, $limit = null)
	{
		$this->connect();

		$this->freeResult();

		if (is_string($query))
		{
			// Allows taking advantage of bound variables in a direct query:
			$query = $this->getQuery(true)->setQuery($query);
		}

		if ($query instanceof LimitableInterface && !is_null($offset) && !is_null($limit))
		{
			$query->setLimit($limit, $offset);
		}

		$sql = $this->replacePrefix((string) $query);

		$this->prepared = $this->connection->prepare($sql);

		// Store reference to the DatabaseQuery instance
		return parent::setQuery($query, $offset, $limit);
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function setUtf()
	{
		// If UTF is not supported return false immediately
		if (!$this->utf)
		{
			return false;
		}

		// Make sure we're connected to the server
		$this->connect();

		// Which charset should I use, plain utf8 or multibyte utf8mb4?
		$charset = $this->utf8mb4 && $this->options['utf8mb4'] ? 'utf8mb4' : 'utf8';

		$result = @$this->connection->set_charset($charset);

		/*
		 * If I could not set the utf8mb4 charset then the server doesn't support utf8mb4 despite claiming otherwise. This happens on old MySQL
		 * server versions (less than 5.5.3) using the mysqlnd PHP driver. Since mysqlnd masks the server version and reports only its own we
		 * can not be sure if the server actually does support UTF-8 Multibyte (i.e. it's MySQL 5.5.3 or later). Since the utf8mb4 charset is
		 * undefined in this case we catch the error and determine that utf8mb4 is not supported!
		 */
		if (!$result && $this->utf8mb4 && $this->options['utf8mb4'])
		{
			$this->utf8mb4 = false;
			$result        = @$this->connection->set_charset('utf8');
		}

		return $result;
	}

	/**
	 * Method to commit a transaction.
	 *
	 * @param   boolean  $toSavepoint  If true, commit to the last savepoint.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function transactionCommit($toSavepoint = false)
	{
		if (!$toSavepoint || $this->transactionDepth <= 1)
		{
			$this->connect();

			if ($this->connection->commit())
			{
				$this->transactionDepth = 0;
			}

			return;
		}

		$this->transactionDepth--;
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @param   boolean  $toSavepoint  If true, rollback to the last savepoint.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function transactionRollback($toSavepoint = false)
	{
		if (!$toSavepoint || $this->transactionDepth <= 1)
		{
			$this->connect();

			if ($this->connection->rollback())
			{
				$this->transactionDepth = 0;
			}

			return;
		}

		$savepoint = 'SP_' . ($this->transactionDepth - 1);

		if ($this->executeUnpreparedQuery('ROLLBACK TO SAVEPOINT ' . $this->quoteName($savepoint)))
		{
			$this->transactionDepth--;
		}
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @param   boolean  $asSavepoint  If true and a transaction is already active, a savepoint will be created.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function transactionStart($asSavepoint = false)
	{
		$this->connect();

		if (!$asSavepoint || !$this->transactionDepth)
		{
			if (version_compare($this->getVersion(), '5.6', 'ge'))
			{
				if ($this->connection->begin_transaction())
				{
					$this->transactionDepth = 1;
				}
			}
			else
			{
				if ($this->executeUnpreparedQuery('START TRANSACTION'))
				{
					$this->transactionDepth = 1;
				}
			}

			return;
		}

		$savepoint = 'SP_' . $this->transactionDepth;

		if ($this->connection->savepoint($savepoint))
		{
			$this->transactionDepth++;
		}
	}

	/**
	 * Internal method to execute queries which cannot be run as prepared statements.
	 *
	 * @param   string  $sql  SQL statement to execute.
	 *
	 * @return  boolean
	 *
	 * @since   1.5.0
	 */
	protected function executeUnpreparedQuery($sql)
	{
		$this->connect();

		$cursor = $this->connection->query($sql);

		// If an error occurred handle it.
		if (!$cursor)
		{
			$this->errorNum = (int) $this->connection->errno;
			$this->errorMsg = (string) $this->connection->error;

			// Check if the server was disconnected.
			if (!$this->connected())
			{
				try
				{
					// Attempt to reconnect.
					$this->connection = null;
					$this->connect();
				}
				catch (ConnectionFailureException $e)
				// If connect fails, ignore that exception and throw the normal exception.
				{
					throw new ExecutionFailureException($sql, $this->errorMsg, $this->errorNum);
				}

				// Since we were able to reconnect, run the query again.
				return $this->executeUnpreparedQuery($sql);
			}

			// The server was not disconnected.
			throw new ExecutionFailureException($sql, $this->errorMsg, $this->errorNum);
		}

		$this->freeResult($cursor);

		return true;
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   1.0
	 */
	protected function fetchArray($cursor = null)
	{
		return mysqli_fetch_row($cursor ?: $this->cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   1.0
	 */
	protected function fetchAssoc($cursor = null)
	{
		return mysqli_fetch_assoc($cursor ?: $this->cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed   $cursor  The optional result set cursor from which to fetch the row.
	 * @param   string  $class   The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   1.0
	 */
	protected function fetchObject($cursor = null, $class = '\\stdClass')
	{
		return mysqli_fetch_object($cursor ?: $this->cursor, $class);
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function freeResult($cursor = null)
	{
		$this->executed = false;

		if ($cursor instanceof \mysqli_result)
		{
			$cursor->free_result();
		}

		if ($this->prepared instanceof \mysqli_stmt)
		{
			$this->prepared->close();
			$this->prepared = null;
		}
	}

	/**
	 * Unlocks tables in the database.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function unlockTables()
	{
		$this->executeUnpreparedQuery('UNLOCK TABLES');

		return $this;
	}

	/**
	 * Does the database server claim to have support for UTF-8 Multibyte (utf8mb4) collation?
	 *
	 * libmysql supports utf8mb4 since 5.5.3 (same version as the MySQL server). mysqlnd supports utf8mb4 since 5.0.9.
	 *
	 * @return  boolean
	 *
	 * @since   1.4.0
	 */
	private function serverClaimsUtf8mb4Support()
	{
		$client_version = mysqli_get_client_info();
		$server_version = $this->getVersion();

		if (version_compare($server_version, '5.5.3', '<'))
		{
			return false;
		}

		if (strpos($client_version, 'mysqlnd') !== false)
		{
			$client_version = preg_replace('/^\D+([\d.]+).*/', '$1', $client_version);

			return version_compare($client_version, '5.0.9', '>=');
		}

		return version_compare($client_version, '5.5.3', '>=');
	}
}
