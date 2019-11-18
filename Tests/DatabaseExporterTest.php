<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseExporter;
use Joomla\Database\DatabaseInterface;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\DatabaseExporter
 */
class DatabaseExporterTest extends TestCase
{
	/**
	 * @testdox  The exporter is correctly configured when instantiated
	 */
	public function testInstantiation()
	{
		/** @var DatabaseExporter|MockObject $exporter */
		$exporter = $this->getMockForAbstractClass(DatabaseExporter::class);

		$expected = (object) [
			'withStructure' => true,
			'withData'      => false,
		];

		$this->assertEquals($expected, TestHelper::getValue($exporter, 'options'));
		$this->assertSame('xml', TestHelper::getValue($exporter, 'asFormat'));
	}

	/**
	 * @testdox  The exporter can be set to XML format
	 */
	public function testAsXml()
	{
		/** @var DatabaseExporter|MockObject $exporter */
		$exporter = $this->getMockForAbstractClass(DatabaseExporter::class);

		$this->assertSame($exporter, $exporter->asXml(), 'The exporter supports method chaining');

		$this->assertSame('xml', TestHelper::getValue($exporter, 'asFormat'));
	}

	/**
	 * Data provider for from test cases
	 *
	 * @return  \Generator
	 */
	public function dataFrom(): \Generator
	{
		yield 'single table' => [
			'#__dbtest',
			false,
		];

		yield 'multiple tables' => [
			['#__content', '#__dbtest'],
			false,
		];

		yield 'incorrect table data type' => [
			new \stdClass,
			true,
		];
	}

	/**
	 * @testdox  The tables to be exported can be configured
	 *
	 * @param   string[]|string  $from                  The name of a single table, or an array of the table names to export.
	 * @param   boolean          $shouldRaiseException  Flag indicating the exporter should raise an exception for an unsupported data type
	 *
	 * @dataProvider  dataFrom
	 */
	public function testFrom($from, bool $shouldRaiseException)
	{
		if ($shouldRaiseException)
		{
			$this->expectException(\InvalidArgumentException::class);
		}

		/** @var DatabaseExporter|MockObject $exporter */
		$exporter = $this->getMockForAbstractClass(DatabaseExporter::class);

		$this->assertSame($exporter, $exporter->from($from), 'The exporter supports method chaining');

		$this->assertSame((array) $from, TestHelper::getValue($exporter, 'from'));
	}

	/**
	 * @testdox  A database drier can be set to the exporter
	 */
	public function testSetDbo()
	{
		/** @var DatabaseExporter|MockObject $exporter */
		$exporter = $this->getMockForAbstractClass(DatabaseExporter::class);

		/** @var DatabaseInterface|MockObject $db */
		$db = $this->createMock(DatabaseInterface::class);

		$this->assertSame($exporter, $exporter->setDbo($db), 'The exporter supports method chaining');
	}

	/**
	 * @testdox  The exporter can be configured to export with structure
	 */
	public function testWithStructure()
	{
		/** @var DatabaseExporter|MockObject $exporter */
		$exporter = $this->getMockForAbstractClass(DatabaseExporter::class);

		$this->assertSame($exporter, $exporter->withStructure(false), 'The exporter supports method chaining');

		$options = TestHelper::getValue($exporter, 'options');

		$this->assertFalse($options->withStructure);
	}

	/**
	 * @testdox  The exporter can be configured to export with data
	 */
	public function testWithData()
	{
		/** @var DatabaseExporter|MockObject $exporter */
		$exporter = $this->getMockForAbstractClass(DatabaseExporter::class);

		$this->assertSame($exporter, $exporter->withData(true), 'The exporter supports method chaining');

		$options = TestHelper::getValue($exporter, 'options');

		$this->assertTrue($options->withData);
	}
}
