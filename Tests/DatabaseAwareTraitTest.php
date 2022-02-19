<?php
/**
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\Exception\DatabaseNotFoundException;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for DatabaseAwareTrait class.
 */
class DatabaseAwareTraitTest extends TestCase
{
    /**
     * @var DatabaseAwareTrait
     */
    protected $object;

    /**
     * @testdox  Database can be set with setDatabase()
     *
     * @covers   Joomla\Database\DatabaseAwareTrait
     * @uses     Joomla\Database\Database
     */
    public function testGetDatabase()
    {
        $db = $this->createMock(DatabaseInterface::class);

        $trait = $this->getObjectForTrait(DatabaseAwareTrait::class);
        $trait->setDatabase($db);

        $this->assertSame($db, TestHelper::getValue($trait, '_db'));
    }

    /**
     * @testdox  getDatabase() throws an DatabaseNotFoundException, if no database is set
     *
     * @covers   Joomla\Database\DatabaseAwareTrait
     */
    public function testGetDatabaseException()
    {
        $this->expectException(DatabaseNotFoundException::class);

        $trait = $this->getObjectForTrait(DatabaseAwareTrait::class);

        TestHelper::invoke($trait, 'getDatabase');
    }
}
