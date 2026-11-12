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
 * Test class for Joomla\Database\Pgsql\PgsqlDriver::replacePrefix(), which needs no database connection
 */
class PgsqlDriverReplacePrefixTest extends TestCase
{
    /**
     * Data provider for replacePrefix()
     *
     * @return  \Generator
     */
    public static function dataReplacePrefix(): \Generator
    {
        yield 'every nextval' => [
            "SELECT nextval('#__a'), nextval('#__b'), nextval('#__c')",
            "SELECT nextval('jos_a'), nextval('jos_b'), nextval('jos_c')",
        ];

        yield 'mixed sequence functions' => [
            "SELECT currval('#__a'), setval('#__b', 1), currval('#__c'), setval('#__d', 2)",
            "SELECT currval('jos_a'), setval('jos_b', 1), currval('jos_c'), setval('jos_d', 2)",
        ];

        yield 'other literals are kept' => [
            "SELECT nextval('#__a'), '#__keep' FROM #__t",
            "SELECT nextval('jos_a'), '#__keep' FROM jos_t",
        ];

        yield 'query starting with a literal' => [
            "'#__keep' || x FROM #__t",
            "'#__keep' || x FROM jos_t",
        ];
    }

    /**
     * The prefix is replaced in sequence names and outside literals
     *
     * @param   string  $sql       The query
     * @param   string  $expected  The expected result
     *
     * @return  void
     */
    #[DataProvider('dataReplacePrefix')]
    public function testReplacePrefix(string $sql, string $expected): void
    {
        $db = new PgsqlDriver(['prefix' => 'jos_']);

        $this->assertSame($expected, $db->replacePrefix($sql));
    }
}
