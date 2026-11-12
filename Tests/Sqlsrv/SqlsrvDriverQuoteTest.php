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
 * Test class for the escaping of Joomla\Database\Sqlsrv\SqlsrvDriver, which needs no database connection
 */
class SqlsrvDriverQuoteTest extends TestCase
{
    #[TestDox('escape() does not emit SQL syntax for NUL bytes')]
    public function testEscapeKeepsNulBytes()
    {
        $db = new SqlsrvDriver([]);

        $this->assertSame("a''b" . \chr(0) . 'c', $db->escape("a'b" . \chr(0) . 'c'));
    }

    #[TestDox('quote() turns NUL bytes into CHAR(0)')]
    public function testQuoteReplacesNulBytes()
    {
        $db = new SqlsrvDriver([]);

        $this->assertSame("N'a''b' + CHAR(0) + N'c'", $db->quote("a'b" . \chr(0) . 'c'));
        $this->assertSame("N'abc'", $db->quote('abc'));
    }
}
