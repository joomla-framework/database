<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\Oracle\OracleDriver;
use Joomla\Test\TestDatabase;
use Joomla\Database\DatabaseDriver;

/**
 * Abstract test case class for Oracle database testing.
 *
 * @since  1.0
 */
abstract class DatabaseOracleCase extends TestDatabase
{
	/**
	 * @var    array  The database driver options for the connection.
	 * @since  1.0
	 */
	private static $options = array('driver' => 'oracle');

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * An example DSN would be: dbname=//localhost:1521/joomla_ut;charset=AL32UTF8;user=utuser;pass=ut1234
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function setUpBeforeClass()
	{
		// First let's look to see if we have a DSN defined or in the environment variables.
		if (\defined('JTEST_DATABASE_ORACLE_DSN') || getenv('JTEST_DATABASE_ORACLE_DSN'))
		{
			$dsn = \defined('JTEST_DATABASE_ORACLE_DSN') ? JTEST_DATABASE_ORACLE_DSN : getenv('JTEST_DATABASE_ORACLE_DSN');
		}
		else
		{
			return;
		}

		// Make sure the driver is supported
		if (!OracleDriver::isSupported())
		{
			static::markTestSkipped('The PDO Oracle driver is not supported on this platform.');
		}

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
					self::$options['charset'] = $v;
					break;
				case 'dbname':
					$components = parse_url($v);
					self::$options['host'] = $components['host'];
					self::$options['port'] = $components['port'];
					self::$options['database'] = ltrim($components['path'], '/');
					break;
				case 'user':
					self::$options['user'] = $v;
					break;
				case 'pass':
					self::$options['password'] = $v;
					break;
				case 'dbschema':
					self::$options['schema'] = $v;
					break;
				case 'prefix':
					self::$options['prefix'] = $v;
					break;
			}
		}

		// Ensure some defaults.
		self::$options['charset'] = isset(self::$options['charset']) ? self::$options['charset'] : 'AL32UTF8';
		self::$options['port'] = isset(self::$options['port']) ? self::$options['port'] : 1521;

		try
		{
			// Attempt to instantiate the driver.
			static::$driver = DatabaseDriver::getInstance(self::$options);
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
