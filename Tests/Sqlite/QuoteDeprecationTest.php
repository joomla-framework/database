<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlite;

use Joomla\Database\Sqlite\SqliteDriver;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests the deprecation of unsupported input to Joomla\Database\DatabaseDriver::quote(), using SQLite without a connection
 */
class QuoteDeprecationTest extends TestCase
{
    #[TestDox('Quoting a value that is neither scalar nor an array is deprecated')]
    public function testQuoteNonScalarIsDeprecated()
    {
        // A driver whose escape() accepts anything that casts to a string, so only quote() is tested
        $db = new class (['driver' => 'sqlite', 'database' => ':memory:']) extends SqliteDriver {
            public function escape($text, $extra = false)
            {
                return (string) $text;
            }
        };

        $resource = fopen('php://memory', 'r');

        $deprecations = [];

        set_error_handler(
            function (int $errno, string $errstr) use (&$deprecations) {
                $deprecations[] = $errstr;

                return true;
            },
            E_USER_DEPRECATED
        );

        try {
            $db->quote('text');
            $db->quote(1);
            $db->quote(['a', 2]);
            $this->assertSame([], $deprecations, 'Scalars and arrays of scalars are fine');

            $db->quote($resource);
        } finally {
            restore_error_handler();
            fclose($resource);
        }

        $this->assertCount(1, $deprecations);
        $this->assertStringContainsString('Passing a value of type resource', $deprecations[0]);
    }
}
