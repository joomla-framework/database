<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Cases;

use Joomla\Database\DatabaseDriver;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\DataSet\XmlDataSet;
use PHPUnit\DbUnit\Operation\Composite;
use PHPUnit\DbUnit\Operation\Factory;
use PHPUnit\DbUnit\Operation\Operation;
use PHPUnit\DbUnit\TestCase;

/**
 * Base test case for the database package
 */
abstract class AbstractDatabaseTestCase extends TestCase
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
	protected function setUp(): void
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
	 * @return  XmlDataSet
	 */
	protected function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__DIR__) . '/Stubs/database.xml');
	}

	/**
	 * Returns the default database connection for running the tests.
	 *
	 * @return  DefaultConnection
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
	 * @return  Operation
	 */
	protected function getSetUpOperation()
	{
		// Required given the use of InnoDB contraints.
		return new Composite(
			[
				Factory::DELETE_ALL(),
				Factory::INSERT(),
			]
		);
	}

	/**
	 * Returns the database operation executed in test cleanup.
	 *
	 * @return  Operation
	 */
	protected function getTearDownOperation()
	{
		// Required given the use of InnoDB contraints.
		return Factory::DELETE_ALL();
	}
}
