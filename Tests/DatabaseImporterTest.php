<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseImporter;
use Joomla\Database\DatabaseInterface;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\DatabaseImporter
 */
class DatabaseImporterTest extends TestCase
{
	/**
	 * @testdox  The importer is correctly configured when instantiated
	 */
	public function testInstantiation()
	{
		/** @var DatabaseImporter|MockObject $importer */
		$importer = $this->getMockForAbstractClass(DatabaseImporter::class);

		$expected = (object) [
			'withStructure' => true,
		];

		$this->assertEquals($expected, TestHelper::getValue($importer, 'options'));
		$this->assertSame('xml', TestHelper::getValue($importer, 'asFormat'));
	}

	/**
	 * @testdox  The importer can be set to XML format
	 */
	public function testAsXml()
	{
		/** @var DatabaseImporter|MockObject $importer */
		$importer = $this->getMockForAbstractClass(DatabaseImporter::class);

		$this->assertSame($importer, $importer->asXml(), 'The importer supports method chaining');

		$this->assertSame('xml', TestHelper::getValue($importer, 'asFormat'));
	}

	/**
	 * @testdox  A database drier can be set to the importer
	 */
	public function testSetDbo()
	{
		/** @var DatabaseImporter|MockObject $importer */
		$importer = $this->getMockForAbstractClass(DatabaseImporter::class);

		/** @var DatabaseInterface|MockObject $db */
		$db = $this->createMock(DatabaseInterface::class);

		$this->assertSame($importer, $importer->setDbo($db), 'The importer supports method chaining');
	}

	/**
	 * @testdox  The importer can be configured to export with structure
	 */
	public function testWithStructure()
	{
		/** @var DatabaseImporter|MockObject $importer */
		$importer = $this->getMockForAbstractClass(DatabaseImporter::class);

		$this->assertSame($importer, $importer->withStructure(false), 'The importer supports method chaining');

		$options = TestHelper::getValue($importer, 'options');

		$this->assertFalse($options->withStructure);
	}
}
