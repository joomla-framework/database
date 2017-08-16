<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Cases;

use Joomla\Database\DatabaseDriver;

/**
 * Base test case for the database package
 */
abstract class AbstractDatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
	/**
	 * The active database driver being used for the tests.
	 *
	 * @var  DatabaseDriver
	 */
	protected static $driver;

	/**
	 * The database driver options for the connection.
	 *
	 * @var  array
	 */
	protected static $options = [];

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		if (!static::$driver)
		{
			$this->markTestSkipped('There is no database driver.');
		}

		parent::setUp();
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

		return $this->createDefaultDBConnection(static::$driver->getConnection(), static::$options['database']);
	}

	/**
	 * Returns the database operation executed in test setup.
	 *
	 * @return  \PHPUnit_Extensions_Database_Operation_IDatabaseOperation
	 */
	protected function getSetUpOperation()
	{
		// Required given the use of InnoDB contraints.
		return new \PHPUnit_Extensions_Database_Operation_Composite(
			[
				\PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(),
				\PHPUnit_Extensions_Database_Operation_Factory::INSERT(),
			]
		);
	}

	/**
	 * Returns the database operation executed in test cleanup.
	 *
	 * @return  \PHPUnit_Extensions_Database_Operation_IDatabaseOperation
	 */
	protected function getTearDownOperation()
	{
		// Required given the use of InnoDB contraints.
		return \PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL();
	}
}
