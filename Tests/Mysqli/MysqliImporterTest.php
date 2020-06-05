<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Mysqli\MysqliDriver;
use Joomla\Database\Mysqli\MysqliImporter;
use Joomla\Database\Mysqli\MysqliQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Mysqli\MysqliImporter.
 */
class MysqliImporterTest extends TestCase
{
	/**
	 * Mock database driver
	 *
	 * @var  MockObject|MysqliDriver
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
						'Table'        => 'jos_dbtest',
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
			->method('getTableList')
			->willReturn(
				[
					'jos_dbtest',
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
		$idField    = '<field Field="id" Type="int(11) unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" />';
		$titleField = '<field Field="title" Type="varchar(255)" Null="NO" Key="" Default="" Extra="" />';
		$aliasField = '<field Field="alias" Type="varchar(255)" Null="NO" Key="" Default="" Extra="" />';

		$idKey    = '<key Table="#__dbtest" Non_unique="0" Key_name="PRIMARY" Seq_in_index="1" Column_name="id" Collation="A" Null="" Index_type="BTREE" Comment="" />';
		$titleKey = '<key Table="#__dbtest" Non_unique="0" Key_name="idx_title" Seq_in_index="1" Column_name="title" Collation="A" Null="" Index_type="BTREE" Comment="" />';

		yield 'no changes in existing structure' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idField . $titleField . $idKey . '</table_structure></database></dump>'),
			[],
			[]
		];

		yield 'inserts row into database' => [
			true,
			true,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idField . $titleField . $idKey . '</table_structure>  <table_data name="#__dbtest"><row><field name="id">1</field><field name="title">Testing</field></row></table_data></database></dump>'),
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
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idField . $titleField . $aliasField . $idKey . '</table_structure></database></dump>'),
			[
				"ALTER TABLE `jos_dbtest` ADD COLUMN `alias` varchar(255) NOT NULL DEFAULT ''",
			],
			[],
		];

		yield 'adds key for the title column to the structure' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idField . $titleField . $idKey . $titleKey . '</table_structure></database></dump>'),
			[
				'ALTER TABLE `jos_dbtest` ADD UNIQUE KEY `idx_title` (`title`)',
			],
			[],
		];

		yield 'removes the title column from the structure' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idField . $idKey . '</table_structure></database></dump>'),
			[
				'ALTER TABLE `jos_dbtest` DROP COLUMN `title`',
			],
			[],
		];

		yield 'removes the primary key based on the id column from the structure' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idField . $titleField . '</table_structure></database></dump>'),
			[
				'ALTER TABLE `jos_dbtest` DROP PRIMARY KEY',
			],
			[],
		];

		yield 'adds a new database table' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest">' . $idField . $titleField . $idKey . '</table_structure><table_structure name="#__newtest">' . $idField . $titleField . $idKey . '</table_structure></database></dump>'),
			[
				"CREATE TABLE `#__newtest` (`id` int(11) unsigned NOT NULL DEFAULT '' AUTO_INCREMENT, `title` varchar(255) NOT NULL DEFAULT '', PRIMARY KEY  (`id`))",
			],
			[],
		];

		yield 'changes the field type of the id field' => [
			true,
			false,
			new \SimpleXMLElement('<dump><database name=""><table_structure name="#__dbtest"><field Field="id" Type="bigint() unsigned" Null="NO" Key="PRI" Default="" Extra="auto_increment" />' . $titleField . $idKey . '</table_structure></database></dump>'),
			[
				"ALTER TABLE `jos_dbtest` CHANGE COLUMN `id` `id` bigint() unsigned NOT NULL DEFAULT '' AUTO_INCREMENT",
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
		$importer = new MysqliImporter;
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
			$this->createMock(MysqliDriver::class),
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
			$this->createMock(MysqliDriver::class),
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

		$importer = new MysqliImporter;

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
