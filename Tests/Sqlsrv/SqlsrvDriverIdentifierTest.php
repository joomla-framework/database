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
 * Tests that Joomla\Database\Sqlsrv\SqlsrvDriver quotes identifiers in its helper statements; needs no database connection
 */
class SqlsrvDriverIdentifierTest extends TestCase
{
    /**
     * Creates a driver that records the queries instead of running them
     *
     * @return  SqlsrvDriver
     */
    private function createDriver(): SqlsrvDriver
    {
        return new class (['database' => 'my]db', 'prefix' => 'jos_']) extends SqlsrvDriver {
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

            public function loadColumn($offset = 0)
            {
                $this->execute();

                return [];
            }
        };
    }

    #[TestDox('renameTable() passes the names to sp_rename as quoted strings')]
    public function testRenameTable()
    {
        $db = $this->createDriver();

        $db->renameTable('#__a', "b'; DROP TABLE x; --");

        $this->assertSame(["sp_rename N'jos_a', N'b''; DROP TABLE x; --'"], $db->queries);
    }

    #[TestDox('getTableList() quotes the database name')]
    public function testGetTableList()
    {
        $db = $this->createDriver();

        $db->getTableList();

        $this->assertSame(["SELECT name FROM [my]]db].sys.Tables WHERE type = 'U';"], $db->queries);
    }
}
