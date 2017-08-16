<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Cases;

use Joomla\Database\Sqlite\SqliteDriver;
use Joomla\Database\DatabaseDriver;

/**
 * Abstract test case class for SQLite database testing.
 */
abstract class SqliteCase extends AbstractDatabaseTestCase
{
	/**
	 * The database driver options for the connection.
	 *
	 * @var  array
	 */
	protected static $options = ['driver' => 'sqlite', 'database' => ':memory:'];

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * An example DSN would be: host=localhost;port=5432;dbname=joomla_ut;user=utuser;pass=ut1234
	 *
	 * @return  void
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
			static::$driver = DatabaseDriver::getInstance(static::$options);

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
}
