<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Pgsql;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Pgsql\PgsqlDriver;
use Joomla\Database\Pgsql\PgsqlExporter;
use Joomla\Database\Pgsql\PgsqlQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Pgsql\PgsqlExporter.
 */
class PgsqlExporterTest extends TestCase
{
	/**
	 * Mock database driver
	 *
	 * @var  MockObject|PgsqlDriver
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

		$this->db = $this->createMock(PgsqlDriver::class);

		$this->db->expects($this->any())
			->method('getPrefix')
			->willReturn('jos_');

		$this->db->expects($this->any())
			->method('getQuery')
			->willReturnCallback(function () {
				return new PgsqlQuery($this->db);
			});

		$this->db->expects($this->any())
			->method('getTableColumns')
			->willReturn(
				[
					'id'          => (object) [
						'column_name' => 'id',
						'type'        => 'integer',
						'Type'        => 'integer',
						'null'        => 'NO',
						'Default'     => 'nextval(\'jos_dbtest_id_seq\'::regclass)',
						'comments'    => '',
					],
					'title'       => (object) [
						'column_name' => 'title',
						'type'        => 'character varying(50)',
						'Type'        => 'character varying(50)',
						'null'        => 'NO',
						'Default'     => 'NULL',
						'comments'    => '',
					],
					'start_date'  => (object) [
						'column_name' => 'start_date',
						'type'        => 'timestamp without time zone',
						'Type'        => 'timestamp without time zone',
						'null'        => 'NO',
						'Default'     => 'NULL',
						'comments'    => '',
					],
					'description' => (object) [
						'column_name' => 'description',
						'type'        => 'text',
						'Type'        => 'text',
						'null'        => 'NO',
						'Default'     => 'NULL',
						'comments'    => '',
					],
				]
			);

		$this->db->expects($this->any())
			->method('getTableKeys')
			->willReturn(
				[
					(object) [
						'idxName'   => 'jos_dbtest_pkey',
						'isPrimary' => 'TRUE',
						'isUnique'  => 'TRUE',
						'indKey'    => '1',
						'Query'     => 'ALTER TABLE "jos_dbtest" ADD PRIMARY KEY (id)',
					],
				]
			);

		$this->db->expects($this->any())
			->method('getTableSequences')
			->willReturn(
				[
					(object) [
						'sequence'      => 'jos_dbtest_id_seq',
						'schema'        => 'public',
						'table'         => 'jos_dbtest',
						'column'        => 'id',
						'data_type'     => 'bigint',
						'start_value'   => '1',
						'minimum_value' => '1',
						'maximum_value' => '9223372036854775807',
						'increment'     => '1',
						'cycle_option'  => 'NO',
					],
				]
			);

		$this->db->expects($this->any())
			->method('quoteName')
			->willReturnCallback(
				function ($name, $as = null) {
					if (is_string($name))
					{
						return '"' . $name . '"';
					}

					$fields = [];

					foreach ($name as $value)
					{
						$fields[] = '"' . $value . '"';
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
<postgresqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
 <database name="">
 </database>
</postgresqldump>
XML,
		];

		yield 'with only structure' => [
			true,
			false,
			<<<XML
<postgresqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <database name="">
    <table_structure name="#__dbtest">
     <sequence column="id" cycle_option="no" increment="1" is_called="" last_value="" max_value="9223372036854775807" min_value="1" name="#__dbtest_id_seq" schema="public" start_value="1" table="#__dbtest" type="bigint"/>
     <field comments="" default="nextval('jos_dbtest_id_seq'::regclass)" field="id" null="no" type="integer"/>
     <field comments="" default="null" field="title" null="no" type="character varying(50)"/>
     <field comments="" default="null" field="start_date" null="no" type="timestamp without time zone"/>
     <field comments="" default="null" field="description" null="no" type="text"/>
     <key Index="#__dbtest_pkey" is_primary="true" is_unique="true" Key_name="" Query="alter table &quot;jos_dbtest&quot; add primary key (id)"/>
    </table_structure>
  </database>
</postgresqldump>
XML,
		];

		yield 'with only data' => [
			false,
			true,
			<<<XML
<postgresqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <database name="">
    <table_data name="#__dbtest">
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
</postgresqldump>
XML,
		];

		yield 'with structure and data' => [
			true,
			true,
			<<<XML
<postgresqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <database name="">
    <table_structure name="#__dbtest">
      <sequence column="id" cycle_option="no" increment="1" is_called="" last_value="" max_value="9223372036854775807" min_value="1" name="#__dbtest_id_seq" schema="public" start_value="1" table="#__dbtest" type="bigint"/>
      <field comments="" default="nextval('jos_dbtest_id_seq'::regclass)" field="id" null="no" type="integer"/>
      <field comments="" default="null" field="title" null="no" type="character varying(50)"/>
      <field comments="" default="null" field="start_date" null="no" type="timestamp without time zone"/>
      <field comments="" default="null" field="description" null="no" type="text"/>
      <key Index="#__dbtest_pkey" is_primary="true" is_unique="true" Key_name="" Query="alter table &quot;jos_dbtest&quot; add primary key (id)"/>
    </table_structure>
    <table_data name="#__dbtest">
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
</postgresqldump>
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
		$exporter = new PgsqlExporter;

		$exporter->setDbo($this->db)
			->from('jos_dbtest')
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
			$this->createMock(PgsqlDriver::class),
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
			$this->createMock(PgsqlDriver::class),
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

		$exporter = new PgsqlExporter;

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
