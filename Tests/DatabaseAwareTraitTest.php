<?php

/**
 * @copyright  Copyright (C) 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Exception\DatabaseNotFoundException;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests for DatabaseAwareTrait class.
 */
#[CoversTrait(DatabaseAwareTrait::class)]
class DatabaseAwareTraitTest extends TestCase
{
    /**
     * @var DatabaseAwareTrait
     */
    protected $object;

    #[TestDox('Database can be set with setDatabase()')]
    public function testGetSetDatabase(): void
    {
        $db = $this->createStub(DatabaseInterface::class);

        $trait = new class () {
            use DatabaseAwareTrait;

            public function getDb()
            {
                return $this->getDatabase();
            }
        };

        $trait->setDatabase($db);

        $this->assertSame($db, $trait->getDb());
    }

    #[TestDox('getDatabase() throws an DatabaseNotFoundException, if no database is set')]
    public function testGetDatabaseException(): void
    {
        $this->expectException(DatabaseNotFoundException::class);

        $trait = new class () {
            use DatabaseAwareTrait;

            public function getDb()
            {
                return $this->getDatabase();
            }
        };

        $trait->getDb();
    }
}
