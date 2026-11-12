<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Exception;

use Joomla\Database\Exception\ConnectionFailureException;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Exception\PrepareStatementFailureException;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests that the database exceptions accept any Throwable as the previous exception
 */
class PreviousThrowableTest extends TestCase
{
    #[TestDox('An Error can be passed as previous exception to ExecutionFailureException')]
    public function testExecutionFailureExceptionAcceptsError()
    {
        $error     = new \Error('mysqli_stmt object is already closed');
        $exception = new ExecutionFailureException('SELECT 1', $error->getMessage(), 0, $error);

        $this->assertSame($error, $exception->getPrevious());
        $this->assertSame('SELECT 1', $exception->getQuery());
    }

    #[TestDox('An Error can be passed as previous exception to ConnectionFailureException')]
    public function testConnectionFailureExceptionAcceptsError()
    {
        $error = new \Error('failure');

        $this->assertSame($error, (new ConnectionFailureException('x', 0, $error))->getPrevious());
    }

    #[TestDox('An Error can be passed as previous exception to PrepareStatementFailureException')]
    public function testPrepareStatementFailureExceptionAcceptsError()
    {
        $error = new \Error('failure');

        $this->assertSame($error, (new PrepareStatementFailureException('x', 0, $error))->getPrevious());
    }
}
