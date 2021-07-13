<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlsrv;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Exception\UnsupportedAdapterException;
use Joomla\Database\Sqlsrv\SqlsrvDriver;
use Joomla\Database\Sqlsrv\SqlsrvQuery;
use Joomla\Database\Tests\AbstractDatabaseDriverTestCase;

/**
 * Test class for Joomla\Database\Sqlsrv\SqlsrvDriver.
 */
class SqlsrvDriverTest extends AbstractDatabaseDriverTestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		if (!static::$connection || static::$connection->getName() !== 'sqlsrv')
		{
			self::markTestSkipped('SQL Server database not configured.');
		}
	}

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

		try
		{
			foreach (DatabaseDriver::splitSql(file_get_contents(dirname(__DIR__) . '/Stubs/Schema/sqlsrv.sql')) as $query)
			{
				static::$connection->setQuery($query)
					->execute();
			}
		}
		catch (ExecutionFailureException $exception)
		{
			$this->markTestSkipped(
				\sprintf(
					'Could not load SQL Server database: %s',
					$exception->getMessage()
				)
			);
		}
	}

	/**
	 * Tears down the fixture.
	 *
	 * This method is called after a test is executed.
	 */
	protected function tearDown(): void
	{
		foreach (static::$connection->getTableList() as $table)
		{
			static::$connection->dropTable($table);
		}
	}

	/**
	 * Loads the example data into the database.
	 *
	 * @return  void
	 *
	 * @note    Overridden to not insert IDs
	 */
	protected function loadExampleData(): void
	{
		$data = [
			(object) [
				// 'id'          => 1,
				'title'       => 'Testing1',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row one',
			],
			(object) [
				// 'id'          => 2,
				'title'       => 'Testing2',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row two',
			],
			(object) [
				// 'id'          => 3,
				'title'       => 'Testing3',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row three',
			],
			(object) [
				// 'id'          => 4,
				'title'       => 'Testing4',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row four',
			],
		];

		foreach ($data as $row)
		{
			static::$connection->insertObject('#__dbtest', $row);
		}
	}

	/*
	 * Overrides for data providers from the parent test case
	 */

	/**
	 * Data provider for escaping test cases
	 *
	 * @return  \Generator
	 */
	public function dataEscape(): \Generator
	{
		yield ["'%_abc123", false, '\'\'%_abc123'];
		yield ["'%_abc123", true, '\'\'[%][_]abc123'];
		yield [3, false, 3];
		yield [3.14, false, '3.14'];
	}

	/**
	 * Data provider for fetching table column test cases
	 *
	 * @return  \Generator
	 */
	public function dataGetTableColumns(): \Generator
	{
		yield 'only column types' => [
			'#__dbtest',
			true,
			[
				'id'          => 'int',
				'title'       => 'nvarchar',
				'start_date'  => 'datetime',
				'description' => 'nvarchar',
				'data'        => 'nvarchar',
			],
		];

		yield 'full column information' => [
			'#__dbtest',
			false,
			[
				'id'          => (object) [
					'Field'      => 'id',
					'Type'       => 'int',
					'Null'       => 'NO',
					'Default'    => '',
				],
				'title'       => (object) [
					'Field'      => 'title',
					'Type'       => 'nvarchar',
					'Null'       => 'NO',
					'Default'    => '',
				],
				'start_date'  => (object) [
					'Field'      => 'start_date',
					'Type'       => 'datetime',
					'Null'       => 'NO',
					'Default'    => '',
				],
				'description' => (object) [
					'Field'      => 'description',
					'Type'       => 'nvarchar',
					'Null'       => 'NO',
					'Default'    => '',
				],
				'data'        => (object) [
					'Field'      => 'data',
					'Type'       => 'nvarchar',
					'Null'       => 'YES',
					'Default'    => '',
				],
			],
		];
	}

	/**
	 * Data provider for binary quoting test cases
	 *
	 * @return  \Generator
	 */
	public function dataQuoteBinary(): \Generator
	{
		yield ['DATA', "0x" . bin2hex('DATA')];
		yield ["\x00\x01\x02\xff", "0x000102ff"];
		yield ["\x01\x01\x02\xff", "0x010102ff"];
	}

	/**
	 * Data provider for name quoting test cases
	 *
	 * @return  \Generator
	 */
	public function dataQuoteName(): \Generator
	{
		yield ['protected`title', null, '[protected`title]'];
		yield ['protected"title', null, '[protected"title]'];
		yield ['protected]title', null, '[protected]]title]'];
	}

	/*
	 * Overrides for parent class test cases
	 */

	/**
	 * @testdox  The first row of a result set can be loaded as a PHP object
	 *
	 * @note This test case is an override from the parent because SQL Server casts the key to integers and has millisecond precision
	 */
	public function testLoadObject()
	{
		$this->loadExampleData();

		$result = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
		)->loadObject();

		$expected = (object) [
			'id'          => 1,
			'title'       => 'Testing1',
			'start_date'  => '2019-10-26 00:00:00.000',
			'description' => 'test row one',
			'data'        => null,
		];

		$this->assertEquals($expected, $result);
	}

	/**
	 * @testdox  All rows of a result set can be loaded as PHP objects
	 *
	 * @note This test case is an override from the parent because SQL Server casts the key to integers and has millisecond precision
	 */
	public function testLoadObjectList()
	{
		$this->loadExampleData();

		$result = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
		)->loadObjectList();

		$expected = [
			(object) [
				'id'          => 1,
				'title'       => 'Testing1',
				'start_date'  => '2019-10-26 00:00:00.000',
				'description' => 'test row one',
				'data'        => null,
			],
			(object) [
				'id'          => 2,
				'title'       => 'Testing2',
				'start_date'  => '2019-10-26 00:00:00.000',
				'description' => 'test row two',
				'data'        => null,
			],
			(object) [
				'id'          => 3,
				'title'       => 'Testing3',
				'start_date'  => '2019-10-26 00:00:00.000',
				'description' => 'test row three',
				'data'        => null,
			],
			(object) [
				'id'          => 4,
				'title'       => 'Testing4',
				'start_date'  => '2019-10-26 00:00:00.000',
				'description' => 'test row four',
				'data'        => null,
			],
		];

		$this->assertEquals($expected, $result);
	}

	/**
	 * @testdox  The first row of a result set can be loaded as an array
	 *
	 * @note This test case is an override from the parent because SQL Server casts the key to integers and has millisecond precision
	 */
	public function testLoadRow()
	{
		$this->loadExampleData();

		$result = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
		)->loadRow();

		$expected = [
			'1',
			'Testing1',
			'2019-10-26 00:00:00.000',
			'test row one',
			null,
		];

		$this->assertEquals($expected, $result);
	}

	/**
	 * @testdox  All rows of a result set can be loaded as an array
	 *
	 * @note This test case is an override from the parent because SQL Server casts the key to integers and has millisecond precision
	 */
	public function testLoadRowList()
	{
		$this->loadExampleData();

		$result = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
		)->loadRowList();

		$expected = [
			[
				1,
				'Testing1',
				'2019-10-26 00:00:00.000',
				'test row one',
				null,
			],
			[
				2,
				'Testing2',
				'2019-10-26 00:00:00.000',
				'test row two',
				null,
			],
			[
				3,
				'Testing3',
				'2019-10-26 00:00:00.000',
				'test row three',
				null,
			],
			[
				4,
				'Testing4',
				'2019-10-26 00:00:00.000',
				'test row four',
				null,
			],
		];

		$this->assertEquals($expected, $result);
	}

	/**
	 * @testdox  Queries converted to the querySet type are correctly built and executed
	 *
	 * @note This test case is an override from the parent because the result set has more rows
	 */
	public function testSelectToQuerySetWithUnionAll()
	{
		$this->loadExampleData();

		$query = static::$connection->getQuery(true);
		$union = static::$connection->getQuery(true);

		$query = $query->select('id, title')
			->from('#__dbtest')
			->where('id = 4')
			->setLimit(1)
			->toQuerySet();

		$union->select('id, title')
			->from('#__dbtest')
			->where('id < 4')
			->order('id DESC')
			->setLimit(2, 1);

		$query->unionAll($union)
			->order('id');

		$result = static::$connection->setQuery($query)->loadAssocList();

		$this->assertEquals(
			[
				['id' => '1', 'title' => 'Testing1'],
				['id' => '2', 'title' => 'Testing2'],
				['id' => '4', 'title' => 'Testing4'],
			],
			$result
		);
	}

	/*
	 * Test cases for this subclass
	 */

	/**
	 * @testdox  The database driver reports if it is supported in the present environment
	 */
	public function testIsSupported()
	{
		$this->assertTrue(
			SqlsrvDriver::isSupported()
		);
	}

	/**
	 * @testdox  The database collation can be retrieved
	 */
	public function testGetCollation()
	{
		$this->assertSame(
			'MSSQL UTF-8 (UCS2)',
			static::$connection->getCollation()
		);
	}

	/**
	 * @testdox  The database connection collation can be retrieved
	 */
	public function testGetConnectionCollation()
	{
		$this->assertSame(
			'MSSQL UTF-8 (UCS2)',
			static::$connection->getConnectionCollation()
		);
	}

	/**
	 * @testdox  The database connection encryption can be retrieved
	 */
	public function testGetConnectionEncryption()
	{
		$this->assertEmpty(
			static::$connection->getConnectionEncryption(),
			'The database connection is not encrypted by default'
		);
	}

	/**
	 * @testdox  A list of queries to create the given tables is returned
	 */
	public function testGetTableCreate()
	{
		$this->assertEmpty(
			static::$connection->getTableCreate('#__dbtest'),
			'Retrieving the queries to create a list of tables is not supported in SQL Server'
		);
	}

	/**
	 * @testdox  Information about the columns of a database table is returned
	 *
	 * @param   string   $table     The name of the database table.
	 * @param   boolean  $typeOnly  True (default) to only return field types.
	 * @param   array    $expected  Expected result.
	 *
	 * @dataProvider  dataGetTableColumns
	 */
	public function testGetTableColumns(string $table, bool $typeOnly, array $expected)
	{
		$this->assertEquals(
			$expected,
			static::$connection->getTableColumns($table, $typeOnly)
		);
	}

	/**
	 * @testdox  Information about the keys of a database table is returned
	 */
	public function testGetTableKeys()
	{
		$this->assertEmpty(
			static::$connection->getTableKeys('#__dbtest'),
			'Retrieving the list of keys for a table is not supported in SQL Server'
		);
	}

	/**
	 * @testdox  The connection can be set to use UTF-8 encoding
	 */
	public function testSetUtf()
	{
		$this->assertTrue(
			static::$connection->setUtf()
		);
	}

	/**
	 * @testdox  The database connection can be retrieved
	 */
	public function testGetConnection()
	{
		$this->assertTrue(
			is_resource(static::$connection->getConnection())
		);
	}

	/**
	 * @testdox  The name of the database driver is retrieved
	 */
	public function testGetName()
	{
		$this->assertSame(
			'sqlsrv',
			static::$connection->getName()
		);
	}

	/**
	 * @testdox  The null date for the server type is retrieved
	 */
	public function testGetNullDate()
	{
		$this->assertSame(
			'1900-01-01 00:00:00',
			static::$connection->getNullDate()
		);
	}

	/**
	 * @testdox  An exporter for the database driver can be created
	 */
	public function testGetExporter()
	{
		$this->expectException(UnsupportedAdapterException::class);

		static::$connection->getExporter();
	}

	/**
	 * @testdox  An importer for the database driver can be created
	 */
	public function testGetImporter()
	{
		$this->expectException(UnsupportedAdapterException::class);

		static::$connection->getImporter();
	}

	/**
	 * @testdox  A new query instance can be created
	 */
	public function testGetQueryNewInstance()
	{
		$this->assertInstanceOf(
			SqlsrvQuery::class,
			static::$connection->getQuery(true)
		);
	}
}
