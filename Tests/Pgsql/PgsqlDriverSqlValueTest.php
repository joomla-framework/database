<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Pgsql;

use Joomla\Database\Pgsql\PgsqlDriver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Pgsql\PgsqlDriver::sqlValue(), which needs no database connection
 */
class PgsqlDriverSqlValueTest extends TestCase
{
    /**
     * Data provider for numeric column values
     *
     * @return  \Generator
     */
    public static function dataNumericValues(): \Generator
    {
        yield 'integer'                 => ['integer', 42, '42'];
        yield 'numeric string'          => ['integer', '42', '42'];
        yield 'negative decimal string' => ['numeric', '-1.5', '-1.5'];
        yield 'float'                   => ['real', 1.5, '1.5'];
        yield 'empty string'            => ['bigint', '', 'NULL'];
        yield 'null'                    => ['smallint', null, 'NULL'];
        yield 'injection attempt'       => ['integer', '0 OR 1=1; DROP TABLE users --', "'0 OR 1=1; DROP TABLE users --'"];
        yield 'quote in value'          => ['numeric', "1'; DELETE FROM t; --", "'1''; DELETE FROM t; --'"];
        yield 'money with symbol'       => ['money', '$1.00', "'$1.00'"];
    }

    /**
     * Values for numeric columns are only used verbatim when they are numeric
     *
     * @param   string  $type      The column type
     * @param   mixed   $value     The value
     * @param   string  $expected  The expected SQL
     *
     * @return  void
     */
    #[DataProvider('dataNumericValues')]
    public function testSqlValueForNumericColumns(string $type, $value, string $expected): void
    {
        $db = new PgsqlDriver([]);

        $this->assertSame($expected, $db->sqlValue(['col' => $type], 'col', $value));
    }
}
