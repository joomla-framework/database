<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysql;

use Joomla\Database\Mysql\MysqlDriver;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Test class for the SSL attributes of Joomla\Database\Mysql\MysqlDriver, which needs no database connection
 */
#[RequiresPhpExtension('pdo_mysql')]
class MysqlDriverSslAttributeTest extends TestCase
{
    #[TestDox('The SSL options map to the PDO MySQL attributes without deprecated constants')]
    public function testSslAttributes()
    {
        $method = new \ReflectionMethod(MysqlDriver::class, 'getSslAttribute');

        $deprecations = [];

        set_error_handler(
            function (int $errno, string $errstr) use (&$deprecations) {
                $deprecations[] = $errstr;

                return true;
            },
            E_DEPRECATED | E_USER_DEPRECATED
        );

        try {
            $attributes = [];

            foreach (['cipher', 'ca', 'capath', 'key', 'cert', 'verify_server_cert'] as $name) {
                $attributes[$name] = $method->invoke(null, $name);
            }
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $deprecations);

        // The numeric values differ between PHP versions, so only check that every option maps to its own attribute
        $this->assertContainsOnlyInt($attributes);
        $this->assertCount(6, array_unique($attributes));
    }
}
