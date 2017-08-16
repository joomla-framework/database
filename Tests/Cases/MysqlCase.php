<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Cases;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Mysql\MysqlDriver;

/**
 * Abstract test case class for MySQL database testing.
 */
abstract class MysqlCase extends AbstractDatabaseTestCase
{
	/**
	 * The database driver options for the connection.
	 *
	 * @var  array
	 */
	protected static $options = ['driver' => 'mysql'];

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
		if (!defined('JTEST_DATABASE_MYSQL_DSN') && !getenv('JTEST_DATABASE_MYSQL_DSN'))
		{
			return;
		}

		// Make sure the driver is supported
		if (!MysqlDriver::isSupported())
		{
			static::markTestSkipped('The PDO MySQL driver is not supported on this platform.');
		}

		$dsn = defined('JTEST_DATABASE_MYSQL_DSN') ? JTEST_DATABASE_MYSQL_DSN : getenv('JTEST_DATABASE_MYSQL_DSN');

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
				case 'dbname':
					static::$options['database'] = $v;
					break;
				case 'user':
					static::$options['user'] = $v;
					break;
				case 'pass':
					static::$options['password'] = $v;
					break;
				case 'charset':
					static::$options['charset'] = $v;
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
}
