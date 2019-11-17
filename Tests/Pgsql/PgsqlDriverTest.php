<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Pgsql;

use Joomla\Database\ParameterType;
use Joomla\Database\Pgsql\PgsqlDriver;
use Joomla\Database\Pgsql\PgsqlExporter;
use Joomla\Database\Pgsql\PgsqlImporter;
use Joomla\Database\Pgsql\PgsqlQuery;
use Joomla\Database\Tests\AbstractDatabaseDriverTestCase;

/**
 * Test class for Joomla\Database\Pgsql\PgsqlDriver
 */
class PgsqlDriverTest extends AbstractDatabaseDriverTestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		if (!static::$connection || static::$connection->getName() !== 'pgsql')
		{
			self::markTestSkipped('PostgreSQL database not configured.');
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
			static::$connection->truncateTable($table);
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
				'id'          => 'integer',
				'title'       => 'character varying',
				'start_date'  => 'timestamp without time zone',
				'description' => 'text',
				'data'        => 'bytea',
			],
		];

		yield 'full column information' => [
			'#__dbtest',
			false,
			[
				'id'          => (object) [
					'column_name' => 'id',
					'Field'       => 'id',
					'type'        => 'integer',
					'Type'        => 'integer',
					'null'        => 'NO',
					'Null'        => 'NO',
					'Default'     => 'nextval(\'dbtest_id_seq\'::regclass)',
					'comments'    => '',
				],
				'title'       => (object) [
					'column_name' => 'title',
					'Field'       => 'title',
					'type'        => 'character varying(50)',
					'Type'        => 'character varying(50)',
					'null'        => 'NO',
					'Null'        => 'NO',
					'Default'     => null,
					'comments'    => '',
				],
				'start_date'  => (object) [
					'column_name' => 'start_date',
					'Field'       => 'start_date',
					'type'        => 'timestamp without time zone',
					'Type'        => 'timestamp without time zone',
					'null'        => 'NO',
					'Null'        => 'NO',
					'Default'     => null,
					'comments'    => '',
				],
				'description' => (object) [
					'column_name' => 'description',
					'Field'       => 'description',
					'type'        => 'text',
					'Type'        => 'text',
					'null'        => 'NO',
					'Null'        => 'NO',
					'Default'     => null,
					'comments'    => '',
				],
				'data'        => (object) [
					'column_name' => 'data',
					'Field'       => 'data',
					'type'        => 'bytea',
					'Type'        => 'bytea',
					'null'        => 'YES',
					'Null'        => 'YES',
					'Default'     => null,
					'comments'    => '',
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
		yield ['DATA', "decode('44415441', 'hex')"];
		yield ["\x00\x01\x02\xff", "decode('000102ff', 'hex')"];
		yield ["\x01\x01\x02\xff", "decode('010102ff', 'hex')"];
	}

	/**
	 * Data provider for table dropping test cases
	 *
	 * @return  \Generator
	 */
	public function dataDropTable()
	{
		yield 'database does not exist before query' => ['#__foo', false];
	}

	/**
	 * Data provider for escaping test cases
	 *
	 * @return  \Generator
	 */
	public function dataEscape(): \Generator
	{
		yield ["'%_abc123", false, '\'\'%_abc123'];
		yield ["'%_abc123", true, '\'\'%_abc123'];
		yield ["\'%_abc123", false, '\\\\\'\'%_abc123'];
		yield ["\'%_abc123", true, '\\\\\'\'%_abc123'];
		yield [3, false, 3];
		yield [3.14, false, '3.14'];
	}

	/**
	 * Data provider for name quoting test cases
	 *
	 * @return  \Generator
	 */
	public function dataQuoteName(): \Generator
	{
		yield ['protected`title', null, '"protected`title"'];
		yield ['protected"title', null, '"protected""title"'];
		yield ['protected]title', null, '"protected]title"'];
	}

	/*
	 * Overrides for parent class test cases
	 */

	/**
	 * @testdox  An object can be inserted into the database
	 */
	public function testInsertObject()
	{
		$this->loadExampleData();

		static::$connection->setQuery(
			\sprintf(
				'ALTER SEQUENCE %s RESTART WITH 5',
				static::$connection->replacePrefix('#__dbtest_id_seq')
			)
		)->execute();

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

		// Check index change
		$subquery = static::$connection->getQuery(true)
			->select('indexrelid')
			->from('pg_index')
			->from('pg_class')
			->where('pg_class.relname = ' . static::$connection->quote($newTableName))
			->where('pg_class.oid = pg_index.indrelid');

		$query = static::$connection->getQuery(true)
			->select('relname')
			->from('pg_class')
			->where('oid IN (' . (string) $subquery . ')');

		$oldIndexes = static::$connection->setQuery($query)
			->loadColumn();

		$this->assertEquals(
			[
				'bak_dbtest_pkey',
			],
			$oldIndexes
		);

		// Check sequence change
		$subquery = static::$connection->getQuery(true)
			->select('oid')
			->from('pg_namespace')
			->where('nspname NOT LIKE ' . static::$connection->quote('pg_%'))
			->where('nspname != ' . static::$connection->quote('information_schema'));

		$query = static::$connection->getQuery(true)
			->select('relname')
			->from('pg_class')
			->where('relkind = ' . static::$connection->quote('S'))
			->where('relnamespace IN (' . (string) $subquery . ')')
			->where('relname LIKE ' . static::$connection->quote('%' . $newTableName . '%'));

		$oldSequences = static::$connection->setQuery($query)
			->loadColumn();

		$this->assertEquals(
			[
				'bak_dbtest_id_seq',
			],
			$oldSequences
		);

		// Restore initial state
		static::$connection->renameTable($newTableName, $oldTableName);

		$this->assertFalse(
			\in_array($newTableName, static::$connection->getTableList())
		);
	}

	/*
	 * Test cases for this subclass
	 */

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
		$expectedResult = '';

		if (\getenv('TRAVIS') === true && in_array(\getenv('PGSQL_VERSION'), ['9.5', '9.6', '10.0']))
		{
			$expectedResult = 'FTLSv1.2 (ECDHE-RSA-AES256-GCM-SHA384)';
		}

		$this->assertEquals(
			$expectedResult,
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
			'Retrieving the queries to create a list of tables is not supported in PostgreSQL'
		);
	}

	/**
	 * @testdox  Information about the keys of a database table is returned
	 */
	public function testGetTableKeys()
	{
		$this->assertEquals(
			[
				(object) [
					'idxName' => static::$connection->replacePrefix('#__dbtest_pkey'),
					'isPrimary' => true,
					'isUnique' => true,
					'indKey' => '1',
					'Query' => \sprintf('ALTER TABLE %s ADD PRIMARY KEY (id)', static::$connection->replacePrefix('#__dbtest'))
				]
			],
			static::$connection->getTableKeys('#__dbtest')
		);
	}

	/**
	 * @testdox  Information about the sequences of a database table is returned
	 */
	public function testGetTableSequences()
	{
		$sequence = [
			'sequence'      => static::$connection->replacePrefix('#__dbtest_id_seq'),
			'schema'        => 'public',
			'table'         => static::$connection->replacePrefix('#__dbtest'),
			'column'        => 'id',
			'data_type'     => 'bigint',
			'minimum_value' => '1',
			'maximum_value' => '9223372036854775807',
			'increment'     => '1',
			'cycle_option'  => 'NO',
			'start_value'   => '1',
		];

		if (version_compare(static::$connection->getVersion(), '10', 'ge'))
		{
			$sequence['data_type']     = 'integer';
			$sequence['maximum_value'] = '2147483647';
		}

		$this->assertEquals(
			[
				(object) $sequence
			],
			static::$connection->getTableSequences('#__dbtest')
		);
	}

	/**
	 * @testdox  The last value of a table sequence is returned
	 */
	public function testGetSequenceLastValue()
	{
		$this->assertTrue(
			\is_int(static::$connection->getSequenceLastValue(static::$connection->replacePrefix('#__dbtest_id_seq')))
		);
	}

	/**
	 * @testdox  The last value of a table sequence is returned
	 */
	public function testGetSequenceIsCalled()
	{
		$this->assertTrue(
			\is_bool(static::$connection->getSequenceIsCalled(static::$connection->replacePrefix('#__dbtest_id_seq')))
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
	 * @testdox  The database driver reports if it is supported in the present environment
	 */
	public function testIsSupported()
	{
		$this->assertTrue(
			PgsqlDriver::isSupported()
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
			'pgsql',
			static::$connection->getName()
		);
	}

	/**
	 * @testdox  The type of server for the database driver is retrieved
	 */
	public function testGetServerType()
	{
		$this->assertSame(
			'postgresql',
			static::$connection->getServerType()
		);
	}

	/**
	 * @testdox  The null date for the server type is retrieved
	 */
	public function testGetNullDate()
	{
		$this->assertSame(
			'1970-01-01 00:00:00',
			static::$connection->getNullDate()
		);
	}

	/**
	 * @testdox  An exporter for the database driver can be created
	 */
	public function testGetExporter()
	{
		$this->assertInstanceOf(
			PgsqlExporter::class,
			static::$connection->getExporter()
		);
	}

	/**
	 * @testdox  An importer for the database driver can be created
	 */
	public function testGetImporter()
	{
		$this->assertInstanceOf(
			PgsqlImporter::class,
			static::$connection->getImporter()
		);
	}

	/**
	 * @testdox  A new query instance can be created
	 */
	public function testGetQueryNewInstance()
	{
		$this->assertInstanceOf(
			PgsqlQuery::class,
			static::$connection->getQuery(true)
		);
	}
}
