<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Mysqli\MysqliDriver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;

/**
 * Tests that failing COMMIT and ROLLBACK calls are reported by Joomla\Database\Mysqli\MysqliDriver
 *
 * Uses a mocked connection, so no database server is needed.
 */
#[RequiresPhpExtension('mysqli')]
class MysqliDriverTransactionFailureTest extends TestCase
{
    /**
     * Data provider for the transaction end methods
     *
     * @return  \Generator
     */
    public static function dataTransactionEnd(): \Generator
    {
        yield 'commit'   => ['transactionCommit', 'commit'];
        yield 'rollback' => ['transactionRollback', 'rollback'];
    }

    /**
     * A failing COMMIT/ROLLBACK must throw instead of being ignored
     *
     * @param   string  $driverMethod      The driver method to call
     * @param   string  $connectionMethod  The mysqli method that fails
     *
     * @return  void
     */
    #[DataProvider('dataTransactionEnd')]
    public function testFailureIsReported(string $driverMethod, string $connectionMethod): void
    {
        $connection = $this->createMock(\mysqli::class);
        $connection->expects($this->once())
            ->method($connectionMethod)
            ->willThrowException(new \mysqli_sql_exception('Deadlock found when trying to get lock', 1213));

        $db = new MysqliDriver([]);

        $property = new \ReflectionProperty($db, 'connection');
        $property->setValue($db, $connection);

        $depth = new \ReflectionProperty($db, 'transactionDepth');
        $depth->setValue($db, 1);

        try {
            $db->$driverMethod();
            $this->fail('A failing ' . $connectionMethod . ' must throw an exception');
        } catch (ExecutionFailureException $e) {
            $this->assertSame(1213, $e->getCode());
            $this->assertInstanceOf(\mysqli_sql_exception::class, $e->getPrevious());
        }

        // The transaction has ended on the server either way
        $this->assertSame(0, $depth->getValue($db));

        // Prevent the destructor from calling methods on the mock
        $property->setValue($db, null);
    }
}
