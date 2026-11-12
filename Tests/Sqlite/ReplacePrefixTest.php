<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlite;

use Joomla\Database\Sqlite\SqliteDriver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\DatabaseDriver::replacePrefix(), using the SQLite driver without a connection
 */
class ReplacePrefixTest extends TestCase
{
    /**
     * Data provider for replacePrefix()
     *
     * @return  \Generator
     */
    public static function dataReplacePrefix(): \Generator
    {
        yield 'plain query' => [
            "SELECT * FROM #__a WHERE x = '#__keep'",
            "SELECT * FROM jos_a WHERE x = '#__keep'",
        ];

        yield 'quote in a line comment' => [
            "SELECT * FROM #__a -- don't\nWHERE x = '#__keep'",
            "SELECT * FROM jos_a -- don't\nWHERE x = '#__keep'",
        ];

        yield 'quote in a block comment' => [
            "SELECT * FROM #__a /* it's */ WHERE x = '#__keep' AND y IN (SELECT id FROM #__b)",
            "SELECT * FROM jos_a /* it's */ WHERE x = '#__keep' AND y IN (SELECT id FROM jos_b)",
        ];

        yield 'comment markers inside a literal' => [
            "SELECT '-- not a comment', '/* nor this' FROM #__a",
            "SELECT '-- not a comment', '/* nor this' FROM jos_a",
        ];

        yield 'double minus without whitespace is not a comment' => [
            "SELECT 5--1, 'it''s' FROM #__a",
            "SELECT 5--1, 'it''s' FROM jos_a",
        ];
    }

    /**
     * The prefix is replaced outside literals, and comments do not affect the literal detection
     *
     * @param   string  $sql       The query
     * @param   string  $expected  The expected result
     *
     * @return  void
     */
    #[DataProvider('dataReplacePrefix')]
    public function testReplacePrefix(string $sql, string $expected): void
    {
        $db = new SqliteDriver(['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => 'jos_']);

        $this->assertSame($expected, $db->replacePrefix($sql));
    }
}
