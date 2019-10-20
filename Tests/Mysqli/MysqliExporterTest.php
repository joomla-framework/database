<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Mysqli\MysqliDriver;
use Joomla\Database\Mysqli\MysqliExporter;
use Joomla\Database\Mysqli\MysqliQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Mysqli\MysqliExporter.
 */
class MysqliExporterTest extends TestCase
{
	/**
	 * Mock database driver
	 *
	 * @var  MockObject|MysqliDriver
	 */
	private $db;

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	public function setup()
	{
		parent::setUp();

		$this->db = $this->createMock(MysqliDriver::class);

		$this->db->expects($this->any())
			->method('getPrefix')
			->willReturn('jos_');

		$this->db->expects($this->any())
			->method('getQuery')
			->willReturnCallback(function () {
				return new MysqliQuery($this->db);
			});

		$this->db->expects($this->any())
			->method('getTableColumns')
			->willReturn(
				[
					'id'    => (object) [
						'Field'      => 'id',
						'Type'       => 'int(11) unsigned',
						'Collation'  => null,
						'Null'       => 'NO',
						'Key'        => 'PRI',
						'Default'    => '',
						'Extra'      => 'auto_increment',
						'Privileges' => 'select,insert,update,references',
						'Comment'    => '',
					],
					'title' => (object) [
						'Field'      => 'title',
						'Type'       => 'varchar(255)',
						'Collation'  => 'utf8_general_ci',
						'Null'       => 'NO',
						'Key'        => '',
						'Default'    => '',
						'Extra'      => '',
						'Privileges' => 'select,insert,update,references',
						'Comment'    => '',
					],
				]
			);

		$this->db->expects($this->any())
			->method('getTableKeys')
			->willReturn(
				[
					(object) [
						'Table'        => 'jos_test',
						'Non_unique'   => '0',
						'Key_name'     => 'PRIMARY',
						'Seq_in_index' => '1',
						'Column_name'  => 'id',
						'Collation'    => 'A',
						'Cardinality'  => '2695',
						'Sub_part'     => '',
						'Packed'       => '',
						'Null'         => '',
						'Index_type'   => 'BTREE',
						'Comment'      => '',
					],
				]
			);

		$this->db->expects($this->any())
			->method('quoteName')
			->willReturnCallback(
				function ($name, $as = null) {
					if (is_string($name))
					{
						return "`$name`";
					}

					$fields = [];

					foreach ($name as $value)
					{
						$fields[] = "`$value`";
					}

					return $fields;
				}
			);
	}

	/**
	 * Data provider for string casting test cases
	 *
	 * @return  \Generator
	 */
	public function dataCastingToString(): \Generator
	{
		yield 'without structure or data' => [
			false,
			false,
			<<<XML
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
 <database name="">
 </database>
</mysqldump>
XML,
		];

		yield 'with only structure' => [
			true,
			false,
			<<<XML
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <database name="">
    <table_structure name="#__test">
     <field Field="id" Type="int(11) unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" />
     <field Field="title" Type="varchar(255)" Null="NO" Key="" Default="" Extra="" />
     <key Table="#__test" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Null="" Index_type="BTREE" Sub_part="" Comment="" />
    </table_structure>
  </database>
</mysqldump>
XML,
		];

		yield 'with only data' => [
			false,
			true,
			<<<XML
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <database name="">
    <table_data name="#__test">
      <row>
        <field name="id">1</field>
        <field name="title">Row 1</field>
      </row>
      <row>
        <field name="id">2</field>
        <field name="title">Row 2</field>
      </row>
    </table_data>
  </database>
</mysqldump>
XML,
		];

		yield 'with structure and data' => [
			true,
			true,
			<<<XML
<mysqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <database name="">
    <table_structure name="#__test">
      <field Field="id" Type="int(11) unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" />
      <field Field="title" Type="varchar(255)" Null="NO" Key="" Default="" Extra="" />
      <key Table="#__test" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Null="" Index_type="BTREE" Sub_part="" Comment="" />
    </table_structure>
    <table_data name="#__test">
      <row>
        <field name="id">1</field>
        <field name="title">Row 1</field>
      </row>
      <row>
        <field name="id">2</field>
        <field name="title">Row 2</field>
      </row>
    </table_data>
  </database>
</mysqldump>
XML,
		];
	}

	/**
	 * @testdox  The exporter can be cast to a string
	 *
	 * @param   boolean  $withStructure  True to export the structure, false to not.
	 * @param   boolean  $withData       True to export the data, false to not.
	 * @param   string   $expectedXml    Expected XML string.
	 *
	 * @dataProvider  dataCastingToString
	 */
	public function testCastingToString(bool $withStructure, bool $withData, string $expectedXml)
	{
		$exporter = new MysqliExporter;

		$exporter->setDbo($this->db)
			->from('jos_test')
			->withStructure($withStructure)
			->withData($withData);

		if ($withData)
		{
			$this->db->expects($this->once())
				->method('loadObjectList')
				->willReturn(
					[
						(object) [
							'id'    => 1,
							'title' => 'Row 1',
						],
						(object) [
							'id'    => 2,
							'title' => 'Row 2',
						],
					]
				);
		}

		$this->assertXmlStringEqualsXmlString($expectedXml, (string) $exporter);
	}

	/**
	 * Data provider for check test cases
	 *
	 * @return  \Generator
	 */
	public function dataCheck(): \Generator
	{
		yield 'passes checks' => [
			$this->createMock(MysqliDriver::class),
			'#__dbtest',
			null,
		];

		yield 'fails checks with incorrect database driver subclass' => [
			$this->createMock(DatabaseInterface::class),
			'#__dbtest',
			'Database connection wrong type.',
		];

		yield 'fails checks with no database driver' => [
			null,
			'#__dbtest',
			'Database connection wrong type.',
		];

		yield 'fails checks with no tables' => [
			$this->createMock(MysqliDriver::class),
			null,
			'ERROR: No Tables Specified',
		];
	}

	/**
	 * @testdox  The exporter checks for errors
	 *
	 * @param   DatabaseInterface|null  $db                Database driver to set in the exporter.
	 * @param   string[]|string|null    $from              Database tables to export from.
	 * @param   string|null             $exceptionMessage  If an Exception should be thrown, the expected message
	 *
	 * @dataProvider  dataCheck
	 */
	public function testCheck(?DatabaseInterface $db, $from, ?string $exceptionMessage)
	{
		if ($exceptionMessage)
		{
			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessage($exceptionMessage);
		}

		$exporter = new MysqliExporter;

		if ($db)
		{
			$exporter->setDbo($db);
		}

		if ($from)
		{
			$exporter->from($from);
		}

		$this->assertSame($exporter, $exporter->check(), 'The exporter supports method chaining');
	}
}
