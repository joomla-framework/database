<?php
/**
 * Part of the Joomla Framework Test Package
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Test;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Sqlite\SqliteDriver;

/**
 * Abstract test case class for database testing.
 *
 * @since  1.0
 * @deprecated  2.0  Deprecated due to the deprecation of `phpunit/dbunit`
 */
abstract class TestDatabase extends \PHPUnit_Extensions_Database_TestCase
{
	/**
	 * The active database driver being used for the tests.
	 *
	 * @var    DatabaseDriver
	 * @since  1.0
	 * @deprecated  2.0
	 */
	protected static $driver;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @deprecated  2.0
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
			static::$driver->getConnection()->exec(file_get_contents(__DIR__ . '/Schema/ddl.sql'));
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
	 * @deprecated  2.0
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
	 * Assigns mock callbacks to methods.
	 *
	 * @param   \PHPUnit_Framework_MockObject_MockObject  $mockObject  The mock object that the callbacks are being assigned to.
	 * @param   array                                     $array       An array of methods names to mock with callbacks.
	 *
	 * @return  void
	 *
	 * @note    This method assumes that the mock callback is named {mock}{method name}.
	 * @since   1.0
	 * @deprecated  2.0  Use TestHelper::assignMockCallbacks instead
	 */
	public function assignMockCallbacks($mockObject, $array)
	{
		TestHelper::assignMockCallbacks($mockObject, $this, $array);
	}

	/**
	 * Assigns mock values to methods.
	 *
	 * @param   \PHPUnit_Framework_MockObject_MockObject  $mockObject  The mock object.
	 * @param   array                                     $array       An associative array of methods to mock with
	 *                                                                 return values:
	 *                                                                 string (method name) => mixed (return value)
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @deprecated  2.0  Use TestHelper::assignMockReturns instead
	 */
	public function assignMockReturns($mockObject, $array)
	{
		TestHelper::assignMockReturns($mockObject, $this, $array);
	}

	/**
	 * Returns the default database connection for running the tests.
	 *
	 * @return  \PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 *
	 * @since   1.0
	 * @deprecated  2.0
	 */
	protected function getConnection()
	{
		if (static::$driver !== null)
		{
			return $this->createDefaultDBConnection(static::$driver->getConnection(), ':memory:');
		}
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_IDataSet
	 *
	 * @since   1.0
	 * @deprecated  2.0
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/Stubs/empty.xml');
	}

	/**
	 * Returns the database operation executed in test setup.
	 *
	 * @return  \PHPUnit_Extensions_Database_Operation_IDatabaseOperation
	 *
	 * @since   1.0
	 * @deprecated  2.0
	 */
	protected function getSetUpOperation()
	{
		// Required given the use of InnoDB contraints.
		return new \PHPUnit_Extensions_Database_Operation_Composite(
			array(
				\PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(),
				\PHPUnit_Extensions_Database_Operation_Factory::INSERT(),
			)
		);
	}

	/**
	 * Returns the database operation executed in test cleanup.
	 *
	 * @return  \PHPUnit_Extensions_Database_Operation_IDatabaseOperation
	 *
	 * @since   1.0
	 * @deprecated  2.0
	 */
	protected function getTearDownOperation()
	{
		// Required given the use of InnoDB contraints.
		return \PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL();
	}

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @deprecated  2.0
	 */
	protected function setUp()
	{
		if (empty(static::$driver))
		{
			$this->markTestSkipped('There is no database driver.');
		}

		parent::setUp();
	}
}
