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
 * Test class for Joomla\Database\Sqlsrv\SqlsrvDriver::insertObject(), which needs no database connection
 */
class SqlsrvDriverInsertObjectTest extends TestCase
{
    /**
     * Creates a driver that records the executed queries instead of running them
     *
     * @return  SqlsrvDriver
     */
    private function createDriver(): SqlsrvDriver
    {
        return new class (['prefix' => 'jos_']) extends SqlsrvDriver {
            public array $queries = [];

            public function connect()
            {
            }

            public function getTableColumns($table, $typeOnly = true)
            {
                return ['id' => 'int', 'title' => 'nvarchar'];
            }

            protected function checkFieldExists($table, $field)
            {
                $this->queries[] = 'column check for ' . $field;

                return true;
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

            public function insertid()
            {
                return 7;
            }
        };
    }

    #[TestDox('A primary key with the value 0 is not inserted and receives the new ID')]
    public function testZeroPrimaryKeyIsSkipped()
    {
        $db     = $this->createDriver();
        $object = (object) ['id' => 0, 'title' => 'Hello'];

        $db->insertObject('#__test', $object, 'id');

        $this->assertSame(["INSERT INTO [#__test] ([title]) VALUES (N'Hello')"], $db->queries);
        $this->assertSame(7, $object->id);
    }

    #[TestDox('No extra query is run per column')]
    public function testNoQueryPerColumn()
    {
        $db     = $this->createDriver();
        $object = (object) ['title' => 'Hello'];

        $db->insertObject('#__test', $object);

        $this->assertCount(1, $db->queries);
    }

    #[TestDox('A primary key with a value is inserted')]
    public function testPrimaryKeyWithValueIsInserted()
    {
        $db     = $this->createDriver();
        $object = (object) ['id' => 5, 'title' => 'Hello'];

        $db->insertObject('#__test', $object, 'id');

        $this->assertSame(["INSERT INTO [#__test] ([id],[title]) VALUES (N'5',N'Hello')"], $db->queries);
    }
}
