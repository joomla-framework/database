<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Cases;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Oracle\OracleDriver;

/**
 * Abstract test case class for Oracle database testing.
 */
abstract class OracleCase extends AbstractDatabaseTestCase
{
	/**
	 * The database driver options for the connection.
	 *
	 * @var  array
	 */
	protected static $options = ['driver' => 'oracle'];

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * An example DSN would be: dbname=//localhost:1521/joomla_ut;charset=AL32UTF8;user=utuser;pass=ut1234
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		// First let's look to see if we have a DSN defined or in the environment variables.
		if (!defined('JTEST_DATABASE_ORACLE_DSN') && !getenv('JTEST_DATABASE_ORACLE_DSN'))
		{
			return;
		}

		// Make sure the driver is supported
		if (!OracleDriver::isSupported())
		{
			static::markTestSkipped('The PDO Oracle driver is not supported on this platform.');
		}

		$dsn = defined('JTEST_DATABASE_ORACLE_DSN') ? JTEST_DATABASE_ORACLE_DSN : getenv('JTEST_DATABASE_ORACLE_DSN');

		// First let's trim the oci: part off the front of the DSN if it exists.
		if (strpos($dsn, 'oci:') === 0)
		{
			$dsn = substr($dsn, 4);
		}

		// Split the DSN into its parts over semicolons.
		$parts = explode(';', $dsn);

		// Parse each part and populate the options array.
		foreach ($parts as $part)
		{
			list ($k, $v) = explode('=', $part, 2);

			switch ($k)
			{
				case 'charset':
					static::$options['charset'] = $v;
					break;
				case 'dbname':
					$components = parse_url($v);
					static::$options['host'] = $components['host'];
					static::$options['port'] = $components['port'];
					static::$options['database'] = ltrim($components['path'], '/');
					break;
				case 'user':
					static::$options['user'] = $v;
					break;
				case 'pass':
					static::$options['password'] = $v;
					break;
				case 'dbschema':
					static::$options['schema'] = $v;
					break;
				case 'prefix':
					static::$options['prefix'] = $v;
					break;
			}
		}

		// Ensure some defaults.
		static::$options['charset'] = static::$options['charset'] ?? 'AL32UTF8';
		static::$options['port']    = static::$options['port'] ?? 1521;

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
