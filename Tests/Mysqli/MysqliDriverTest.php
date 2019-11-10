<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseIterator;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Monitor\ChainedMonitor;
use Joomla\Database\Mysqli\MysqliDriver;
use Joomla\Database\Mysqli\MysqliExporter;
use Joomla\Database\Mysqli\MysqliImporter;
use Joomla\Database\Mysqli\MysqliQuery;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;
use Joomla\Test\DatabaseTestCase;

/**
 * Test class for Joomla\Database\Mysqli\MysqliDriver
 */
class MysqliDriverTest extends DatabaseTestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		if (!static::$connection || static::$connection->getName() !== 'mysqli')
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

	/**
	 * Loads the example data into the database.
	 *
	 * @return  void
	 */
	protected function loadExampleData(): void
	{
		$data = [
			(object) [
				'id'          => 1,
				'title'       => 'Testing1',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row one',
			],
			(object) [
				'id'          => 2,
				'title'       => 'Testing2',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row two',
			],
			(object) [
				'id'          => 3,
				'title'       => 'Testing3',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row three',
			],
			(object) [
				'id'          => 4,
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
		yield ["'%_abc123", false, '\\\'%_abc123'];
		yield ["'%_abc123", true, '\\\'\\%\_abc123'];
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
	 * @testdox  The database driver reports if it is supported in the present environment
	 */
	public function testIsSupported()
	{
		$this->assertTrue(
			MysqliDriver::isSupported()
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
		$this->assertCount(
			1,
			static::$connection->getTableCreate('#__dbtest'),
			'The create table queries for the given tables is returned'
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
					'Type'       => 'int(10) unsigned',
					'Collation'  => '',
					'Null'       => 'NO',
					'Key'        => 'PRI',
					'Default'    => '',
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

		// MySQL 8.0 adds additional data and casts certain keys to integers
		if (!static::$connection->isMariaDb() && version_compare(static::$connection->getVersion(), '8.0', '>='))
		{
			$dbtestPrimaryKey['Non_unique']   = (int) $dbtestPrimaryKey['Non_unique'];
			$dbtestPrimaryKey['Seq_in_index'] = (int) $dbtestPrimaryKey['Seq_in_index'];
			$dbtestPrimaryKey['Cardinality']  = (int) $dbtestPrimaryKey['Cardinality'];

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
	 * @testdox  The database reports if it has support for the utf8mb4 character sets
	 */
	public function testHasUTF8mb4Support()
	{
		$this->assertTrue(
			static::$connection->hasUTF8mb4Support(),
			'The database driver does have utf8mb4 support by default'
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
	 * @testdox  A database table can be locked
	 */
	public function testLockTable()
	{
		$this->assertSame(
			static::$connection,
			static::$connection->lockTable('#__dbtest'),
			'The database driver supports method chaining'
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
	 * @testdox  A database can be selected for use
	 */
	public function testSelect()
	{
		$this->assertTrue(
			static::$connection->select(static::$dbManager->getDbName())
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
	 * @testdox  The database tables can be unlocked
	 */
	public function testUnlockTables()
	{
		$this->assertSame(
			static::$connection,
			static::$connection->unlockTables(),
			'The database driver supports method chaining'
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
		$this->assertInstanceOf(
			\mysqli::class,
			static::$connection->getConnection()
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
	 * @testdox  The name of the database driver is retrieved
	 */
	public function testGetName()
	{
		$this->assertSame(
			'mysqli',
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
	 * @testdox  An exporter for the database driver can be created
	 */
	public function testGetExporter()
	{
		$this->assertInstanceOf(
			MysqliExporter::class,
			static::$connection->getExporter()
		);
	}

	/**
	 * @testdox  An importer for the database driver can be created
	 */
	public function testGetImporter()
	{
		$this->assertInstanceOf(
			MysqliImporter::class,
			static::$connection->getImporter()
		);
	}

	/**
	 * @testdox  A new query instance can be created
	 */
	public function testGetQueryNewInstance()
	{
		$this->assertInstanceOf(
			MysqliQuery::class,
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
			'id'          => '1',
			'title'       => 'Testing1',
			'start_date'  => '2019-10-26 00:00:00',
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
				'id'          => '1',
				'title'       => 'Testing1',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row one',
				'data'        => null,
			],
			(object) [
				'id'          => '2',
				'title'       => 'Testing2',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row two',
				'data'        => null,
			],
			(object) [
				'id'          => '3',
				'title'       => 'Testing3',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row three',
				'data'        => null,
			],
			(object) [
				'id'          => '4',
				'title'       => 'Testing4',
				'start_date'  => '2019-10-26 00:00:00',
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
			'2019-10-26 00:00:00',
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
				'2019-10-26 00:00:00',
				'test row one',
				null,
			],
			[
				'2',
				'Testing2',
				'2019-10-26 00:00:00',
				'test row two',
				null,
			],
			[
				'3',
				'Testing3',
				'2019-10-26 00:00:00',
				'test row three',
				null,
			],
			[
				'4',
				'Testing4',
				'2019-10-26 00:00:00',
				'test row four',
				null,
			],
		];

		$this->assertEquals($expected, $result);
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
			->where('data = '. static::$connection->quoteBinary("\x01\x01\x02\xff"));

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

		$query->select('id, title')
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
			],
			$result
		);
	}

	/**
	 * @testdox  Select statements can be prepared once and executed repeateadly
	 */
	public function testRepeatedSelectStatement()
	{
		$this->loadExampleData();
		$results = [];

		$query = static::$connection->getQuery(true);
		$query->select('id, title')
			->from('#__dbtest')
			->where('id = :id')
			->bind(':id', $id, ParameterType::INTEGER);

		// Test repeated statements.
		static::$connection->setQuery($query);
		$id        = 1;
		$results[] = static::$connection->loadAssocList();
		$id        = 4;
		$results[] = static::$connection->loadAssocList();

		// Also test that running a new query works.
		static::$connection->setQuery($query);
		$id        = 2;
		$results[] = static::$connection->loadAssocList();

		$this->assertEquals(
			[
				['id' => '1', 'title' => 'Testing1'],
				['id' => '4', 'title' => 'Testing4'],
				['id' => '2', 'title' => 'Testing2'],
			],
			$results
		);
	}
}
