<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Sqlite\SqliteDriver;
use Joomla\Test\TestDatabase;

/**
 * Abstract test case class for MySQLi database testing.
 *
 * @since  1.0
 */
abstract class DatabaseSqliteCase extends TestDatabase
{
	/**
	 * @var    array  The database driver options for the connection.
	 * @since  1.0
	 */
	private static $options = array('driver' => 'sqlite');

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function setUpBeforeClass()
	{
		if (!class_exists('\\Joomla\\Database\\DatabaseDriver'))
		{
			static::markTestSkipped('The joomla/database package is not installed, cannot use this test case.');
		}

		// Make sure the driver is supported
		if (!SqliteDriver::isSupported())
		{
			static::markTestSkipped('The SQLite driver is not supported on this platform.');
		}

		// We always want the default database test case to use an SQLite memory database.
		$options = array(
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => 'jos_',
		);

		try
		{
			// Attempt to instantiate the driver.
			static::$driver = DatabaseDriver::getInstance($options);
			static::$driver->connect();

			// Get the PDO instance for an SQLite memory database and load the test schema into it.
			static::$driver->getConnection()->exec(file_get_contents(__DIR__ . '/Stubs/ddl.sql'));
		}
		catch (\RuntimeException $e)
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
		return $this->createXMLDataSet(__DIR__ . '/Stubs/database.xml');
	}

	/**
	 * Returns the default database connection for running the tests.
	 *
	 * @return  \PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 *
	 * @since   1.0
	 */
	protected function getConnection()
	{
		if (!is_null(static::$driver))
		{
			return $this->createDefaultDBConnection(static::$driver->getConnection(), ':memory:');
		}

		return null;
	}
}
