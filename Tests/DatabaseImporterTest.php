<?php

/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseImporter;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Tests\Stubs\TestDatabaseImporter;
use Joomla\Database\Tests\Stubs\TestDatabaseQuery;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\DatabaseImporter
 */
class DatabaseImporterTest extends TestCase
{
    /**
     * Importer object
     *
     * @var  DatabaseImporter
     */
    private $importer;

    /**
     * Sets up the fixture.
     *
     * This method is called before a test is executed.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->importer = new TestDatabaseImporter();
    }

    /**
     * @testdox  The importer is correctly configured when instantiated
     */
    public function testInstantiation()
    {
        $expected = (object) [
            'withStructure' => true,
        ];

        $this->assertEquals($expected, TestHelper::getValue($this->importer, 'options'));
        $this->assertSame('xml', TestHelper::getValue($this->importer, 'asFormat'));
    }

    /**
     * @testdox  The importer can be set to XML format
     */
    public function testAsXml()
    {
        $this->assertSame($this->importer, $this->importer->asXml(), 'The importer supports method chaining');

        $this->assertSame('xml', TestHelper::getValue($this->importer, 'asFormat'));
    }

    /**
     * @testdox  A database drier can be set to the importer
     */
    public function testSetDbo()
    {
        /** @var DatabaseInterface|MockObject $db */
        $db = $this->createMock(DatabaseInterface::class);

        $this->assertSame($this->importer, $this->importer->setDbo($db), 'The importer supports method chaining');
    }

    /**
     * @testdox  The importer can be configured to export with structure
     */
    public function testWithStructure()
    {
        $this->assertSame($this->importer, $this->importer->withStructure(false), 'The importer supports method chaining');

        $options = TestHelper::getValue($this->importer, 'options');

        $this->assertFalse($options->withStructure);
    }
}
