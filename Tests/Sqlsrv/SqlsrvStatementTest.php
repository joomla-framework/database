<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlqrv;

use Joomla\Database\Sqlsrv\SqlsrvStatement;
use Joomla\Database\Tests\Cases\SqlsrvCase;

/**
 * Test class for \Joomla\Database\Sqlsrv\SqlsrvStatement.
 *
 * @since  1.0
 */
class SqlsrvStatementTest extends SqlsrvCase
{
	/**
	 * Regression test to ensure that named values with matching named params are correctly prepared.
     * This simulates a whereIn condition
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION
	 */
	public function testStatementPreparesManyArrayValues()
	{
		static::$driver->connect();
		$query = 'SELECT * FROM dbtest WHERE id IN (:preparedArray1,:preparedArray2,:preparedArray3,:preparedArray4,:preparedArray5,:preparedArray6,'
			. ':preparedArray7,:preparedArray8,:preparedArray9,:preparedArray10)';

        // Dummy assertion to ensure we haven't thrown an exception preparing the statement
        $this->assertInstanceOf(
            '\\Joomla\\Database\\Sqlsrv\\SqlsrvStatement',
            new SqlsrvStatement(static::$driver->getConnection(), $query)
        );
	}

    /**
     * Regression test to ensure that named values with matching named params are correctly prepared (part 2).
     * This simulates a general use case
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION
     */
    public function testStatementWithKeysMatching()
    {
        static::$driver->connect();
        $query = 'SELECT * FROM dbtest WHERE `id` = :id AND `title` = :id_title';

        // Dummy assertion to ensure we haven't thrown an exception preparing the statement
        $this->assertInstanceOf(
            '\\Joomla\\Database\\Sqlsrv\\SqlsrvStatement',
            new SqlsrvStatement(static::$driver->getConnection(), $query)
        );
    }

    /**
     * Regression test to ensure that named values with matching named params are correctly prepared (part 3).
     * This simulates a general use case for a search function where we reuse the same prepared statement term
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION
     */
    public function testStatementWithMultipleUseOfVars()
    {
        static::$driver->connect();
        $query = 'SELECT * FROM `dbtest` WHERE `description` LIKE :search_term AND `title` LIKE :search_term';

        // Dummy assertion to ensure we haven't thrown an exception preparing the statement
        $this->assertInstanceOf(
            '\\Joomla\\Database\\Sqlsrv\\SqlsrvStatement',
            new SqlsrvStatement(static::$driver->getConnection(), $query)
        );
    }
}
