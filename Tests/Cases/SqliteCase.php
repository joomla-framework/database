<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Cases;

use Joomla\Database\Sqlite\SqliteDriver;
use Joomla\Test\TestDatabase;
use Joomla\Database\DatabaseDriver;

/**
 * Abstract test case class for SQLite database testing.
 *
 * @since  1.0
 */
abstract class SqliteCase extends TestDatabase
{
	/**
	 * The database driver options for the connection.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private static $options = array('driver' => 'sqlite', 'database' => ':memory:');

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * An example DSN would be: host=localhost;port=5432;dbname=joomla_ut;user=utuser;pass=ut1234
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function setUpBeforeClass()
	{
		// Make sure the driver is supported
		if (!SqliteDriver::isSupported())
		{
			static::markTestSkipped('The SQLite driver is not supported on this platform.');
		}

		try
		{
			// Attempt to instantiate the driver.
			static::$driver = DatabaseDriver::getInstance(self::$options);

			// Get the PDO instance for an SQLite memory database and load the test schema into it.
			static::$driver->connect();
			static::$driver->getConnection()->exec(file_get_contents(dirname(__DIR__) . '/Stubs/ddl.sql'));
		}
		catch (\RuntimeException $e)
		{
			static::$driver = null;
		}

		// If for some reason an exception object was returned set our database object to null.
		if (static::$driver instanceof \Exception)
		{
			static::$driver = null;
		}
	}

	/**
	 * This method is called after the last test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function tearDownAfterClass()
	{
		if (static::$driver !== null)
		{
			static::$driver->disconnect();
			static::$driver = null;
		}
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_XmlDataSet
	 *
	 * @since   1.0
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__DIR__) . '/Stubs/database.xml');
	}

	/**
	 * Returns the default database connection for running the tests.
	 *
	 * @return  \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 *
	 * @since   1.0
	 */
	protected function getConnection()
	{
		if (static::$driver === null)
		{
			static::fail('Could not fetch a database driver to establish the connection.');
		}

		static::$driver->connect();

		return $this->createDefaultDBConnection(static::$driver->getConnection(), self::$options['database']);
	}
}
