<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysql;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Mysql\MysqlDriver;
use Joomla\Database\Mysql\MysqlExporter;
use Joomla\Database\Mysql\MysqlImporter;
use Joomla\Database\Mysql\MysqlQuery;
use Joomla\Database\ParameterType;
use Joomla\Database\Tests\AbstractDatabaseDriverTestCase;

/**
 * Test class for Joomla\Database\Mysql\MysqlDriver
 */
class MysqlDriverTest extends AbstractDatabaseDriverTestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		if (!static::$connection || static::$connection->getName() !== 'mysql')
		{
			self::markTestSkipped('MySQL database not configured.');
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
			foreach (DatabaseDriver::splitSql(file_get_contents(dirname(__DIR__) . '/Stubs/Schema/mysql.sql')) as $query)
			{
				static::$connection->setQuery($query)
					->execute();
			}
		}
		catch (ExecutionFailureException $exception)
		{
			$this->markTestSkipped(
				\sprintf(
					'Could not load MySQL database: %s',
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

	/*
	 * Overrides for data providers from the parent test case
	 */

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
				'id'          => 'int unsigned',
				'title'       => 'varchar',
				'start_date'  => 'datetime',
				'description' => 'text',
				'data'        => 'blob',
			],
		];

		yield 'full column information' => [
			'#__dbtest',
			false,
			[
				'id'          => (object) [
					'Field'      => 'id',
					'Type'       => (float) getenv('MYSQL_VERSION') < 8.0 ? 'int(10) unsigned' : 'int unsigned',
					'Collation'  => (float) getenv('MYSQL_VERSION') < 8.0 ? '' : null,
					'Null'       => 'NO',
					'Key'        => 'PRI',
					'Default'    => (float) getenv('MYSQL_VERSION') < 8.0 ? '' : null,
					'Extra'      => 'auto_increment',
					'Privileges' => 'select,insert,update,references',
					'Comment'    => '',
				],
				'title'       => (object) [
					'Field'      => 'title',
					'Type'       => 'varchar(50)',
					'Collation'  => 'utf8_general_ci',
					'Null'       => 'NO',
					'Key'        => '',
					'Default'    => '',
					'Extra'      => '',
					'Privileges' => 'select,insert,update,references',
					'Comment'    => '',
				],
				'start_date'  => (object) [
					'Field'      => 'start_date',
					'Type'       => 'datetime',
					'Collation'  => '',
					'Null'       => 'NO',
					'Key'        => '',
					'Default'    => '',
					'Extra'      => '',
					'Privileges' => 'select,insert,update,references',
					'Comment'    => '',
				],
				'description' => (object) [
					'Field'      => 'description',
					'Type'       => 'text',
					'Collation'  => 'utf8_general_ci',
					'Null'       => 'NO',
					'Key'        => '',
					'Default'    => '',
					'Extra'      => '',
					'Privileges' => 'select,insert,update,references',
					'Comment'    => '',
				],
				'data'        => (object) [
					'Field'      => 'data',
					'Type'       => 'blob',
					'Collation'  => '',
					'Null'       => 'YES',
					'Key'        => '',
					'Default'    => '',
					'Extra'      => '',
					'Privileges' => 'select,insert,update,references',
					'Comment'    => '',
				],
			],
		];
	}

	/**
	 * Data provider for escaping test cases
	 *
	 * @return  \Generator
	 */
	public function dataEscape(): \Generator
	{
		yield ["'%_abc123", false, '\\\'%_abc123'];
		yield ["'%_abc123", true, '\\\'\\%\_abc123'];
		yield [3, false, 3];
		yield [3.14, false, '3.14'];
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

	/*
	 * Test cases for this subclass
	 */

	/**
	 * @testdox  The database driver reports if it is supported in the present environment
	 */
	public function testIsSupported()
	{
		$this->assertTrue(
			MysqlDriver::isSupported()
		);
	}

	/**
	 * @testdox  The database collation can be retrieved
	 */
	public function testGetCollation()
	{
		$this->assertNotFalse(
			static::$connection->getCollation()
		);
	}

	/**
	 * @testdox  The database connection collation can be retrieved
	 */
	public function testGetConnectionCollation()
	{
		$this->assertNotFalse(
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
		$this->assertCount(
			1,
			static::$connection->getTableCreate('#__dbtest'),
			'The create table queries for the given tables is returned'
		);
	}

	/**
	 * @testdox  Information about the keys of a database table is returned
	 */
	public function testGetTableKeys()
	{
		$dbtestPrimaryKey = [
			'Table'         => static::$connection->replacePrefix('#__dbtest'),
			'Non_unique'    => '0',
			'Key_name'      => 'PRIMARY',
			'Seq_in_index'  => '1',
			'Column_name'   => 'id',
			'Collation'     => 'A',
			'Cardinality'   => '0',
			'Sub_part'      => null,
			'Packed'        => null,
			'Null'          => '',
			'Index_type'    => 'BTREE',
			'Comment'       => '',
			'Index_comment' => '',
		];

		// MySQL 8.0 adds additional data
		if (!static::$connection->isMariaDb() && version_compare(static::$connection->getVersion(), '8.0', '>='))
		{
			$dbtestPrimaryKey['Visible']    = 'YES';
			$dbtestPrimaryKey['Expression'] = null;
		}

		$keys = [
			(object) $dbtestPrimaryKey,
		];

		$this->assertEquals(
			$keys,
			static::$connection->getTableKeys('#__dbtest')
		);
	}

	/**
	 * @testdox  The database reports if it has support for the utf8mb4 character sets
	 */
	public function testHasUTF8mb4Support()
	{
		$this->assertFalse(
			static::$connection->hasUTF8mb4Support(),
			'The database driver does not have utf8mb4 support by default'
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
			'mysql',
			static::$connection->getName()
		);
	}

	/**
	 * @testdox  The type of server for the database driver is retrieved
	 */
	public function testGetServerType()
	{
		$this->assertSame(
			'mysql',
			static::$connection->getServerType()
		);
	}

	/**
	 * @testdox  The null date for the server type is retrieved
	 */
	public function testGetNullDate()
	{
		$result   = static::$connection->setQuery('SELECT @@SESSION.sql_mode;')->loadResult();
		$expected = '0000-00-00 00:00:00';

		if (strpos($result, 'NO_ZERO_DATE') !== false)
		{
			$expected = '1000-01-01 00:00:00';
		}

		$this->assertSame(
			$expected,
			static::$connection->getNullDate()
		);
	}

	/**
	 * @testdox  An exporter for the database driver can be created
	 */
	public function testGetExporter()
	{
		$this->assertInstanceOf(
			MysqlExporter::class,
			static::$connection->getExporter()
		);
	}

	/**
	 * @testdox  An importer for the database driver can be created
	 */
	public function testGetImporter()
	{
		$this->assertInstanceOf(
			MysqlImporter::class,
			static::$connection->getImporter()
		);
	}

	/**
	 * @testdox  A new query instance can be created
	 */
	public function testGetQueryNewInstance()
	{
		$this->assertInstanceOf(
			MysqlQuery::class,
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

		$this->assertSame(0, static::$connection->getAffectedRows());
	}
}
