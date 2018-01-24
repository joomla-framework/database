<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Cases;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Pgsql\PgsqlDriver;
use Joomla\Database\Postgresql\PostgresqlDriver;

/**
 * Abstract test case class for PostgreSQL database testing.
 */
abstract class PostgresqlCase extends AbstractDatabaseTestCase
{
	/**
	 * The database driver options for the connection.
	 *
	 * @var  array
	 */
	protected static $options = ['driver' => 'postgresql'];

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * An example DSN would be: host=localhost;port=5432;dbname=joomla_ut;user=utuser;pass=ut1234
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		// First let's look to see if we have a DSN defined or in the environment variables.
		if (!defined('JTEST_DATABASE_POSTGRESQL_DSN') && !getenv('JTEST_DATABASE_POSTGRESQL_DSN'))
		{
			return;
		}

		/*
		 * Make sure the driver is supported, we check both PDO PostgreSQL and "plain" PostgreSQL here
		 * due to PHPUnit requiring a PDO connection to set up the test
		 */
		if (!PostgresqlDriver::isSupported() || !PgsqlDriver::isSupported())
		{
			static::markTestSkipped('The PDO PostgreSQL or PostgreSQL driver is not supported on this platform.');
		}

		$dsn = defined('JTEST_DATABASE_POSTGRESQL_DSN') ? JTEST_DATABASE_POSTGRESQL_DSN : getenv('JTEST_DATABASE_POSTGRESQL_DSN');

		// First let's trim the pgsql: part off the front of the DSN if it exists.
		if (strpos($dsn, 'pgsql:') === 0)
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
					static::$options['port'] = (int) $v;
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
	 * @return  \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 */
	protected function getConnection()
	{
		// Compile the connection DSN.
		$dsn = 'pgsql:host=' . static::$options['host'] . ';port=' . static::$options['port'] . ';dbname=' . static::$options['database'];

		// Create the PDO object from the DSN and options.
		$pdo = new \PDO($dsn, static::$options['user'], static::$options['password']);

		return $this->createDefaultDBConnection($pdo, static::$options['database']);
	}
}
