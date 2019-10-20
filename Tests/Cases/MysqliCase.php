<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Cases;

use Joomla\Database\Mysql\MysqlDriver;
use Joomla\Database\Mysqli\MysqliDriver;
use Joomla\Database\DatabaseDriver;
use PHPUnit\DbUnit\Database\DefaultConnection;

/**
 * Abstract test case class for MySQLi database testing.
 */
abstract class MysqliCase extends AbstractDatabaseTestCase
{
	/**
	 * The database driver options for the connection.
	 *
	 * @var  array
	 */
	protected static $options = array('driver' => 'mysqli');

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * An example DSN would be: host=localhost;dbname=joomla_ut;user=utuser;pass=ut1234
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		// First let's look to see if we have a DSN defined or in the environment variables.
		if (!\defined('JTEST_DATABASE_MYSQLI_DSN') && !getenv('JTEST_DATABASE_MYSQLI_DSN'))
		{
			return;
		}

		// Make sure the driver is supported, we check both PDO MySQL and MySQLi here due to PHPUnit requiring a PDO connection to set up the test
		if (!MysqlDriver::isSupported() || !MysqliDriver::isSupported())
		{
			static::markTestSkipped('The PDO MySQL or MySQLi driver is not supported on this platform.');
		}

		$dsn = \defined('JTEST_DATABASE_MYSQLI_DSN') ? JTEST_DATABASE_MYSQLI_DSN : getenv('JTEST_DATABASE_MYSQLI_DSN');

		// First let's trim the mysql: part off the front of the DSN if it exists.
		if (strpos($dsn, 'mysql:') === 0)
		{
			$dsn = substr($dsn, 6);
		}

		// Split the DSN into its parts over semicolons.
		$parts = explode(';', $dsn);

		// Parse each part and populate the options array.
		foreach ($parts as $part)
		{
			list ($k, $v) = explode('=', $part, 2);

			switch ($k)
			{
				case 'host':
					static::$options['host'] = $v;
					break;
				case 'port':
					static::$options['port'] = $v;
					break;
				case 'dbname':
					static::$options['database'] = $v;
					break;
				case 'user':
					static::$options['user'] = $v;
					break;
				case 'pass':
					static::$options['password'] = $v;
					break;
			}
		}

		try
		{
			// Attempt to instantiate the driver.
			static::$driver = DatabaseDriver::getInstance(static::$options);
		}
		catch (\RuntimeException $e)
		{
			static::$driver = null;
		}
	}

	/**
	 * Returns the default database connection for running the tests.
	 *
	 * @return  DefaultConnection
	 */
	protected function getConnection()
	{
		// Compile the connection DSN.
		$dsn = 'mysql:host=' . static::$options['host'] . ';dbname=' . static::$options['database'];

		if (isset(self::$options['port']))
		{
			$dsn .= ';port=' . self::$options['port'];
		}

		// Create the PDO object from the DSN and options.
		$pdo = new \PDO($dsn, static::$options['user'], static::$options['password']);

		return $this->createDefaultDBConnection($pdo, static::$options['database']);
	}
}
