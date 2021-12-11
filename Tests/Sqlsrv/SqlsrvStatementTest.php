<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlqrv;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Sqlsrv\SqlsrvStatement;
use Joomla\Test\DatabaseTestCase;

/**
 * Test class for Joomla\Database\Sqlsrv\SqlsrvStatement
 */
class SqlsrvStatementTest extends DatabaseTestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		if (!static::$connection || static::$connection->getName() !== 'sqlsrv')
		{
			self::markTestSkipped('SQL Server database not configured.');
		}
	}

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		try
		{
			foreach (DatabaseDriver::splitSql(file_get_contents(dirname(__DIR__) . '/Stubs/Schema/sqlsrv.sql')) as $query)
			{
				static::$connection->setQuery($query)
					->execute();
			}
		}
		catch (ExecutionFailureException $exception)
		{
			$this->markTestSkipped(
				\sprintf(
					'Could not load SQL Server database: %s',
					$exception->getMessage()
				)
			);
		}
	}

	/**
	 * Tears down the fixture.
	 *
	 * This method is called after a test is executed.
	 */
	protected function tearDown(): void
	{
		foreach (static::$connection->getTableList() as $table)
		{
			static::$connection->dropTable($table);
		}
	}

	/**
	 * Regression test to ensure that named values with matching named params are correctly prepared, this simulates a whereIn condition.
	 *
	 * @doesNotPerformAssertions
	 */
	public function testStatementPreparesManyArrayValues()
	{
		$query = 'SELECT * FROM dbtest WHERE id IN (:preparedArray1,:preparedArray2,:preparedArray3,:preparedArray4,:preparedArray5,:preparedArray6,:preparedArray7,:preparedArray8,:preparedArray9,:preparedArray10)';

		new SqlsrvStatement(static::$connection->getConnection(), $query);
	}

	/**
	 * Regression test to ensure that named values with matching named params are correctly prepared (part 2), this simulates a general use case.
	 *
	 * @doesNotPerformAssertions
	 */
	public function testStatementWithKeysMatching()
	{
		$query = 'SELECT * FROM dbtest WHERE id = :id AND title = :id_title';

		new SqlsrvStatement(static::$connection->getConnection(), $query);
	}

	/**
  * Regression test to ensure that named values with matching named params are correctly prepared (part 3).
  * This simulates a general use case for a search function where we reuse the same prepared statement term.
  *
* @doesNotPerformAssertions
  */
	public function testStatementWithMultipleUseOfVars()
	{
		$query = 'SELECT * FROM dbtest WHERE description LIKE :search_term AND title LIKE :search_term';

		new SqlsrvStatement(static::$connection->getConnection(), $query);
	}
}
