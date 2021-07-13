<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Pgsql;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Pgsql\PgsqlDriver;
use Joomla\Database\Pgsql\PgsqlImporter;
use Joomla\Database\Pgsql\PgsqlQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Pgsql\PgsqlImporter.
 */
class PgsqlImporterTest extends TestCase
{
	/**
	 * Mock database driver
	 *
	 * @var  MockObject|PgsqlDriver
	 */
	private $db;

	/**
	 * A list of the executed inserted objects for a test case
	 *
	 * @var  string[]
	 */
	private $executedInsertObjects = [];

	/**
	 * A list of the executed queries for a test case
	 *
	 * @var  string[]
	 */
	private $executedQueries = [];

	/**
	 * Selected sample data for tests.
	 *
	 * @var  string[]
	 */
	protected $sample = [
		'xml-id-field'    =>
			'<field Field="id" Type="int(11) unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" />',
		'xml-title-field' =>
			'<field Field="title" Type="varchar(50)" Null="NO" Key="" Default="" Extra="" />',
		'xml-body-field'  =>
			'<field Field="body" Type="mediumtext" Null="NO" Key="" Default="" Extra="" />',
		'xml-primary-key' =>
			'<key Table="#__dbtest" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Null="" Index_type="BTREE" Comment="" />',
	];

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
						'Null'        => 'NO',
						'Default'     => 'nextval(\'jos_dbtest_id_seq\'::regclass)',
						'comments'    => '',
					],
					'title'       => (object) [
						'column_name' => 'title',
						'type'        => 'character varying(50)',
						'Type'        => 'character varying(50)',
						'Null'        => 'NO',
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
						'Index'      => 'jos_dbtest_pkey',
						'is_primary' => 'TRUE',
						'is_unique'  => 'TRUE',
						'Key_name'   => 'id',
						'Query'      => 'ALTER TABLE jos_dbtest ADD PRIMARY KEY (id)',
					],
				]
			);

		$this->db->expects($this->any())
			->method('getTableList')
			->willReturn(
				[
					'jos_dbtest',
				]
			);

		$this->db->expects($this->any())
			->method('getTableSequences')
			->willReturn(
				[
					(object) [
						'Name'         => 'jos_dbtest_id_seq',
						'Schema'       => 'public',
						'Table'        => 'jos_dbtest',
						'Column'       => 'id',
						'Type'         => 'bigint',
						'Start_Value'  => '1',
						'Min_Value'    => '1',
						'Max_Value'    => '9223372036854775807',
						'Increment'    => '1',
						'Cycle_option' => 'NO',
					],
				]
			);

		$this->db->expects($this->any())
			->method('insertObject')
			->willReturnCallback(
				function ($table, &$object, $key = null) {
					if (!isset($this->executedInsertObjects[$table]))
					{
						$this->executedInsertObjects[$table] = [];
					}

					$this->executedInsertObjects[$table][] = $object;

					return true;
				}
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

		$this->db->expects($this->any())
			->method('quote')
			->willReturnCallback(
				function ($text, $escape = true) {
					if (is_string($text))
					{
						return "'$text'";
					}

					$fields = [];

					foreach ($text as $value)
					{
						$fields[] = "'$value'";
					}

					return $fields;
				}
			);

		$this->db->expects($this->any())
			->method('setQuery')
			->willReturnCallback(
				function ($query, $offset = 0, $limit = 0) {
					$this->executedQueries[] = $query;

					return $this->db;
				}
			);
	}

	/**
	 * This method is called after each test.
	 */
	protected function tearDown(): void
	{
		$this->expectedInsertObjects = [];
		$this->executedQueries       = [];
	}

	/**
	 * Data provider for import test cases
	 *
	 * @return  \Generator
	 */
	public function dataImport(): \Generator
	{
		$idSequence = '<sequence Name="jos_dbtest_id_seq" Schema="public" Table="jos_dbtest" Column="id" Type="bigint" Start_Value="1" Min_Value="1" Max_Value="9223372036854775807" Increment="1" Cycle_option="NO" />';

		$idField    = '<field Field="id" Type="integer" Null="NO" Default="nextval(\'jos_dbtest_id_seq\'::regclass)" Comments="" />';
		$titleField = '<field Field="title" Type="character varying(50)" Null="NO" Default="NULL" Comments="" />';
		$aliasField = '<field Field="alias" Type="character varying(255)" Null="NO" Default="test" Comments="" />';

		$idKey    = '<key Index="jos_dbtest_pkey" is_primary="TRUE" is_unique="TRUE" Key_name="id" Query="ALTER TABLE jos_dbtest ADD PRIMARY KEY (id)" />';
		$titleKey = '<key Index="jos_dbtest_idx_name" is_primary="FALSE" is_unique="FALSE" Key_name="name" Query="CREATE INDEX jos_dbtest_idx_name ON jos_dbtest USING btree (name)" />';

		yield 'no changes in existing structure' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idSequence . $idField . $titleField . $idKey . '</table_structure></database></dump>'),
			[],
			[]
		];

		yield 'inserts row into database' => [
			true,
			true,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idSequence . $idField . $titleField . $idKey . '</table_structure>  <table_data name="#__dbtest"><row><field name="id">1</field><field name="title">Testing</field></row></table_data></database></dump>'),
			[],
			[
				'jos_dbtest' => [
					(object) [
						'id'    => '1',
						'title' => 'Testing',
					],
				],
			],
		];

		yield 'adds alias column to the structure' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idSequence . $idField . $titleField . $aliasField . $idKey . '</table_structure></database></dump>'),
			[
				"ALTER TABLE \"jos_dbtest\" ADD COLUMN \"alias\" character varying(255) NOT NULL DEFAULT 'test'",
			],
			[],
		];

		yield 'adds key for the title column to the structure' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idSequence . $idField . $titleField . $idKey . $titleKey . '</table_structure></database></dump>'),
			[
				'CREATE INDEX jos_dbtest_idx_name ON jos_dbtest USING btree (name)',
			],
			[],
		];

		yield 'removes the title column from the structure' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idSequence . $idField . $idKey . '</table_structure></database></dump>'),
			[
				'ALTER TABLE "jos_dbtest" DROP COLUMN "title"',
			],
			[],
		];

		yield 'removes the primary key based on the id column from the structure' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idSequence . $idField . $titleField . '</table_structure></database></dump>'),
			[
				'ALTER TABLE ONLY "jos_dbtest" DROP CONSTRAINT "jos_dbtest_pkey"',
			],
			[],
		];

		yield 'adds a new database table' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idSequence . $idField . $titleField . $idKey . '</table_structure><table_structure name="jos_newtest"><sequence Name="jos_newtest_id_seq" Schema="public" Table="jos_newtest" Column="id" Type="bigint" Start_Value="1" Min_Value="1" Max_Value="9223372036854775807" Increment="1" Cycle_option="NO" /><field Field="id" Type="integer" Null="NO" Default="nextval(\'jos_newtest_id_seq\'::regclass)" Comments="" />' . $titleField . '<key Index="jos_newtest_pkey" is_primary="TRUE" is_unique="TRUE" Key_name="id" Query="ALTER TABLE jos_newtest ADD PRIMARY KEY (id)" /></table_structure></database></dump>'),
			[
				'CREATE TABLE "jos_newtest" ("id" SERIAL, "title" character varying(50) NOT NULL DEFAULT \'NULL\')',
				'CREATE SEQUENCE IF NOT EXISTS jos_newtest_id_seq INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 NO CYCLE OWNED BY "public.jos_newtest.id"',
				"SELECT setval('jos_newtest_id_seq', , FALSE)",
				'ALTER TABLE jos_newtest ADD PRIMARY KEY (id)',
			],
			[],
		];

		yield 'changes the field type of the id field' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idSequence . '<field Field="id" Type="bigint" Null="NO" Default="nextval(\'jos_dbtest_id_seq\'::regclass)" Comments="" />' . $titleField . $idKey . '</table_structure></database></dump>'),
			[
				'ALTER TABLE "jos_dbtest" ALTER COLUMN "id"  TYPE bigint,
ALTER COLUMN "id" SET NOT NULL,
ALTER COLUMN "id" SET DEFAULT \'nextval(\'jos_dbtest_id_seq\'::regclass)\';
ALTER SEQUENCE "jos_dbtest_id_seq" OWNED BY "jos_dbtest.id"',
			],
			[],
		];
	}

	/**
	 * @testdox  The importer processes a XML document
	 *
	 * @param   boolean            $mergeStructure         True to merge the structure.
	 * @param   boolean            $importData             True to import the data.
	 * @param   \SimpleXMLElement  $from                   XML document to import.
	 * @param   string[]           $expectedQueries        The expected database queries to perform.
	 * @param   string[]           $expectedInsertObjects  The expected objects to be given to the database's insertObject method.
	 *
	 * @dataProvider  dataImport
	 */
	public function testImport(bool $mergeStructure, bool $importData, \SimpleXMLElement $from, array $expectedQueries, array $expectedInsertObjects)
	{
		$importer = new PgsqlImporter;
		$importer->setDbo($this->db);
		$importer->from($from);

		if ($mergeStructure)
		{
			$importer->mergeStructure();
		}

		if ($importData)
		{
			$importer->importData();
		}

		$this->assertEquals($expectedQueries, $this->executedQueries);
		$this->assertEquals($expectedInsertObjects, $this->executedInsertObjects);
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
			new \SimpleXMLElement('<table_structure name="#__dbtest" />'),
			'Database connection wrong type.',
		];

		yield 'fails checks with no database driver' => [
			null,
			new \SimpleXMLElement('<table_structure name="#__dbtest" />'),
			'Database connection wrong type.',
		];

		yield 'fails checks with no tables' => [
			$this->createMock(PgsqlDriver::class),
			null,
			'ERROR: No Tables Specified',
		];
	}

	/**
	 * @testdox  The importer checks for errors
	 *
	 * @param   DatabaseInterface|null  $db                Database driver to set in the importer.
	 * @param   string[]|string|null    $from              Database structure to import.
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

		$importer = new PgsqlImporter;

		if ($db)
		{
			$importer->setDbo($db);
		}

		if ($from)
		{
			$importer->from($from);
		}

		$this->assertSame($importer, $importer->check(), 'The importer supports method chaining');
	}
}
