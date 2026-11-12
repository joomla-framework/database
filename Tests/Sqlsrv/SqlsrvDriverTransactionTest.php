<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlsrv;

use Joomla\Database\Sqlsrv\SqlsrvDriver;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Test class for the transaction handling of Joomla\Database\Sqlsrv\SqlsrvDriver, which needs no database connection
 */
class SqlsrvDriverTransactionTest extends TestCase
{
    /**
     * Creates a driver that records the queries instead of running them
     *
     * @return  SqlsrvDriver
     */
    private function createDriver(): SqlsrvDriver
    {
        return new class ([]) extends SqlsrvDriver {
            public array $queries = [];

            public function connect()
            {
            }

            public function setQuery($query, $offset = 0, $limit = 0)
            {
                $this->sql = $query;

                return $this;
            }

            public function execute()
            {
                $this->queries[] = (string) $this->sql;

                return true;
            }
        };
    }

    #[TestDox('A nested transaction creates a savepoint and rolling back targets it')]
    public function testSavepointRollback()
    {
        $db = $this->createDriver();

        $db->transactionStart();
        $db->transactionStart(true);
        $db->transactionRollback(true);
        $db->transactionCommit();

        $this->assertSame(
            ['BEGIN TRANSACTION', 'SAVE TRANSACTION [SP_1]', 'ROLLBACK TRANSACTION [SP_1]', 'COMMIT TRANSACTION'],
            $db->queries
        );
    }

    #[TestDox('Committing a savepoint does not run a statement')]
    public function testSavepointCommit()
    {
        $db = $this->createDriver();

        $db->transactionStart();
        $db->transactionStart(true);
        $db->transactionCommit(true);
        $db->transactionCommit();

        $this->assertSame(['BEGIN TRANSACTION', 'SAVE TRANSACTION [SP_1]', 'COMMIT TRANSACTION'], $db->queries);
    }
}
