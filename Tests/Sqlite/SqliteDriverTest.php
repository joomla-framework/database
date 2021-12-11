<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlite;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Exception\UnsupportedAdapterException;
use Joomla\Database\ParameterType;
use Joomla\Database\Sqlite\SqliteDriver;
use Joomla\Database\Sqlite\SqliteQuery;
use Joomla\Database\Tests\AbstractDatabaseDriverTestCase;

/**
 * Test class for Joomla\Database\Sqlite\SqliteDriver
 */
class SqliteDriverTest extends AbstractDatabaseDriverTestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		if (!static::$connection || static::$connection->getName() !== 'sqlite')
		{
			self::markTestSkipped('SQLite database not configured.');
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
			foreach (DatabaseDriver::splitSql(file_get_contents(dirname(__DIR__) . '/Stubs/Schema/sqlite.sql')) as $query)
			{
				static::$connection->setQuery($query)
					->execute();
			}
		}
		catch (ExecutionFailureException $exception)
		{
			$this->markTestSkipped(
				\sprintf(
					'Could not load SQLite database: %s',
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
		$tables = array_filter(
			static::$connection->getTableList(),
			function (string $table): bool
			{
				return $table !== 'sqlite_sequence';
			}
		);

		foreach ($tables as $table)
		{
			static::$connection->dropTable($table);
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
		yield ["'%_abc123", false, "''%_abc123"];
		yield ["'%_abc123", true, "''%_abc123"];
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
				'id'          => 'INTEGER',
				'title'       => 'TEXT',
				'start_date'  => 'TEXT',
				'description' => 'TEXT',
				'data'        => 'BLOB',
			],
		];

		yield 'full column information' => [
			'#__dbtest',
			false,
			[
				'id'          => (object) [
					'Field'   => 'id',
					'Type'    => 'INTEGER',
					'Null'    => 'YES',
					'Default' => null,
					'Key'     => 'PRI',
				],
				'title'       => (object) [
					'Field'   => 'title',
					'Type'    => 'TEXT',
					'Null'    => 'NO',
					'Default' => '\'\'',
					'Key'     => '',
				],
				'start_date'  => (object) [
					'Field'   => 'start_date',
					'Type'    => 'TEXT',
					'Null'    => 'NO',
					'Default' => '\'\'',
					'Key'     => '',
				],
				'description' => (object) [
					'Field'   => 'description',
					'Type'    => 'TEXT',
					'Null'    => 'NO',
					'Default' => '\'\'',
					'Key'     => '',
				],
				'data'        => (object) [
					'Field'   => 'data',
					'Type'    => 'BLOB',
					'Null'    => 'YES',
					'Default' => null,
					'Key'     => '',
				],
			],
		];
	}

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
	 * Data provider for binary quoting test cases
	 *
	 * @return  \Generator
	 */
	public function dataQuoteBinary(): \Generator
	{
		yield ['DATA', "X'" . bin2hex('DATA') . "'"];
		yield ["\x00\x01\x02\xff", "X'000102ff'"];
		yield ["\x01\x01\x02\xff", "X'010102ff'"];
	}

	/**
	 * Data provider for name quoting test cases
	 *
	 * @return  \Generator
	 */
	public function dataQuoteName(): \Generator
	{
		yield ['protected`title', null, '`protected``title`'];
		yield ['protected"title', null, '`protected"title`'];
		yield ['protected]title', null, '`protected]title`'];
	}

	/*
	 * Overrides for parent class test cases
	 */

	/**
	 * @testdox  The list of tables is returned
	 */
	public function testGetTableList()
	{
		$this->assertSame(
			[
				static::$connection->replacePrefix('#__dbtest'),
				'sqlite_sequence',
			],
			static::$connection->getTableList()
		);
	}

	/**
	 * @testdox  The minimum supported database version is retrieved
	 */
	public function testGetMinimum()
	{
		$this->assertNull(
			static::$connection->getMinimum(),
			'A minimum version is not specified for SQLite'
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

		$this->assertSame(0, static::$connection->getNumRows());
	}

	/*
	 * Test cases for this subclass
	 */

	/**
	 * @testdox  The database character set can be changed
	 */
	public function testAlterDbCharacterSet()
	{
		$this->assertFalse(
			static::$connection->alterDbCharacterSet(static::$dbManager->getDbName()),
			'Altering a database character set is not supported in SQLite'
		);
	}

	/**
	 * @testdox  A database can be created
	 */
	public function testCreateDatabase()
	{
		$this->assertTrue(
			static::$connection->createDatabase(new \stdClass),
			'Creating a database is not supported in SQLite'
		);
	}

	/**
	 * @testdox  The database collation can be retrieved
	 */
	public function testGetCollation()
	{
		$this->assertFalse(
			static::$connection->getCollation(),
			'Retrieving the database collation is not supported in SQLite'
		);
	}

	/**
	 * @testdox  The database connection collation can be retrieved
	 */
	public function testGetConnectionCollation()
	{
		$this->assertFalse(
			static::$connection->getConnectionCollation(),
			'Retrieving the database connection collation is not supported in SQLite'
		);
	}

	/**
	 * @testdox  The database connection encryption can be retrieved
	 */
	public function testGetConnectionEncryption()
	{
		$this->assertEmpty(
			static::$connection->getConnectionEncryption(),
			'Retrieving the database connection encryption is not supported in SQLite'
		);
	}

	/**
	 * @testdox  A list of queries to create the given tables is returned
	 */
	public function testGetTableCreate()
	{
		$this->assertSame(
			['#__dbtest'],
			static::$connection->getTableCreate('#__dbtest'),
			'Retrieving the queries to create a list of tables is not supported in SQLite'
		);
	}

	/**
	 * @testdox  Information about the keys of a database table is returned
	 */
	public function testGetTableKeys()
	{
		$this->assertEquals(
			[
				'id' => (object) [
					'CID' => '0',
					'NAME' => 'id',
					'TYPE' => 'INTEGER',
					'NOTNULL' => '0',
					'DFLT_VALUE' => null,
					'PK' => '1',
				],
			],
			static::$connection->getTableKeys('#__dbtest')
		);
	}

	/**
	 * @testdox  The connection can be set to use UTF-8 encoding
	 */
	public function testSetUtf()
	{
		$this->assertFalse(
			static::$connection->setUtf()
		);
	}

	/**
	 * @testdox  A database table can be truncated
	 */
	public function testTruncateTable()
	{
		$this->loadExampleData();

		static::$connection->truncateTable('#__dbtest');

		$this->assertSame(4, static::$connection->getAffectedRows());
	}

	/**
	 * @testdox  The database driver reports if it is supported in the present environment
	 */
	public function testIsSupported()
	{
		$this->assertTrue(
			SqliteDriver::isSupported()
		);
	}

	/**
	 * @testdox  A transaction can be started and committed
	 */
	public function testTransactionCommit()
	{
		$this->loadExampleData();

		static::$connection->transactionStart();

		$id          = 6;
		$title       = 'Test Title';
		$startDate   = '2019-10-26';
		$description = 'Test Description';

		// Insert row
		static::$connection->setQuery(
			static::$connection->getQuery(true)
				->insert('#__dbtest')
				->columns(['id', 'title', 'start_date', 'description'])
				->values(':id, :title, :start_date, :description')
				->bind(':id', $id, ParameterType::INTEGER)
				->bind(':title', $title)
				->bind(':start_date', $startDate)
				->bind(':description', $description)
		)->execute();

		static::$connection->transactionCommit();

		// Validate row is present
		$this->assertSame(1, static::$connection->getAffectedRows());

		$row = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
				->where('id = :id')
				->bind(':id', $id, ParameterType::INTEGER)
		)->loadObject();

		$this->assertEquals($id, $row->id);
	}

	/**
	 * Data provider for transaction rollback test cases
	 *
	 * @return  \Generator
	 */
	public function dataTransactionRollback()
	{
		yield 'rollback without savepoint' => [null, 0];

		yield 'rollback with savepoint' => ['transactionSavepoint', 1];
	}

	/**
	 * @testdox  A transaction can be started and committed
	 *
	 * @param   string|null  $toSavepoint  Savepoint name to rollback transaction to
	 * @param   integer      $tupleCount   Number of tuples found after insertion and rollback
	 *
	 * @dataProvider  dataTransactionRollback
	 */
	public function testTransactionRollback(?string $toSavepoint, int $tupleCount)
	{
		$this->loadExampleData();

		static::$connection->transactionStart();

		// Try to insert this tuple, inserted only when savepoint != null
		$id          = 6;
		$title       = 'testRollback';
		$startDate   = '2019-10-26';
		$description = 'testRollbackSp';

		static::$connection->setQuery(
			static::$connection->getQuery(true)
				->insert('#__dbtest')
				->columns(['id', 'title', 'start_date', 'description'])
				->values(':id, :title, :start_date, :description')
				->bind(':id', $id, ParameterType::INTEGER)
				->bind(':title', $title)
				->bind(':start_date', $startDate)
				->bind(':description', $description)
		)->execute();

		// Create savepoint only if is passed by data provider
		if ($toSavepoint !== null)
		{
			static::$connection->transactionStart(true);
		}

		// Try to insert this tuple, always rolled back
		$id        = 7;
		$startDate = '2019-10-27';

		static::$connection->setQuery(
			static::$connection->getQuery(true)
				->insert('#__dbtest')
				->columns(['id', 'title', 'start_date', 'description'])
				->values(':id, :title, :start_date, :description')
				->bind(':id', $id, ParameterType::INTEGER)
				->bind(':title', $title)
				->bind(':start_date', $startDate)
				->bind(':description', $description)
		)->execute();

		static::$connection->transactionRollback($toSavepoint !== null);

		// Release savepoint and commit only if a savepoint exists
		if ($toSavepoint !== null)
		{
			static::$connection->transactionCommit();
		}

		/*
		 * Determine number of rows that should exist, dependent on if a savepoint was created
		 *
		 * - 0 if a savepoint doesn't exist
		 * - 1 if a savepoint exists
		 */
		$transactionRows = static::$connection->setQuery(
			static::$connection->getQuery(true)
				->select('*')
				->from('#__dbtest')
				->where('description = :description')
				->bind(':description', $description)
		)->loadRowList();

		$this->assertCount($tupleCount, $transactionRows);
	}

	/**
	 * @testdox  The database connection can be retrieved
	 */
	public function testGetConnection()
	{
		$this->assertInstanceOf(
			\PDO::class,
			static::$connection->getConnection()
		);
	}

	/**
	 * @testdox  The name of the database driver is retrieved
	 */
	public function testGetName()
	{
		$this->assertSame(
			'sqlite',
			static::$connection->getName()
		);
	}

	/**
	 * @testdox  The type of server for the database driver is retrieved
	 */
	public function testGetServerType()
	{
		$this->assertSame(
			'sqlite',
			static::$connection->getServerType()
		);
	}

	/**
	 * @testdox  The null date for the server type is retrieved
	 */
	public function testGetNullDate()
	{
		$this->assertSame(
			'0000-00-00 00:00:00',
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
			SqliteQuery::class,
			static::$connection->getQuery(true)
		);
	}

	/**
	 * @testdox  Binary values are correctly supported
	 */
	public function testQuoteAndDecodeBinary()
	{
		$this->loadExampleData();

		// Add binary data with null byte
		$query = static::$connection->getQuery(true)
			->update('#__dbtest')
			->set('data = ' . static::$connection->quoteBinary("\x00\x01\x02\xff"))
			->where('id = 3');

		static::$connection->setQuery($query)->execute();

		// Add binary data with invalid UTF-8
		$query = static::$connection->getQuery(true)
			->update('#__dbtest')
			->set('data = ' . static::$connection->quoteBinary("\x01\x01\x02\xff"))
			->where('id = 4');

		static::$connection->setQuery($query)->execute();

		$selectRow3 = static::$connection->getQuery(true)
			->select('id')
			->from('#__dbtest')
			->where('data = ' . static::$connection->quoteBinary("\x00\x01\x02\xff"));

		$selectRow4 = static::$connection->getQuery(true)
			->select('id')
			->from('#__dbtest')
			->where('data = ' . static::$connection->quoteBinary("\x01\x01\x02\xff"));

		$result = static::$connection->setQuery($selectRow3)->loadResult();
		$this->assertEquals(3, $result);

		$result = static::$connection->setQuery($selectRow4)->loadResult();
		$this->assertEquals(4, $result);

		$selectRows = static::$connection->getQuery(true)
			->select('data')
			->from('#__dbtest')
			->order('id');

		// Test loadColumn
		$result = static::$connection->setQuery($selectRows)->loadColumn();

		foreach ($result as $i => $v)
		{
			$result[$i] = static::$connection->decodeBinary($v);
		}

		$this->assertEquals(
			[null, null, "\x00\x01\x02\xff", "\x01\x01\x02\xff"],
			$result
		);

		// Test loadAssocList
		$result = static::$connection->setQuery($selectRows)->loadAssocList();

		foreach ($result as $i => $v)
		{
			$result[$i]['data'] = static::$connection->decodeBinary($v['data']);
		}

		$expected = [
			['data' => null],
			['data' => null],
			['data' => "\x00\x01\x02\xff"],
			['data' => "\x01\x01\x02\xff"],
		];

		$this->assertEquals($expected, $result);

		// Test loadObjectList
		$result = static::$connection->setQuery($selectRows)->loadObjectList();

		foreach ($result as $i => $v)
		{
			$result[$i]->data = static::$connection->decodeBinary($v->data);
		}

		$expected = [
			(object) ['data' => null],
			(object) ['data' => null],
			(object) ['data' => "\x00\x01\x02\xff"],
			(object) ['data' => "\x01\x01\x02\xff"],
		];

		$this->assertEquals($expected, $result);
	}
}
