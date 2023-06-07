<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Joomla Framework Database Interface
 *
 * @since  1.0
 */
interface DatabaseInterface
{
    /**
     * Connects to the database if needed.
     *
     * @return  void
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function connect();

    /**
     * Determines if the connection to the server is active.
     *
     * @return  boolean
     *
     * @since   2.0.0
     */
    public function connected();

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
    public function createDatabase($options, $utf = true);

    /**
     * Replace special placeholder representing binary field with the original string.
     *
     * @param   string|resource  $data  Encoded string or resource.
     *
     * @return  string  The original string.
     *
     * @since   1.7.0
     */
    public function decodeBinary($data);

    /**
     * Disconnects the database.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function disconnect();

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
    public function dropTable($table, $ifExists = true);

    /**
     * Escapes a string for usage in an SQL statement.
     *
     * @param   string   $text   The string to be escaped.
     * @param   boolean  $extra  Optional parameter to provide extra escaping.
     *
     * @return  string   The escaped string.
     *
     * @since   2.0.0
     */
    public function escape($text, $extra = false);

    /**
     * Execute the SQL statement.
     *
     * @return  boolean
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function execute();

    /**
     * Get the number of affected rows for the previous executed SQL statement.
     *
     * @return  integer
     *
     * @since   2.0.0
     */
    public function getAffectedRows();

    /**
     * Method to get the database collation in use by sampling a text field of a table in the database.
     *
     * @return  string|boolean  The collation in use by the database or boolean false if not supported.
     *
     * @since   2.0.0
     */
    public function getCollation();

    /**
     * Method that provides access to the underlying database connection.
     *
     * @return  resource  The underlying database connection resource.
     *
     * @since   2.0.0
     */
    public function getConnection();

    /**
     * Method to get the database connection collation, as reported by the driver.
     *
     * If the connector doesn't support reporting this value please return an empty string.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function getConnectionCollation();

    /**
     * Method to get the database encryption details (cipher and protocol) in use.
     *
     * @return  string  The database encryption details.
     *
     * @since   2.0.0
     */
    public function getConnectionEncryption(): string;

    /**
     * Method to test if the database TLS connections encryption are supported.
     *
     * @return  boolean  Whether the database supports TLS connections encryption.
     *
     * @since   2.0.0
     */
    public function isConnectionEncryptionSupported(): bool;

    /**
     * Method to check whether the installed database version is supported by the database driver
     *
     * @return  boolean  True if the database version is supported
     *
     * @since   2.0.0
     */
    public function isMinimumVersion();

    /**
     * Get the total number of SQL statements executed by the database driver.
     *
     * @return  integer
     *
     * @since   2.0.0
     */
    public function getCount();

    /**
     * Returns a PHP date() function compliant date format for the database driver.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function getDateFormat();

    /**
     * Get the minimum supported database version.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function getMinimum();

    /**
     * Get the name of the database driver.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function getName();

    /**
     * Get the null or zero representation of a timestamp for the database driver.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function getNullDate();

    /**
     * Get the common table prefix for the database driver.
     *
     * @return  string  The common database table prefix.
     *
     * @since   3.0
     */
    public function getPrefix();

    /**
     * Get the number of returned rows for the previous executed SQL statement.
     *
     * @return  integer
     *
     * @since   2.0.0
     */
    public function getNumRows();

    /**
     * Get the current query object or a new QueryInterface object.
     *
     * @param   boolean  $new  False to return the current query object, True to return a new QueryInterface object.
     *
     * @return  QueryInterface
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function getQuery($new = false);

    /**
     * Get the server family type.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function getServerType();

    /**
     * Retrieves field information about the given tables.
     *
     * @param   string   $table     The name of the database table.
     * @param   boolean  $typeOnly  True (default) to only return field types.
     *
     * @return  array
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function getTableColumns($table, $typeOnly = true);

    /**
     * Retrieves field information about the given tables.
     *
     * @param   mixed  $tables  A table name or a list of table names.
     *
     * @return  array
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function getTableKeys($tables);

    /**
     * Method to get an array of all tables in the database.
     *
     * @return  array
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function getTableList();

    /**
     * Get the version of the database connector.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function getVersion();

    /**
     * Determine whether or not the database engine supports UTF-8 character encoding.
     *
     * @return  boolean  True if the database engine supports UTF-8 character encoding.
     *
     * @since   2.0.0
     */
    public function hasUtfSupport();

    /**
     * Method to get the auto-incremented value from the last INSERT statement.
     *
     * @return  mixed  The value of the auto-increment field from the last inserted row.
     *
     * @since   2.0.0
     */
    public function insertid();

    /**
     * Inserts a row into a table based on an object's properties.
     *
     * @param   string  $table   The name of the database table to insert into.
     * @param   object  $object  A reference to an object whose public properties match the table fields.
     * @param   string  $key     The name of the primary key. If provided the object property is updated.
     *
     * @return  boolean
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function insertObject($table, &$object, $key = null);

    /**
     * Test to see if the connector is available.
     *
     * @return  boolean
     *
     * @since   1.0
     */
    public static function isSupported();

    /**
     * Method to get the first row of the result set from the database query as an associative array of ['field_name' => 'row_value'].
     *
     * @return  mixed  The return value or null if the query failed.
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function loadAssoc();

    /**
     * Method to get an array of the result set rows from the database query where each row is an associative array
     * of ['field_name' => 'row_value'].  The array of rows can optionally be keyed by a field name, but defaults to
     * a sequential numeric array.
     *
     * NOTE: Choosing to key the result array by a non-unique field name can result in unwanted
     * behavior and should be avoided.
     *
     * @param   string  $key     The name of a field on which to key the result array.
     * @param   string  $column  An optional column name. Instead of the whole row, only this column value will be in the result array.
     *
     * @return  mixed   The return value or null if the query failed.
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function loadAssocList($key = null, $column = null);

    /**
     * Method to get an array of values from the <var>$offset</var> field in each row of the result set from the database query.
     *
     * @param   integer  $offset  The row offset to use to build the result array.
     *
     * @return  mixed  The return value or null if the query failed.
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function loadColumn($offset = 0);

    /**
     * Method to get the first row of the result set from the database query as an object.
     *
     * @param   string  $class  The class name to use for the returned row object.
     *
     * @return  mixed  The return value or null if the query failed.
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function loadObject($class = \stdClass::class);

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
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function loadObjectList($key = '', $class = \stdClass::class);

    /**
     * Method to get the first field of the first row of the result set from the database query.
     *
     * @return  mixed  The return value or null if the query failed.
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function loadResult();

    /**
     * Method to get the first row of the result set from the database query as an array.
     *
     * Columns are indexed numerically so the first column in the result set would be accessible via <var>$row[0]</var>, etc.
     *
     * @return  mixed  The return value or null if the query failed.
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function loadRow();

    /**
     * Method to get an array of the result set rows from the database query where each row is an array.  The array
     * of objects can optionally be keyed by a field offset, but defaults to a sequential numeric array.
     *
     * NOTE: Choosing to key the result array by a non-unique field can result in unwanted behavior and should be avoided.
     *
     * @param   string  $key  The name of a field on which to key the result array.
     *
     * @return  mixed   The return value or null if the query failed.
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function loadRowList($key = null);

    /**
     * Locks a table in the database.
     *
     * @param   string  $tableName  The name of the table to unlock.
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function lockTable($tableName);

    /**
     * Quotes and optionally escapes a string to database requirements for use in database queries.
     *
     * @param   array|string  $text    A string or an array of strings to quote.
     * @param   boolean       $escape  True (default) to escape the string, false to leave it unchanged.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function quote($text, $escape = true);

    /**
     * Quotes a binary string to database requirements for use in database queries.
     *
     * @param   string  $data  A binary string to quote.
     *
     * @return  string  The binary quoted input string.
     *
     * @since   1.7.0
     */
    public function quoteBinary($data);

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
     * @since   2.0.0
     */
    public function quoteName($name, $as = null);

    /**
     * Renames a table in the database.
     *
     * @param   string  $oldTable  The name of the table to be renamed
     * @param   string  $newTable  The new name for the table.
     * @param   string  $backup    Table prefix
     * @param   string  $prefix    For the table - used to rename constraints in non-mysql databases
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null);

    /**
     * This function replaces a string identifier with the configured table prefix.
     *
     * @param   string  $sql     The SQL statement to prepare.
     * @param   string  $prefix  The table prefix.
     *
     * @return  string  The processed SQL statement.
     *
     * @since   2.0.0
     */
    public function replacePrefix($sql, $prefix = '#__');

    /**
     * Select a database for use.
     *
     * @param   string  $database  The name of the database to select for use.
     *
     * @return  boolean
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function select($database);

    /**
     * Sets the SQL statement string for later execution.
     *
     * @param   mixed    $query   The SQL statement to set either as a Query object or a string.
     * @param   integer  $offset  The affected row offset to set. {@deprecated 3.0 Use LimitableInterface::setLimit() instead}
     * @param   integer  $limit   The maximum affected rows to set. {@deprecated 3.0 Use LimitableInterface::setLimit() instead}
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function setQuery($query, $offset = 0, $limit = 0);

    /**
     * Method to commit a transaction.
     *
     * @param   boolean  $toSavepoint  If true, commit to the last savepoint.
     *
     * @return  void
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function transactionCommit($toSavepoint = false);

    /**
     * Method to roll back a transaction.
     *
     * @param   boolean  $toSavepoint  If true, rollback to the last savepoint.
     *
     * @return  void
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function transactionRollback($toSavepoint = false);

    /**
     * Method to initialize a transaction.
     *
     * @param   boolean  $asSavepoint  If true and a transaction is already active, a savepoint will be created.
     *
     * @return  void
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function transactionStart($asSavepoint = false);

    /**
     * Method to truncate a table.
     *
     * @param   string  $table  The table to truncate
     *
     * @return  void
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function truncateTable($table);

    /**
     * Unlocks tables in the database.
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function unlockTables();

    /**
     * Updates a row in a table based on an object's properties.
     *
     * @param   string        $table   The name of the database table to update.
     * @param   object        $object  A reference to an object whose public properties match the table fields.
     * @param   array|string  $key     The name of the primary key.
     * @param   boolean       $nulls   True to update null fields or false to ignore them.
     *
     * @return  boolean
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function updateObject($table, &$object, $key, $nulls = false);
}
