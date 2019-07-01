<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\Mysqli\MysqliStatement;
use Joomla\Database\Tests\Cases\MysqliCase;

/**
 * Test class for \Joomla\Database\Mysqli\MysqliStatement.
 *
 * @since  1.0
 */
class MysqliStatementTest extends MysqliCase
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
            '\\Joomla\\Database\\Mysqli\\MysqliStatement',
            new MysqliStatement(static::$driver->getConnection(), $query)
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
            '\\Joomla\\Database\\Mysqli\\MysqliStatement',
            new MysqliStatement(static::$driver->getConnection(), $query)
        );
    }
}
