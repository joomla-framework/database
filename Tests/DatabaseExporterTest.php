<?php

/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseExporter;
use Joomla\Database\DatabaseImporter;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Tests\Stubs\TestDatabaseExporter;
use Joomla\Database\Tests\Stubs\TestDatabaseImporter;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\DatabaseExporter
 */
class DatabaseExporterTest extends TestCase
{
    /**
     * Importer object
     *
     * @var  DatabaseExporter
     */
    private $exporter;

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

        $this->exporter = new TestDatabaseExporter();
    }

    /**
     * @testdox  The exporter is correctly configured when instantiated
     */
    public function testInstantiation()
    {
        $expected = (object) [
            'withStructure' => true,
            'withData'      => false,
        ];

        $this->assertEquals($expected, TestHelper::getValue($this->exporter, 'options'));
        $this->assertSame('xml', TestHelper::getValue($this->exporter, 'asFormat'));
    }

    /**
     * @testdox  The exporter can be set to XML format
     */
    public function testAsXml()
    {
        $this->assertSame($this->exporter, $this->exporter->asXml(), 'The exporter supports method chaining');

        $this->assertSame('xml', TestHelper::getValue($this->exporter, 'asFormat'));
    }

    /**
     * Data provider for from test cases
     *
     * @return  array
     */
    public static function dataFrom(): array
    {
        return [
            'single table' => [
                '#__dbtest',
                false,
            ],

            'multiple tables' => [
                ['#__content', '#__dbtest'],
                false,
            ],

            'incorrect table data type' => [
                new \stdClass(),
                true,
            ],
        ];
    }

    /**
     * @testdox  The tables to be exported can be configured
     *
     * @param   string[]|string  $from                  The name of a single table, or an array of the table names to export.
     * @param   boolean          $shouldRaiseException  Flag indicating the exporter should raise an exception for an unsupported data type
     */
    #[DataProvider('dataFrom')]
    public function testFrom($from, bool $shouldRaiseException)
    {
        if ($shouldRaiseException) {
            $this->expectException(\InvalidArgumentException::class);
        }

        $this->assertSame($this->exporter, $this->exporter->from($from), 'The exporter supports method chaining');

        $this->assertSame((array) $from, TestHelper::getValue($this->exporter, 'from'));
    }

    /**
     * @testdox  A database drier can be set to the exporter
     */
    public function testSetDbo()
    {
        /** @var DatabaseInterface|MockObject $db */
        $db = $this->createMock(DatabaseInterface::class);

        $this->assertSame($this->exporter, $this->exporter->setDbo($db), 'The exporter supports method chaining');
    }

    /**
     * @testdox  The exporter can be configured to export with structure
     */
    public function testWithStructure()
    {
        $this->assertSame($this->exporter, $this->exporter->withStructure(false), 'The exporter supports method chaining');

        $options = TestHelper::getValue($this->exporter, 'options');

        $this->assertFalse($options->withStructure);
    }

    /**
     * @testdox  The exporter can be configured to export with data
     */
    public function testWithData()
    {
        $this->assertSame($this->exporter, $this->exporter->withData(true), 'The exporter supports method chaining');

        $options = TestHelper::getValue($this->exporter, 'options');

        $this->assertTrue($options->withData);
    }
}
