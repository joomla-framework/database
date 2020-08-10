<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

use Joomla\Database\Event\ConnectionEvent;
use Joomla\Database\Exception\ConnectionFailureException;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Exception\PrepareStatementFailureException;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;
use Joomla\Event\EventInterface;

/**
 * Joomla Framework Database Driver Class
 *
 * @since  1.0
 */
abstract class DatabaseDriver implements DatabaseInterface, DispatcherAwareInterface
{
	use DispatcherAwareTrait;

	/**
	 * The name of the database.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $database;

	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $name;

	/**
	 * The type of the database server family supported by this driver.
	 *
	 * @var    string
	 * @since  1.4.0
	 */
	public $serverType;

	/**
	 * The database connection resource.
	 *
	 * @var    resource
	 * @since  1.0
	 */
	protected $connection;

	/**
	 * Holds the list of available db connectors.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected static $connectors = [];

	/**
	 * The number of SQL statements executed by the database driver.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	protected $count = 0;

	/**
	 * The database connection cursor from the last query.
	 *
	 * @var    resource
	 * @since  1.0
	 */
	protected $cursor;

	/**
	 * Contains the current query execution status
	 *
	 * @var    boolean
	 * @since  2.0.0
	 */
	protected $executed = false;

	/**
	 * The affected row limit for the current SQL statement.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	protected $limit = 0;

	/**
	 * The character(s) used to quote SQL statement names such as table names or field names, etc.
	 *
	 * If a single character string the same character is used for both sides of the quoted name, else the first character will be used for the
	 * opening quote and the second for the closing quote.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $nameQuote;

	/**
	 * The null or zero representation of a timestamp for the database driver.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $nullDate;

	/**
	 * The affected row offset to apply for the current SQL statement.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	protected $offset = 0;

	/**
	 * Passed in upon instantiation and saved.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $options;

	/**
	 * The current SQL statement to execute.
	 *
	 * @var    mixed
	 * @since  1.0
	 */
	protected $sql;

	/**
	 * The prepared statement.
	 *
	 * @var    StatementInterface
	 * @since  2.0.0
	 */
	protected $statement;

	/**
	 * The common database table prefix.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $tablePrefix;

	/**
	 * True if the database engine supports UTF-8 character encoding.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $utf = true;

	/**
	 * The database error number.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	protected $errorNum = 0;

	/**
	 * The database error message.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $errorMsg;

	/**
	 * DatabaseDriver instances container.
	 *
	 * @var    DatabaseDriver[]
	 * @since  1.0
	 * @deprecated  3.0  Singleton storage will no longer be supported.
	 */
	protected static $instances = [];

	/**
	 * The minimum supported database version.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $dbMinimum;

	/**
	 * The depth of the current transaction.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	protected $transactionDepth = 0;

	/**
	 * DatabaseFactory object
	 *
	 * @var    DatabaseFactory
	 * @since  2.0.0
	 */
	protected $factory;

	/**
	 * Query monitor object
	 *
	 * @var    QueryMonitorInterface
	 * @since  2.0.0
	 */
	protected $monitor;

	/**
	 * Get a list of available database connectors.
	 *
	 * The list will only be populated with connectors that the class exists for and the environment supports its use.
	 * This gives us the ability to have a multitude of connector classes that are self-aware as to whether or not they
	 * are able to be used on a given system.
	 *
	 * @return  array  An array of available database connectors.
	 *
	 * @since   1.0
	 */
	public static function getConnectors()
	{
		if (empty(self::$connectors))
		{
			// Get an iterator and loop trough the driver classes.
			$dir      = __DIR__;
			$iterator = new \DirectoryIterator($dir);

			/** @var $file \DirectoryIterator */
			foreach ($iterator as $file)
			{
				// Only load for php files.
				if (!$file->isDir())
				{
					continue;
				}

				$baseName = $file->getBasename();

				// Derive the class name from the type.
				/** @var $class DatabaseDriver */
				$class = __NAMESPACE__ . '\\' . ucfirst(strtolower($baseName)) . '\\' . ucfirst(strtolower($baseName)) . 'Driver';

				// If the class doesn't exist, or if it's not supported on this system, move on to the next type.
				if (!class_exists($class) || !$class::isSupported())
				{
					continue;
				}

				// Everything looks good, add it to the list.
				self::$connectors[] = $baseName;
			}
		}

		return self::$connectors;
	}

	/**
	 * Method to return a DatabaseDriver instance based on the given options.
	 *
	 * There are three global options and then the rest are specific to the database driver.
	 *
	 * - The 'driver' option defines which DatabaseDriver class is used for the connection -- the default is 'mysqli'.
	 * - The 'database' option determines which database is to be used for the connection.
	 * - The 'select' option determines whether the connector should automatically select the chosen database.
	 *
	 * Instances are unique to the given options and new objects are only created when a unique options array is
	 * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
	 *
	 * @param   array  $options  Parameters to be passed to the database driver.
	 *
	 * @return  DatabaseDriver
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 * @deprecated  3.0  Use DatabaseFactory::getDriver() instead
	 */
	public static function getInstance(array $options = [])
	{
		trigger_deprecation(
			'joomla/database',
			'2.0.0',
			'%s() is deprecated and will be removed in 3.0, use %s::getDriver() instead.',
			__METHOD__,
			DatabaseFactory::class
		);

		// Sanitize the database connector options.
		$options['driver']   = isset($options['driver']) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $options['driver']) : 'mysqli';
		$options['database'] = $options['database'] ?? null;
		$options['select']   = $options['select'] ?? true;
		$options['factory']  = $options['factory'] ?? new DatabaseFactory;
		$options['monitor']  = $options['monitor'] ?? null;

		// Get the options signature for the database connector.
		$signature = md5(serialize($options));

		// If we already have a database connector instance for these options then just use that.
		if (empty(self::$instances[$signature]))
		{
			// Set the new connector to the global instances based on signature.
			self::$instances[$signature] = $options['factory']->getDriver($options['driver'], $options);
		}

		return self::$instances[$signature];
	}

	/**
	 * Splits a string of multiple queries into an array of individual queries.
	 *
	 * @param   string  $sql  Input SQL string with which to split into individual queries.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public static function splitSql($sql)
	{
		$start     = 0;
		$open      = false;
		$comment   = false;
		$endString = '';
		$end       = \strlen($sql);
		$queries   = [];
		$query     = '';

		for ($i = 0; $i < $end; $i++)
		{
			$current      = substr($sql, $i, 1);
			$current2     = substr($sql, $i, 2);
			$current3     = substr($sql, $i, 3);
			$lenEndString = \strlen($endString);
			$testEnd      = substr($sql, $i, $lenEndString);

			if ($current === '"' || $current === "'" || $current2 === '--'
				|| ($current2 === '/*' && $current3 !== '/*!' && $current3 !== '/*+')
				|| ($current === '#' && $current3 !== '#__')
				|| ($comment && $testEnd === $endString))
			{
				// Check if quoted with previous backslash
				$n = 2;

				while (substr($sql, $i - $n + 1, 1) === '\\' && $n < $i)
				{
					$n++;
				}

				// Not quoted
				if ($n % 2 === 0)
				{
					if ($open)
					{
						if ($testEnd === $endString)
						{
							if ($comment)
							{
								$comment = false;

								if ($lenEndString > 1)
								{
									$i += ($lenEndString - 1);
									$current = substr($sql, $i, 1);
								}

								$start = $i + 1;
							}

							$open      = false;
							$endString = '';
						}
					}
					else
					{
						$open = true;

						if ($current2 === '--')
						{
							$endString = "\n";
							$comment   = true;
						}
						elseif ($current2 === '/*')
						{
							$endString = '*/';
							$comment   = true;
						}
						elseif ($current === '#')
						{
							$endString = "\n";
							$comment   = true;
						}
						else
						{
							$endString = $current;
						}

						if ($comment && $start < $i)
						{
							$query .= substr($sql, $start, $i - $start);
						}
					}
				}
			}

			if ($comment)
			{
				$start = $i + 1;
			}

			if (($current === ';' && !$open) || $i === $end - 1)
			{
				if ($start <= $i)
				{
					$query .= substr($sql, $start, $i - $start + 1);
				}

				$query = trim($query);

				if ($query)
				{
					if (($i === $end - 1) && ($current !== ';'))
					{
						$query .= ';';
					}

					$queries[] = $query;
				}

				$query = '';
				$start = $i + 1;
			}

			$endComment = false;
		}

		return $queries;
	}

	/**
	 * Magic method to access properties of the database driver.
	 *
	 * @param   string  $name  The name of the property.
	 *
	 * @return  mixed   A value if the property name is valid, null otherwise.
	 *
	 * @since       1.4.0
	 * @deprecated  3.0  This is a B/C proxy since $this->name was previously public
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'name':
				trigger_deprecation(
					'joomla/database',
					'1.4.0',
					'Accessing the name property of %s is deprecated, use the getName() method instead.',
					self::class
				);

				return $this->getName();

			default:
				$trace = debug_backtrace();
				trigger_error(
					sprintf(
						'Undefined property via __get(): %1$s in %2$s on line %3$s',
						$name,
						$trace[0]['file'],
						$trace[0]['line']
					),
					\E_USER_NOTICE
				);
		}
	}

	/**
	 * Constructor.
	 *
	 * @param   array  $options  List of options used to configure the connection
	 *
	 * @since   1.0
	 */
	public function __construct(array $options)
	{
		// Initialise object variables.
		$this->database    = $options['database'] ?? '';
		$this->tablePrefix = $options['prefix'] ?? '';
		$this->count       = 0;
		$this->errorNum    = 0;

		// Set class options.
		$this->options = $options;

		// Register the DatabaseFactory
		$this->factory = $options['factory'] ?? new DatabaseFactory;

		// Register the query monitor if available
		$this->monitor = $options['monitor'] ?? null;
	}

	/**
	 * Destructor.
	 *
	 * @since   2.0.0
	 */
	public function __destruct()
	{
		$this->disconnect();
	}

	/**
	 * Alter database's character set.
	 *
	 * @param   string  $dbName  The database name that will be altered
	 *
	 * @return  boolean|resource
	 *
	 * @since   2.0.0
	 * @throws  \RuntimeException
	 */
	public function alterDbCharacterSet($dbName)
	{
		if ($dbName === null)
		{
			throw new \RuntimeException('Database name must not be null.');
		}

		$this->setQuery($this->getAlterDbCharacterSet($dbName));

		return $this->execute();
	}

	/**
	 * Create a new database using information from $options object.
	 *
	 * @param   \stdClass  $options  Object used to pass user and database name to database driver. This object must have "db_name" and "db_user" set.
	 * @param   boolean    $utf      True if the database supports the UTF-8 character set.
	 *
	 * @return  boolean|resource
	 *
	 * @since   2.0.0
	 * @throws  \RuntimeException
	 */
	public function createDatabase($options, $utf = true)
	{
		if ($options === null)
		{
			throw new \RuntimeException('$options object must not be null.');
		}

		if (empty($options->db_name))
		{
			throw new \RuntimeException('$options object must have db_name set.');
		}

		if (empty($options->db_user))
		{
			throw new \RuntimeException('$options object must have db_user set.');
		}

		$this->setQuery($this->getCreateDatabaseQuery($options, $utf));

		return $this->execute();
	}

	/**
	 * Disconnects the database.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function disconnect()
	{
		$this->freeResult();
		$this->connection = null;

		$this->dispatchEvent(new ConnectionEvent(DatabaseEvents::POST_DISCONNECT, $this));
	}

	/**
	 * Dispatch an event.
	 *
	 * @param   EventInterface  $event  The event to dispatch
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	protected function dispatchEvent(EventInterface $event)
	{
		try
		{
			$this->getDispatcher()->dispatch($event->getName(), $event);
		}
		catch (\UnexpectedValueException $exception)
		{
			// Don't error if a dispatcher hasn't been set
		}
	}

	/**
	 * Drops a table from the database.
	 *
	 * @param   string   $table     The name of the database table to drop.
	 * @param   boolean  $ifExists  Optionally specify that the table must exist before it is dropped.
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 * @throws  \RuntimeException
	 */
	public function dropTable($table, $ifExists = true)
	{
		$this->connect();

		$this->setQuery('DROP TABLE ' . ($ifExists ? 'IF EXISTS ' : '') . $this->quoteName($table))
			->execute();

		return $this;
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  boolean
	 *
	 * @since   2.0.0
	 * @throws  \RuntimeException
	 */
	public function execute()
	{
		$this->connect();

		// Increment the query counter.
		$this->count++;

		// Get list of bound parameters
		$bounded =& $this->sql->getBounded();

		// If there is a monitor registered, let it know we are starting this query
		if ($this->monitor)
		{
			// Take a local copy so that we don't modify the original query and cause issues later
			$sql = $this->replacePrefix((string) $this->sql);

			$this->monitor->startQuery($sql, $bounded);
		}

		// Execute the query.
		$this->executed = false;

		// Bind the variables
		foreach ($bounded as $key => $obj)
		{
			$this->statement->bindParam($key, $obj->value, $obj->dataType);
		}

		try
		{
			$this->executed = $this->statement->execute();

			// If there is a monitor registered, let it know we have finished this query
			if ($this->monitor)
			{
				$this->monitor->stopQuery();
			}

			return true;
		}
		catch (ExecutionFailureException $exception)
		{
			// If there is a monitor registered, let it know we have finished this query
			if ($this->monitor)
			{
				$this->monitor->stopQuery();
			}

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
				{
					// If connect fails, ignore that exception and throw the normal exception.
					throw $exception;
				}

				// Since we were able to reconnect, run the query again.
				return $this->execute();
			}

			// Throw the normal query exception.
			throw $exception;
		}
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   1.0
	 */
	protected function fetchArray()
	{
		if ($this->statement)
		{
			return $this->statement->fetch(FetchMode::NUMERIC);
		}
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   1.0
	 */
	protected function fetchAssoc()
	{
		if ($this->statement)
		{
			return $this->statement->fetch(FetchMode::ASSOCIATIVE);
		}
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * Note, the fetch mode should be configured before calling this method using `StatementInterface::setFetchMode()`.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   1.0
	 */
	protected function fetchObject()
	{
		if ($this->statement)
		{
			return $this->statement->fetch();
		}
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function freeResult()
	{
		$this->executed = false;

		if ($this->statement)
		{
			$this->statement->closeCursor();
		}
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return  integer  The number of affected rows in the previous operation
	 *
	 * @since   2.0.0
	 */
	public function getAffectedRows()
	{
		$this->connect();

		if ($this->statement)
		{
			return $this->statement->rowCount();
		}

		return 0;
	}

	/**
	 * Method that provides access to the underlying database connection.
	 *
	 * @return  resource  The underlying database connection resource.
	 *
	 * @since   1.0
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Get the total number of SQL statements executed by the database driver.
	 *
	 * @return  integer
	 *
	 * @since   1.0
	 */
	public function getCount()
	{
		return $this->count;
	}

	/**
	 * Return the query string to alter the database character set.
	 *
	 * @param   string  $dbName  The database name
	 *
	 * @return  string  The query that alter the database query string
	 *
	 * @since   1.6.0
	 */
	protected function getAlterDbCharacterSet($dbName)
	{
		return 'ALTER DATABASE ' . $this->quoteName($dbName) . ' CHARACTER SET ' . $this->quote('UTF8');
	}

	/**
	 * Return the query string to create new Database.
	 *
	 * @param   stdClass  $options  Object used to pass user and database name to database driver. This object must have "db_name" and "db_user" set.
	 * @param   boolean   $utf      True if the database supports the UTF-8 character set.
	 *
	 * @return  string  The query that creates database
	 *
	 * @since   2.0.0
	 */
	protected function getCreateDatabaseQuery($options, $utf)
	{
		return 'CREATE DATABASE ' . $this->quoteName($options->db_name);
	}

	/**
	 * Gets the name of the database used by this conneciton.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	protected function getDatabase()
	{
		return $this->database;
	}

	/**
	 * Returns a PHP date() function compliant date format for the database driver.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getDateFormat()
	{
		return 'Y-m-d H:i:s';
	}

	/**
	 * Get the minimum supported database version.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getMinimum()
	{
		return static::$dbMinimum;
	}

	/**
	 * Get the name of the database driver.
	 *
	 * If $this->name is not set it will try guessing the driver name from the class name.
	 *
	 * @return  string
	 *
	 * @since   1.4.0
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			$reflect = new \ReflectionClass($this);

			$this->name = strtolower(str_replace('Driver', '', $reflect->getShortName()));
		}

		return $this->name;
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 * @since   2.0.0
	 */
	public function getNumRows()
	{
		$this->connect();

		if ($this->statement)
		{
			return $this->statement->rowCount();
		}

		return 0;
	}

	/**
	 * Get the server family type.
	 *
	 * If $this->serverType is not set it will attempt guessing the server family type from the driver name. If this is not possible the driver
	 * name will be returned instead.
	 *
	 * @return  string
	 *
	 * @since   1.4.0
	 */
	public function getServerType()
	{
		if (empty($this->serverType))
		{
			$name = $this->getName();

			if (stristr($name, 'mysql') !== false)
			{
				$this->serverType = 'mysql';
			}
			elseif (stristr($name, 'postgre') !== false)
			{
				$this->serverType = 'postgresql';
			}
			elseif (stristr($name, 'pgsql') !== false)
			{
				$this->serverType = 'postgresql';
			}
			elseif (stristr($name, 'oracle') !== false)
			{
				$this->serverType = 'oracle';
			}
			elseif (stristr($name, 'sqlite') !== false)
			{
				$this->serverType = 'sqlite';
			}
			elseif (stristr($name, 'sqlsrv') !== false)
			{
				$this->serverType = 'mssql';
			}
			elseif (stristr($name, 'sqlazure') !== false)
			{
				$this->serverType = 'mssql';
			}
			elseif (stristr($name, 'mssql') !== false)
			{
				$this->serverType = 'mssql';
			}
			else
			{
				$this->serverType = $name;
			}
		}

		return $this->serverType;
	}

	/**
	 * Get the null or zero representation of a timestamp for the database driver.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getNullDate()
	{
		return $this->nullDate;
	}

	/**
	 * Get the common table prefix for the database driver.
	 *
	 * @return  string  The common database table prefix.
	 *
	 * @since   1.0
	 */
	public function getPrefix()
	{
		return $this->tablePrefix;
	}

	/**
	 * Gets an exporter class object.
	 *
	 * @return  DatabaseExporter  An exporter object.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getExporter()
	{
		return $this->factory->getExporter($this->name, $this);
	}

	/**
	 * Gets an importer class object.
	 *
	 * @return  DatabaseImporter
	 *
	 * @since   1.0
	 */
	public function getImporter()
	{
		return $this->factory->getImporter($this->name, $this);
	}

	/**
	 * Get the current query object or a new DatabaseQuery object.
	 *
	 * @param   boolean  $new  False to return the current query object, True to return a new DatabaseQuery object.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   1.0
	 */
	public function getQuery($new = false)
	{
		if ($new)
		{
			return $this->factory->getQuery($this->name, $this);
		}

		return $this->sql;
	}

	/**
	 * Get a new iterator on the current query.
	 *
	 * @param   string  $column  An option column to use as the iterator key.
	 * @param   string  $class   The class of object that is returned.
	 *
	 * @return  DatabaseIterator
	 *
	 * @since   1.0
	 */
	public function getIterator($column = null, $class = \stdClass::class)
	{
		if (!$this->executed)
		{
			$this->execute();
		}

		return $this->factory->getIterator($this->name, $this->statement, $column, $class);
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
	abstract public function getTableCreate($tables);

	/**
	 * Determine whether or not the database engine supports UTF-8 character encoding.
	 *
	 * @return  boolean  True if the database engine supports UTF-8 character encoding.
	 *
	 * @since   1.0
	 */
	public function hasUtfSupport()
	{
		return $this->utf;
	}

	/**
	 * Inserts a row into a table based on an object's properties.
	 *
	 * @param   string  $table   The name of the database table to insert into.
	 * @param   object  $object  A reference to an object whose public properties match the table fields.
	 * @param   string  $key     The name of the primary key. If provided the object property is updated.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function insertObject($table, &$object, $key = null)
	{
		$fields       = [];
		$values       = [];
		$tableColumns = $this->getTableColumns($table);

		// Iterate over the object variables to build the query fields and values.
		foreach (get_object_vars($object) as $k => $v)
		{
			// Skip columns that don't exist in the table.
			if (!\array_key_exists($k, $tableColumns))
			{
				continue;
			}

			// Only process non-null scalars.
			if (\is_array($v) || \is_object($v) || $v === null)
			{
				continue;
			}

			// Ignore any internal fields.
			if ($k[0] === '_')
			{
				continue;
			}

			// Prepare and sanitize the fields and values for the database query.
			$fields[] = $this->quoteName($k);
			$values[] = $this->quote($v);
		}

		// Create the base insert statement.
		$query = $this->getQuery(true)
			->insert($this->quoteName($table))
			->columns($fields)
			->values(implode(',', $values));

		// Set the query and execute the insert.
		$this->setQuery($query)->execute();

		// Update the primary key if it exists.
		$id = $this->insertid();

		if ($key && $id && \is_string($key))
		{
			$object->$key = $id;
		}

		return true;
	}

	/**
	 * Method to check whether the installed database version is supported by the database driver
	 *
	 * @return  boolean  True if the database version is supported
	 *
	 * @since   1.0
	 */
	public function isMinimumVersion()
	{
		return version_compare($this->getVersion(), $this->getMinimum()) >= 0;
	}

	/**
	 * Method to get the first row of the result set from the database query as an associative array of ['field_name' => 'row_value'].
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadAssoc()
	{
		$this->connect();

		$ret = null;

		// Execute the query and get the result set cursor.
		$this->execute();

		// Get the first row from the result set as an associative array.
		$array = $this->fetchAssoc();

		if ($array)
		{
			$ret = $array;
		}

		// Free up system resources and return.
		$this->freeResult();

		return $ret;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an associative array
	 * of ['field_name' => 'row_value'].  The array of rows can optionally be keyed by a field name, but defaults to
	 * a sequential numeric array.
	 *
	 * NOTE: Chosing to key the result array by a non-unique field name can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param   string  $key     The name of a field on which to key the result array.
	 * @param   string  $column  An optional column name. Instead of the whole row, only this column value will be in
	 *                           the result array.
	 *
	 * @return  mixed   The return value or null if the query failed.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadAssocList($key = null, $column = null)
	{
		$this->connect();

		$array = [];

		// Execute the query and get the result set cursor.
		$this->execute();

		// Get all of the rows from the result set.
		while ($row = $this->fetchAssoc())
		{
			$value = $column ? ($row[$column] ?? $row) : $row;

			if ($key)
			{
				$array[$row[$key]] = $value;
			}
			else
			{
				$array[] = $value;
			}
		}

		// Free up system resources and return.
		$this->freeResult();

		return $array;
	}

	/**
	 * Method to get an array of values from the <var>$offset</var> field in each row of the result set from the database query.
	 *
	 * @param   integer  $offset  The row offset to use to build the result array.
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadColumn($offset = 0)
	{
		$this->connect();

		$array = [];

		// Execute the query and get the result set cursor.
		$this->execute();

		// Get all of the rows from the result set as arrays.
		while ($row = $this->fetchArray())
		{
			$array[] = $row[$offset];
		}

		// Free up system resources and return.
		$this->freeResult();

		return $array;
	}

	/**
	 * Method to get the first row of the result set from the database query as an object.
	 *
	 * @param   string  $class  The class name to use for the returned row object.
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadObject($class = \stdClass::class)
	{
		$this->connect();

		$ret = null;

		if ($this->statement)
		{
			$fetchMode = $class === \stdClass::class ? FetchMode::STANDARD_OBJECT : FetchMode::CUSTOM_OBJECT;

			// PDO doesn't allow extra arguments for \PDO::FETCH_CLASS, so only forward the class for the custom object mode
			if ($fetchMode === FetchMode::STANDARD_OBJECT)
			{
				$this->statement->setFetchMode($fetchMode);
			}
			else
			{
				$this->statement->setFetchMode($fetchMode, $class);
			}
		}

		// Execute the query and get the result set cursor.
		$this->execute();

		// Get the first row from the result set as an object of type $class.
		$object = $this->fetchObject();

		if ($object)
		{
			$ret = $object;
		}

		// Free up system resources and return.
		$this->freeResult();

		return $ret;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an object.  The array
	 * of objects can optionally be keyed by a field name, but defaults to a sequential numeric array.
	 *
	 * NOTE: Choosing to key the result array by a non-unique field name can result in unwanted behavior and should be avoided.
	 *
	 * @param   string  $key    The name of a field on which to key the result array.
	 * @param   string  $class  The class name to use for the returned row objects.
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadObjectList($key = '', $class = \stdClass::class)
	{
		$this->connect();

		$array = [];

		if ($this->statement)
		{
			$fetchMode = $class === \stdClass::class ? FetchMode::STANDARD_OBJECT : FetchMode::CUSTOM_OBJECT;

			// PDO doesn't allow extra arguments for \PDO::FETCH_CLASS, so only forward the class for the custom object mode
			if ($fetchMode === FetchMode::STANDARD_OBJECT)
			{
				$this->statement->setFetchMode($fetchMode);
			}
			else
			{
				$this->statement->setFetchMode($fetchMode, $class);
			}
		}

		// Execute the query and get the result set cursor.
		$this->execute();

		// Get all of the rows from the result set as objects of type $class.
		while ($row = $this->fetchObject())
		{
			if ($key)
			{
				$array[$row->$key] = $row;
			}
			else
			{
				$array[] = $row;
			}
		}

		// Free up system resources and return.
		$this->freeResult();

		return $array;
	}

	/**
	 * Method to get the first field of the first row of the result set from the database query.
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadResult()
	{
		$this->connect();

		$ret = null;

		// Execute the query and get the result set cursor.
		$this->execute();

		// Get the first row from the result set as an array.
		$row = $this->fetchArray();

		if ($row)
		{
			$ret = $row[0];
		}

		// Free up system resources and return.
		$this->freeResult();

		return $ret;
	}

	/**
	 * Method to get the first row of the result set from the database query as an array.
	 *
	 * Columns are indexed numerically so the first column in the result set would be accessible via <var>$row[0]</var>, etc.
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadRow()
	{
		$this->connect();

		$ret = null;

		// Execute the query and get the result set cursor.
		$this->execute();

		// Get the first row from the result set as an array.
		$row = $this->fetchArray();

		if ($row)
		{
			$ret = $row;
		}

		// Free up system resources and return.
		$this->freeResult();

		return $ret;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an array.  The array
	 * of objects can optionally be keyed by a field offset, but defaults to a sequential numeric array.
	 *
	 * NOTE: Choosing to key the result array by a non-unique field can result in unwanted behavior and should be avoided.
	 *
	 * @param   string  $key  The name of a field on which to key the result array.
	 *
	 * @return  array   An array of results.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadRowList($key = null)
	{
		$this->connect();

		$array = [];

		// Execute the query and get the result set cursor.
		$this->execute();

		// Get all of the rows from the result set as arrays.
		while ($row = $this->fetchArray())
		{
			if ($key !== null)
			{
				$array[$row[$key]] = $row;
			}
			else
			{
				$array[] = $row;
			}
		}

		// Free up system resources and return.
		$this->freeResult();

		return $array;
	}

	/**
	 * Prepares a SQL statement for execution
	 *
	 * @param   string  $query  The SQL query to be prepared.
	 *
	 * @return  StatementInterface
	 *
	 * @since   2.0.0
	 * @throws  PrepareStatementFailureException
	 */
	abstract protected function prepareStatement(string $query): StatementInterface;

	/**
	 * Alias for quote method
	 *
	 * @param   array|string  $text    A string or an array of strings to quote.
	 * @param   boolean       $escape  True (default) to escape the string, false to leave it unchanged.
	 *
	 * @return  string  The quoted input string.
	 *
	 * @since   1.0
	 */
	public function q($text, $escape = true)
	{
		return $this->quote($text, $escape);
	}

	/**
	 * Quotes and optionally escapes a string to database requirements for use in database queries.
	 *
	 * @param   array|string  $text    A string or an array of strings to quote.
	 * @param   boolean       $escape  True (default) to escape the string, false to leave it unchanged.
	 *
	 * @return  string  The quoted input string.
	 *
	 * @since   1.0
	 */
	public function quote($text, $escape = true)
	{
		if (\is_array($text))
		{
			foreach ($text as $k => $v)
			{
				$text[$k] = $this->quote($v, $escape);
			}

			return $text;
		}

		return '\'' . ($escape ? $this->escape($text) : $text) . '\'';
	}

	/**
	 * Quotes a binary string to database requirements for use in database queries.
	 *
	 * @param   string  $data  A binary string to quote.
	 *
	 * @return  string  The binary quoted input string.
	 *
	 * @since   1.7.0
	 */
	public function quoteBinary($data)
	{
		// SQL standard syntax for hexadecimal literals
		return "X'" . bin2hex($data) . "'";
	}

	/**
	 * Replace special placeholder representing binary field with the original string.
	 *
	 * @param   string|resource  $data  Encoded string or resource.
	 *
	 * @return  string  The original string.
	 *
	 * @since   1.7.0
	 */
	public function decodeBinary($data)
	{
		return $data;
	}

	/**
	 * Alias for quoteName method
	 *
	 * @param   array|string  $name  The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
	 *                               Each type supports dot-notation name.
	 * @param   array|string  $as    The AS query part associated to $name. It can be string or array, in latter case it has to be
	 *                               same length of $name; if is null there will not be any AS part for string or array element.
	 *
	 * @return  array|string  The quote wrapped name, same type of $name.
	 *
	 * @since   1.0
	 */
	public function qn($name, $as = null)
	{
		return $this->quoteName($name, $as);
	}

	/**
	 * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
	 * risks and reserved word conflicts.
	 *
	 * @param   array|string  $name  The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
	 *                               Each type supports dot-notation name.
	 * @param   array|string  $as    The AS query part associated to $name. It can be string or array, in latter case it has to be
	 *                               same length of $name; if is null there will not be any AS part for string or array element.
	 *
	 * @return  array|string  The quote wrapped name, same type of $name.
	 *
	 * @since   1.0
	 */
	public function quoteName($name, $as = null)
	{
		if (\is_string($name))
		{
			$name = $this->quoteNameString($name);

			if ($as !== null)
			{
				$name .= ' AS ' . $this->quoteNameString($as, true);
			}

			return $name;
		}

		$fin = [];

		if ($as === null)
		{
			foreach ($name as $str)
			{
				$fin[] = $this->quoteName($str);
			}
		}
		elseif (\is_array($name) && (\count($name) === \count($as)))
		{
			$count = \count($name);

			for ($i = 0; $i < $count; $i++)
			{
				$fin[] = $this->quoteName($name[$i], $as[$i]);
			}
		}

		return $fin;
	}

	/**
	 * Quote string coming from quoteName call.
	 *
	 * @param   string   $name          Identifier name to be quoted.
	 * @param   boolean  $asSinglePart  Treat the name as a single part of the identifier.
	 *
	 * @return  string  Quoted identifier string.
	 *
	 * @since   1.7.0
	 */
	protected function quoteNameString($name, $asSinglePart = false)
	{
		$q = $this->nameQuote . $this->nameQuote;

		// Double quote reserved keyword
		$name = str_replace($q[1], $q[1] . $q[1], $name);

		if ($asSinglePart)
		{
			return $q[0] . $name . $q[1];
		}

		return $q[0] . str_replace('.', "$q[1].$q[0]", $name) . $q[1];
	}

	/**
	 * Quote strings coming from quoteName call.
	 *
	 * @param   array  $strArr  Array of strings coming from quoteName dot-explosion.
	 *
	 * @return  string  Dot-imploded string of quoted parts.
	 *
	 * @since   1.0
	 * @deprecated  2.0  Use quoteNameString instead
	 */
	protected function quoteNameStr($strArr)
	{
		$parts = [];
		$q     = $this->nameQuote;

		foreach ($strArr as $part)
		{
			if ($part === null)
			{
				continue;
			}

			if (\strlen($q) === 1)
			{
				$parts[] = $q . $part . $q;
			}
			else
			{
				$parts[] = $q[0] . $part . $q[1];
			}
		}

		return implode('.', $parts);
	}

	/**
	 * This function replaces a string identifier with the configured table prefix.
	 *
	 * @param   string  $sql     The SQL statement to prepare.
	 * @param   string  $prefix  The table prefix.
	 *
	 * @return  string  The processed SQL statement.
	 *
	 * @since   1.0
	 */
	public function replacePrefix($sql, $prefix = '#__')
	{
		$escaped   = false;
		$startPos  = 0;
		$quoteChar = '';
		$literal   = '';

		$sql = trim($sql);
		$n   = \strlen($sql);

		while ($startPos < $n)
		{
			$ip = strpos($sql, $prefix, $startPos);

			if ($ip === false)
			{
				break;
			}

			$j = strpos($sql, "'", $startPos);
			$k = strpos($sql, '"', $startPos);

			if (($k !== false) && (($k < $j) || ($j === false)))
			{
				$quoteChar = '"';
				$j         = $k;
			}
			else
			{
				$quoteChar = "'";
			}

			if ($j === false)
			{
				$j = $n;
			}

			$literal .= str_replace($prefix, $this->tablePrefix, substr($sql, $startPos, $j - $startPos));
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n)
			{
				break;
			}

			// Quote comes first, find end of quote
			while (true)
			{
				$k       = strpos($sql, $quoteChar, $j);
				$escaped = false;

				if ($k === false)
				{
					break;
				}

				$l = $k - 1;

				while ($l >= 0 && $sql[$l] === '\\')
				{
					$l--;
					$escaped = !$escaped;
				}

				if ($escaped)
				{
					$j = $k + 1;

					continue;
				}

				break;
			}

			if ($k === false)
			{
				// Error in the query - no end quote; ignore it
				break;
			}

			$literal .= substr($sql, $startPos, $k - $startPos + 1);
			$startPos = $k + 1;
		}

		if ($startPos < $n)
		{
			$literal .= substr($sql, $startPos, $n - $startPos);
		}

		return $literal;
	}

	/**
	 * Get the query monitor.
	 *
	 * @return  QueryMonitorInterface|null  The query monitor or null if not set.
	 *
	 * @since   2.0.0
	 */
	public function getMonitor()
	{
		return $this->monitor;
	}

	/**
	 * Set a query monitor.
	 *
	 * @param   QueryMonitorInterface|null  $monitor  The query monitor.
	 *
	 * @return  $this
	 *
	 * @since   2.0.0
	 */
	public function setMonitor(QueryMonitorInterface $monitor = null)
	{
		$this->monitor = $monitor;

		return $this;
	}

	/**
	 * Sets the SQL statement string for later execution.
	 *
	 * @param   string|QueryInterface  $query   The SQL statement to set either as a Query object or a string.
	 * @param   integer                $offset  The affected row offset to set. {@deprecated 3.0 Use LimitableInterface::setLimit() instead}
	 * @param   integer                $limit   The maximum affected rows to set. {@deprecated 3.0 Use LimitableInterface::setLimit() instead}
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public function setQuery($query, $offset = 0, $limit = 0)
	{
		$this->connect();

		$this->freeResult();

		if (\is_string($query))
		{
			// Allows taking advantage of bound variables in a direct query:
			$query = $this->getQuery(true)->setQuery($query);
		}
		elseif (!($query instanceof QueryInterface))
		{
			throw new \InvalidArgumentException(
				sprintf(
					'A query must be a string or a %s instance, a %s was given.',
					QueryInterface::class,
					\gettype($query) === 'object' ? (\get_class($query) . ' instance') : \gettype($query)
				)
			);
		}

		if ($offset > 0 || $limit > 0)
		{
			trigger_deprecation(
				'joomla/database',
				'2.0.0',
				'The "$offset" and "$limit" arguments of %s() are deprecated and will be removed in 3.0, use %s::setLimit() instead.',
				__METHOD__,
				QueryInterface::class
			);
		}

		// Check for values set on the query object and use those if there is a zero value passed here
		if ($limit === 0 && $query->limit > 0)
		{
			$limit = $query->limit;
		}

		if ($offset === 0 && $query->offset > 0)
		{
			$offset = $query->offset;
		}

		$query->setLimit($limit, $offset);

		$sql = $this->replacePrefix((string) $query);

		$this->statement = $this->prepareStatement($sql);

		$this->sql    = $query;
		$this->limit  = (int) max(0, $limit);
		$this->offset = (int) max(0, $offset);

		return $this;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	abstract public function setUtf();

	/**
	 * Method to truncate a table.
	 *
	 * @param   string  $table  The table to truncate
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function truncateTable($table)
	{
		$this->setQuery('TRUNCATE TABLE ' . $this->quoteName($table))
			->execute();
	}

	/**
	 * Updates a row in a table based on an object's properties.
	 *
	 * @param   string        $table   The name of the database table to update.
	 * @param   object        $object  A reference to an object whose public properties match the table fields.
	 * @param   array|string  $key     The name of the primary key.
	 * @param   boolean       $nulls   True to update null fields or false to ignore them.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function updateObject($table, &$object, $key, $nulls = false)
	{
		$fields       = [];
		$where        = [];
		$tableColumns = $this->getTableColumns($table);

		if (\is_string($key))
		{
			$key = [$key];
		}

		if (\is_object($key))
		{
			$key = (array) $key;
		}

		// Create the base update statement.
		$statement = 'UPDATE ' . $this->quoteName($table) . ' SET %s WHERE %s';

		// Iterate over the object variables to build the query fields/value pairs.
		foreach (get_object_vars($object) as $k => $v)
		{
			// Skip columns that don't exist in the table.
			if (!\array_key_exists($k, $tableColumns))
			{
				continue;
			}

			// Only process scalars that are not internal fields.
			if (\is_array($v) || \is_object($v) || $k[0] === '_')
			{
				continue;
			}

			// Set the primary key to the WHERE clause instead of a field to update.
			if (\in_array($k, $key, true))
			{
				$where[] = $this->quoteName($k) . ($v === null ? ' IS NULL' : ' = ' . $this->quote($v));

				continue;
			}

			// Prepare and sanitize the fields and values for the database query.
			if ($v === null)
			{
				// If the value is null and we want to update nulls then set it.
				if ($nulls)
				{
					$val = 'NULL';
				}
				else
				{
					// If the value is null and we do not want to update nulls then ignore this field.
					continue;
				}
			}
			else
			{
				// The field is not null so we prep it for update.
				$val = $this->quote($v);
			}

			// Add the field to be updated.
			$fields[] = $this->quoteName($k) . '=' . $val;
		}

		// We don't have any fields to update.
		if (empty($fields))
		{
			return true;
		}

		// Set the query and execute the update.
		$this->setQuery(sprintf($statement, implode(',', $fields), implode(' AND ', $where)))->execute();

		return true;
	}
}
