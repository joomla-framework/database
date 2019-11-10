<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlsrv;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseIterator;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Exception\UnsupportedAdapterException;
use Joomla\Database\Monitor\ChainedMonitor;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;
use Joomla\Database\Sqlsrv\SqlsrvDriver;
use Joomla\Database\Sqlsrv\SqlsrvQuery;
use Joomla\Test\DatabaseTestCase;

/**
 * Test class for Joomla\Database\Sqlsrv\SqlsrvDriver.
 */
class SqlsrvDriverTest extends DatabaseTestCase
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
	 * @testdox  Text can be escaped
	 *
	 * @param   string   $text      The string to be escaped.
	 * @param   boolean  $extra     Optional parameter to provide extra escaping.
	 * @param   string   $expected  The expected result.
	 *
	 * @dataProvider  dataEscape
	 */
	public function testEscape($text, $extra, $expected)
	{
		$this->assertSame(
			$expected,
			static::$connection->escape($text, $extra)
		);
	}

	/**
	 * @testdox  Values can be escaped in a locale aware context
	 */
	public function testEscapeNonLocaleAware()
	{
		$origin = setlocale(LC_NUMERIC, 0);

		// Test with decimal_point equals to comma
		setlocale(LC_NUMERIC, 'pl_PL');

		$this->assertSame('3.14', static::$connection->escape(3.14));

		// Test with C locale
		setlocale(LC_NUMERIC, 'C');

		$this->assertSame('3.14', static::$connection->escape(3.14));

		// Revert to origin locale
		setlocale(LC_NUMERIC, $origin);
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
	 * @testdox  A binary value is quoted properly
	 *
	 * @param   string  $data      The binary quoted input string.
	 * @param   string  $expected  The expected result.
	 *
	 * @dataProvider  dataQuoteBinary
	 */
	public function testQuoteBinary($data, $expected)
	{
		$this->assertSame($expected, static::$connection->quoteBinary($data));
	}

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
	 * @testdox  The connection can be checked for encryption support
	 */
	public function testIsConnectionEncryptionSupported()
	{
		$this->assertTrue(
			\is_bool(static::$connection->isConnectionEncryptionSupported()),
			'The driver should report whether connection encryption is supported.'
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
	 * @testdox  The list of tables is returned
	 */
	public function testGetTableList()
	{
		$this->assertSame(
			[
				static::$connection->replacePrefix('#__dbtest'),
			],
			static::$connection->getTableList()
		);
	}

	/**
	 * @testdox  The database version is returned
	 */
	public function testGetVersion()
	{
		$this->assertNotEmpty(
			static::$connection->getVersion()
		);
	}

	/**
	 * @testdox  An object can be inserted into the database
	 */
	public function testInsertObject()
	{
		$this->loadExampleData();

		$data = (object) [
			'id'          => null,
			'title'       => 'Testing insertObject',
			'start_date'  => '2019-10-26 00:00:00',
			'description' => 'test insertObject row',
		];

		static::$connection->insertObject(
			'#__dbtest',
			$data,
			'id'
		);

		$this->assertNotNull($data->id, 'When given a key, the insertObject method should set the row ID');
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
	 * @testdox  A database table can be renamed
	 */
	public function testRenameTable()
	{
		$oldTableName = '#__dbtest';
		$newTableName = 'bak_dbtest';

		$this->assertSame(
			static::$connection,
			static::$connection->renameTable($oldTableName, $newTableName),
			'The database driver supports method chaining'
		);

		$this->assertTrue(
			\in_array($newTableName, static::$connection->getTableList())
		);

		// Restore initial state
		static::$connection->renameTable($newTableName, $oldTableName);

		$this->assertFalse(
			\in_array($newTableName, static::$connection->getTableList())
		);
	}

	/**
	 * @testdox  A database table can be locked and unlocked
	 */
	public function testLockAndUnlockTable()
	{
		$this->assertSame(
			static::$connection,
			static::$connection->lockTable('#__dbtest'),
			'The database driver supports method chaining'
		);

		$this->assertSame(
			static::$connection,
			static::$connection->unlockTables(),
			'The database driver supports method chaining'
		);
	}

	/*
	 * Tests covering parent class
	 */

	/**
	 * Data provider for table dropping test cases
	 *
	 * @return  \Generator
	 */
	public function dataDropTable()
	{
		yield 'database exists before query' => ['#__dbtest', true];

		yield 'database does not exist before query' => ['#__foo', false];
	}

	/**
	 * @testdox  A database table can be dropped
	 *
	 * @param   string   $table          The name of the database table to drop.
	 * @param   boolean  $alreadyExists  Flag indicating the table should exist before the DROP TABLE query.
	 *
	 * @dataProvider  dataDropTable
	 */
	public function testDropTable(string $table, bool $alreadyExists)
	{
		$this->assertSame(
			$alreadyExists,
			\in_array(static::$connection->replacePrefix($table), static::$connection->getTableList())
		);

		$this->assertSame(
			static::$connection,
			static::$connection->dropTable($table, true),
			'The database driver supports method chaining'
		);

		$this->assertFalse(
			\in_array(static::$connection->replacePrefix($table), static::$connection->getTableList())
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
	 * @testdox  The number of executed SQL statements can be retrieved
	 */
	public function testGetCount()
	{
		$this->assertTrue(
			is_int(static::$connection->getCount()),
			'The count of the number of executed SQL statements should be retrieved'
		);
	}

	/**
	 * @testdox  A PHP DateTime compatible date format for the database driver can be retrieved
	 */
	public function testGetDateFormat()
	{
		$this->assertSame(
			'Y-m-d H:i:s',
			static::$connection->getDateFormat()
		);
	}

	/**
	 * @testdox  The minimum supported database version is retrieved
	 */
	public function testGetMinimum()
	{
		$this->assertTrue(
			\is_string(static::$connection->getMinimum()),
			'The minimum version is returned as a string'
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
	 * @testdox  The number of rows returned by the query can be retrieved
	 */
	public function testGetNumRows()
	{
		$this->loadExampleData();

		static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
				->where(static::$connection->quoteName('description') . ' = ' . static::$connection->quote('test row one'))
		);

		static::$connection->execute();

		$this->assertSame(1, static::$connection->getNumRows());
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

	/**
	 * @testdox  A cached query instance can be retrieved
	 */
	public function testGetQueryCachedQuery()
	{
		$query = static::$connection->getQuery(true)
			->select('*')
			->from('#__dbtest');

		static::$connection->setQuery($query);

		$this->assertSame($query, static::$connection->getQuery(false));
	}

	/**
	 * @testdox  An iterator for the database driver can be created
	 */
	public function testGetIterator()
	{
		$this->loadExampleData();

		static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
		);

		$this->assertInstanceOf(
			DatabaseIterator::class,
			static::$connection->getIterator()
		);
	}

	/**
	 * @testdox  The connection can be checked for UTF support
	 */
	public function testHasUtfSupport()
	{
		$this->assertTrue(
			static::$connection->hasUtfSupport()
		);
	}

	/**
	 * @testdox  The database server can be checked if it is running a version matching the minimum supported version
	 */
	public function testIsMinimumVersion()
	{
		$this->assertTrue(
			static::$connection->isMinimumVersion()
		);
	}

	/**
	 * @testdox  The first row of a result set can be loaded as an associative array
	 */
	public function testLoadAssoc()
	{
		$this->loadExampleData();

		$result = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('title')
				->from('#__dbtest')
		)->loadAssoc();

		$this->assertEquals(
			[
				'title' => 'Testing1',
			],
			$result
		);
	}

	/**
	 * @testdox  All rows of a result set can be loaded as an associative array
	 */
	public function testLoadAssocList()
	{
		$this->loadExampleData();

		$result = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('title')
				->from('#__dbtest')
		)->loadAssocList();

		$this->assertEquals(
			[
				['title' => 'Testing1'],
				['title' => 'Testing2'],
				['title' => 'Testing3'],
				['title' => 'Testing4'],
			],
			$result
		);
	}

	/**
	 * @testdox  The specified column from all rows of a result set can be loaded as an array
	 */
	public function testLoadColumn()
	{
		$this->loadExampleData();

		$result = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('title')
				->from('#__dbtest')
		)->loadColumn();

		$this->assertEquals(
			[
				'Testing1',
				'Testing2',
				'Testing3',
				'Testing4',
			],
			$result
		);
	}

	/**
	 * @testdox  The first row of a result set can be loaded as a PHP object
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
	 * @testdox  The first field from the first row of a result set can be loaded
	 */
	public function testLoadResult()
	{
		$this->loadExampleData();

		$result = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
		)->loadResult();

		$this->assertEquals('1', $result);
	}

	/**
	 * @testdox  The first row of a result set can be loaded as an array
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
				'1',
				'Testing1',
				'2019-10-26 00:00:00.000',
				'test row one',
				null,
			],
			[
				'2',
				'Testing2',
				'2019-10-26 00:00:00.000',
				'test row two',
				null,
			],
			[
				'3',
				'Testing3',
				'2019-10-26 00:00:00.000',
				'test row three',
				null,
			],
			[
				'4',
				'Testing4',
				'2019-10-26 00:00:00.000',
				'test row four',
				null,
			],
		];

		$this->assertEquals($expected, $result);
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

	/**
	 * @testdox  A value is name quoted properly
	 *
	 * @param   array|string  $name      The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
	 * @param   array|string  $as        The AS query part associated to $name.
	 * @param   array|string  $expected  The expected result.
	 *
	 * @dataProvider  dataQuoteName
	 */
	public function testQuoteName($name, $as, $expected)
	{
		$this->assertSame(
			$expected,
			static::$connection->quoteName($name, $as)
		);
	}

	/**
	 * @testdox  A query monitor can be set and retrieved
	 */
	public function testGetAndSetQueryMonitor()
	{
		$this->assertNull(static::$connection->getMonitor(), 'A database driver has no monitor by default');

		$monitor = new ChainedMonitor;

		$this->assertSame(
			static::$connection,
			static::$connection->setMonitor($monitor),
			'The database driver supports method chaining'
		);

		$this->assertSame(
			$monitor,
			static::$connection->getMonitor()
		);
	}

	/**
	 * @testdox  A QueryInterface object can be set to the driver without an offset or limit
	 */
	public function testSetQueryWithQueryObjectWithoutOffsetOrLimit()
	{
		$query = static::$connection->getQuery(true)
			->select('*')
			->from('#__dbtest');

		$this->assertSame(
			static::$connection,
			static::$connection->setQuery($query),
			'The database driver supports method chaining'
		);

		$this->assertSame(
			$query,
			static::$connection->getQuery(false),
			'The injected query object should be returned'
		);
	}

	/**
	 * @testdox  A QueryInterface object can be set to the driver with an offset or limit
	 */
	public function testSetQueryWithQueryObjectWithOffsetAndLimit()
	{
		$query = static::$connection->getQuery(true)
			->select('*')
			->from('#__dbtest');

		$this->assertSame(
			static::$connection,
			static::$connection->setQuery($query, 3, 10),
			'The database driver supports method chaining'
		);

		$queryFromDriver = static::$connection->getQuery(false);

		$this->assertSame(
			$query,
			$queryFromDriver,
			'The injected query object should be returned'
		);

		$this->assertSame(
			3,
			$queryFromDriver->offset,
			'An offset should be set to the query object.'
		);

		$this->assertSame(
			10,
			$queryFromDriver->limit,
			'A limit should be set to the query object.'
		);
	}

	/**
	 * @testdox  A QueryInterface object can be set to the driver while retraining the offset and limit from the query
	 */
	public function testSetQueryWithQueryObjectWithOffsetAndLimitOnQuery()
	{
		$query = static::$connection->getQuery(true)
			->select('*')
			->from('#__dbtest')
			->setLimit(10, 3);

		$this->assertSame(
			static::$connection,
			static::$connection->setQuery($query),
			'The database driver supports method chaining'
		);

		$queryFromDriver = static::$connection->getQuery(false);

		$this->assertSame(
			$query,
			$queryFromDriver,
			'The injected query object should be returned'
		);

		$this->assertSame(
			3,
			$queryFromDriver->offset,
			'An offset should be set to the query object.'
		);

		$this->assertSame(
			10,
			$queryFromDriver->limit,
			'A limit should be set to the query object.'
		);
	}

	/**
	 * @testdox  A string can be set to the driver without an offset or limit
	 */
	public function testSetQueryWithStringWithoutOffsetOrLimit()
	{
		$query = 'SELECT * FROM #__dbtest';

		$this->assertSame(
			static::$connection,
			static::$connection->setQuery($query),
			'The database driver supports method chaining'
		);

		$this->assertInstanceOf(
			QueryInterface::class,
			static::$connection->getQuery(false),
			\sprintf('A string should be converted to a %s instance', QueryInterface::class)
		);
	}

	/**
	 * @testdox  An invalid query type cannot be set to the driver
	 */
	public function testSetQueryWithInvalidQueryType()
	{
		$this->expectException(\InvalidArgumentException::class);

		static::$connection->setQuery(new \stdClass);
	}

	/**
	 * @testdox  An object can be used to update a row in the database
	 */
	public function testUpdateObject()
	{
		$this->loadExampleData();

		$data = (object) [
			'id'          => 1,
			'title'       => 'Testing updateObject',
			'start_date'  => '2019-10-26 00:00:00',
			'description' => 'test updateObject row',
			'data'        => null,
		];

		static::$connection->updateObject(
			'#__dbtest',
			$data,
			'id'
		);

		// Fetch row to validate update
		$row = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
				->where('id = :id')
				->bind(':id', $data->id, ParameterType::INTEGER)
		)->loadObject();

		$this->assertSame($row->title, $data->title);
	}

	/**
	 * @testdox  Queries using the querySet type are correctly built and executed
	 */
	public function testQuerySetWithUnionAll()
	{
		$this->loadExampleData();

		$query  = static::$connection->getQuery(true);
		$union1 = static::$connection->getQuery(true);
		$union2 = static::$connection->getQuery(true);

		$union1->select('id, title')
			->from('#__dbtest')
			->where('id = 4')
			->setLimit(1);

		$union2->select('id, title')
			->from('#__dbtest')
			->where('id < 4')
			->order('id DESC')
			->setLimit(2, 1);

		$query->querySet($union1)
			->unionAll($union2)
			->order('id');

		$result = static::$connection->setQuery($query, 0, 3)->loadAssocList();

		$this->assertEquals(
			[
				['id' => '1', 'title' => 'Testing1'],
				['id' => '2', 'title' => 'Testing2'],
				['id' => '4', 'title' => 'Testing4'],
			],
			$result
		);
	}

	/**
	 * @testdox  Queries converted to the querySet type are correctly built and executed
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
}
