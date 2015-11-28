<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysql;

use Joomla\Database\Mysql\MysqlDriver;
use Joomla\Database\Tests\Cases\MysqlCase;

/**
 * Test class for Joomla\Database\Mysql\MysqlDriver.
 *
 * @since  1.0
 */
class MysqlDriverTest extends MysqlCase
{
	/**
	 * Data for the testEscape test.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function dataTestEscape()
	{
		return array(
			array("'%_abc123", false, '\\\'%_abc123'),
			array("'%_abc123", true, '\\\'\\%\_abc123')
		);
	}

	/**
	 * Data for the testTransactionRollback test.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function dataTestTransactionRollback()
	{
		return array(array(null, 0), array('transactionSavepoint', 1));
	}

	/**
	 * Tests the __destruct method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function test__destruct()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the connected method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testConnected()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the dropTable method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testDropTable()
	{
		$db = self::$driver;

		// Create #__bar table first
		$db->setQuery('CREATE TABLE IF NOT EXISTS ' . $db->quoteName('#__bar') . ' (' . $db->quoteName('id') . ' int(10) unsigned NOT NULL);')
			->execute();

		// Check return self or not.
		$this->assertInstanceOf(
			'\\Joomla\\Database\\Mysql\\MysqlDriver',
			$db->dropTable('#__bar', true)
		);

		// Check is table dropped.
		$exists = $db->setQuery('SHOW TABLES LIKE ' . $db->quote('%#__bar%'))->loadResult();

		$this->assertNull($exists);
	}

	/**
	 * Tests the escape method.
	 *
	 * @param   string   $text      The string to be escaped.
	 * @param   boolean  $extra     Optional parameter to provide extra escaping.
	 * @param   string   $expected  The expected result.
	 *
	 * @return  void
	 *
	 * @dataProvider  dataTestEscape
	 * @since      1.0
	 */
	public function testEscape($text, $extra, $expected)
	{
		$this->assertEquals(
			$expected,
			self::$driver->escape($text, $extra)
		);
	}

	/**
	 * Test getAffectedRows method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetAffectedRows()
	{
		$query = self::$driver->getQuery(true)
			->delete()
			->from('jos_dbtest');
		self::$driver->setQuery($query)->execute();

		$this->assertSame(4, self::$driver->getAffectedRows());
	}

	/**
	 * Test getCollation method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetCollation()
	{
		$this->assertSame(
			'utf8_general_ci',
			self::$driver->getCollation()
		);
	}

	/**
	 * Test getExporter method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetExporter()
	{
		$this->assertInstanceOf(
			'\\Joomla\\Database\\Mysql\\MysqlExporter',
			self::$driver->getExporter()
		);
	}

	/**
	 * Test getImporter method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetImporter()
	{
		$this->assertInstanceOf(
			'\\Joomla\\Database\\Mysql\\MysqlImporter',
			self::$driver->getImporter()
		);
	}

	/**
	 * Test getNumRows method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetNumRows()
	{
		$query = self::$driver->getQuery(true)
			->select('*')
			->from('jos_dbtest')
			->where('description = ' . self::$driver->quote('one'));

		$res = self::$driver->setQuery($query)->execute();

		$this->assertSame(2, self::$driver->getNumRows($res));
	}

	/**
	 * Tests the getTableCreate method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTableCreate()
	{
		$this->assertInternalType(
			'array',
			self::$driver->getTableCreate('#__dbtest')
		);
	}

	/**
	 * Test getTableColumns method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTableColumns()
	{
		$tableCol = array('id' => 'int unsigned', 'title' => 'varchar', 'start_date' => 'datetime', 'description' => 'text');

		$this->assertSame(
			$tableCol,
			self::$driver->getTableColumns('jos_dbtest')
		);

		/* not only type field */
		$id = new \stdClass;
		$id->Default    = null;
		$id->Field      = 'id';
		$id->Type       = 'int(10) unsigned';
		$id->Null       = 'NO';
		$id->Key        = 'PRI';
		$id->Collation  = null;
		$id->Extra      = 'auto_increment';
		$id->Privileges = 'select,insert,update,references';
		$id->Comment    = '';

		$title = new \stdClass;
		$title->Default    = null;
		$title->Field      = 'title';
		$title->Type       = 'varchar(50)';
		$title->Null       = 'NO';
		$title->Key        = '';
		$title->Collation  = 'utf8_general_ci';
		$title->Extra      = '';
		$title->Privileges = 'select,insert,update,references';
		$title->Comment    = '';

		$start_date = new \stdClass;
		$start_date->Default    = null;
		$start_date->Field      = 'start_date';
		$start_date->Type       = 'datetime';
		$start_date->Null       = 'NO';
		$start_date->Key        = '';
		$start_date->Collation  = null;
		$start_date->Extra      = '';
		$start_date->Privileges = 'select,insert,update,references';
		$start_date->Comment    = '';

		$description = new \stdClass;
		$description->Default    = null;
		$description->Field      = 'description';
		$description->Type       = 'text';
		$description->Null       = 'NO';
		$description->Key        = '';
		$description->Collation  = 'utf8_general_ci';
		$description->Extra      = '';
		$description->Privileges = 'select,insert,update,references';
		$description->Comment    = '';

		$this->assertEquals(
			array(
				'id' => $id,
				'title' => $title,
				'start_date' => $start_date,
				'description' => $description
			),
			self::$driver->getTableColumns('jos_dbtest', false)
		);
	}

	/**
	 * Tests the getTableKeys method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTableKeys()
	{
		$this->assertInternalType(
			'array',
			self::$driver->getTableKeys('#__dbtest')
		);
	}

	/**
	 * Tests the getTableList method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTableList()
	{
		$this->assertInternalType(
			'array',
			self::$driver->getTableList()
		);
	}

	/**
	 * Test getVersion method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetVersion()
	{
		$this->assertGreaterThan(
			0,
			strlen(self::$driver->getVersion())
		);
	}

	/**
	 * Test insertid method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testInsertid()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test loadAssoc method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadAssoc()
	{
		$query = self::$driver->getQuery(true)
			->select('title')
			->from('jos_dbtest');

		$result = self::$driver->setQuery($query)->loadAssoc();

		$this->assertSame(array('title' => 'Testing'), $result);
	}

	/**
	 * Test loadAssocList method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadAssocList()
	{
		$query = self::$driver->getQuery(true)
			->select('title')
			->from('jos_dbtest');

		$result = self::$driver->setQuery($query)->loadAssocList();

		$this->assertSame(
			array(
				array('title' => 'Testing'),
				array('title' => 'Testing2'),
				array('title' => 'Testing3'),
				array('title' => 'Testing4')
			),
			$result
		);
	}

	/**
	 * Test loadColumn method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadColumn()
	{
		$query = self::$driver->getQuery(true)
			->select('title')
			->from('jos_dbtest');

		$result = self::$driver->setQuery($query)->loadColumn();

		$this->assertSame(array('Testing', 'Testing2', 'Testing3', 'Testing4'), $result);
	}

	/**
	 * Test loadObject method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadObject()
	{
		$query = self::$driver->getQuery(true)
			->select('*')
			->from('jos_dbtest')
			->where('description = ' . self::$driver->quote('three'));

		$result = self::$driver->setQuery($query)->loadObject();

		$objCompare = new \stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'three';

		$this->assertEquals($objCompare, $result);
	}

	/**
	 * Test loadObjectList method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadObjectList()
	{
		$query = self::$driver->getQuery(true)
			->select('*')
			->from('jos_dbtest')
			->order('id');

		$result = self::$driver->setQuery($query)->loadObjectList();

		$expected = array();

		$objCompare = new \stdClass;
		$objCompare->id = 1;
		$objCompare->title = 'Testing';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'one';

		$expected[] = clone $objCompare;

		$objCompare = new \stdClass;
		$objCompare->id = 2;
		$objCompare->title = 'Testing2';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'one';

		$expected[] = clone $objCompare;

		$objCompare = new \stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'three';

		$expected[] = clone $objCompare;

		$objCompare = new \stdClass;
		$objCompare->id = 4;
		$objCompare->title = 'Testing4';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'four';

		$expected[] = clone $objCompare;

		$this->assertEquals($expected, $result);
	}

	/**
	 * Test loadResult method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadResult()
	{
		$query = self::$driver->getQuery(true)
			->select('id')
			->from('jos_dbtest')
			->where('title = ' . self::$driver->quote('Testing2'));

		$result = self::$driver->setQuery($query)->loadResult();

		$this->assertSame(2, (int) $result);
	}

	/**
	 * Test loadRow method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadRow()
	{
		$query = self::$driver->getQuery(true)
			->select('*')
			->from('jos_dbtest')
			->where('description = ' . self::$driver->quote('three'));

		$result = self::$driver->setQuery($query)->loadRow();

		$this->assertSame(array('3', 'Testing3', '1980-04-18 00:00:00', 'three'), $result);
	}

	/**
	 * Test loadRowList method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testLoadRowList()
	{
		$query = self::$driver->getQuery(true)
			->select('*')
			->from('jos_dbtest')
			->where('description = ' . self::$driver->quote('one'));

		$result = self::$driver->setQuery($query)->loadRowList();

		$expected = array(array('1', 'Testing', '1980-04-18 00:00:00', 'one'), array('2', 'Testing2', '1980-04-18 00:00:00', 'one'));

		$this->assertSame($expected, $result);
	}

	/**
	 * Test the execute method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testExecute()
	{
		self::$driver->setQuery(
			"REPLACE INTO `jos_dbtest` SET `id` = 5, `title` = 'testTitle', `start_date` = '1980-04-18 00:00:00', `description` = 'Testing'"
		);

		$this->assertTrue((bool) self::$driver->execute());

		$this->assertEquals(5, self::$driver->insertid());
	}

	/**
	 * Tests the renameTable method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testRenameTable()
	{
		$newTableName = 'bak_jos_dbtest';

		self::$driver->renameTable('jos_dbtest', $newTableName);

		// Check name change
		$tableList = self::$driver->getTableList();
		$this->assertTrue(in_array($newTableName, $tableList));

		// Restore initial state
		self::$driver->renameTable($newTableName, 'jos_dbtest');
	}

	/**
	 * Test select method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSelect()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test setUtf method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetUtf()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the transactionCommit method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testTransactionCommit()
	{
		self::$driver->transactionStart();
		$queryIns = self::$driver->getQuery(true)
			->insert('#__dbtest')
			->columns('id, title, start_date, description')
			->values("6, 'testTitle', '1970-01-01', 'testDescription'");

		self::$driver->setQuery($queryIns)->execute();

		self::$driver->transactionCommit();

		/* check if value is present */
		$queryCheck = self::$driver->getQuery(true)
			->select('*')
			->from('#__dbtest')
			->where('id = 6');

		$result = self::$driver->setQuery($queryCheck)->loadRow();

		$expected = array('6', 'testTitle', '1970-01-01 00:00:00', 'testDescription');

		$this->assertSame($expected, $result);
	}

	/**
	 * Tests the transactionRollback method, with and without savepoint.
	 *
	 * @param   string  $toSavepoint  Savepoint name to rollback transaction to
	 * @param   int     $tupleCount   Number of tuple found after insertion and rollback
	 *
	 * @return  void
	 *
	 * @since        1.0
	 * @dataProvider dataTestTransactionRollback
	 */
	public function testTransactionRollback($toSavepoint, $tupleCount)
	{
		self::$driver->transactionStart();

		/* try to insert this tuple, inserted only when savepoint != null */
		$queryIns = self::$driver->getQuery(true)
			->insert('#__dbtest')
			->columns('id, title, start_date, description')
			->values("7, 'testRollback', '1970-01-01', 'testRollbackSp'");
		self::$driver->setQuery($queryIns)->execute();

		/* create savepoint only if is passed by data provider */
		if (!is_null($toSavepoint))
		{
			self::$driver->transactionStart((boolean) $toSavepoint);
		}

		/* try to insert this tuple, always rolled back */
		$queryIns = self::$driver->getQuery(true)
			->insert('#__dbtest')
			->columns('id, title, start_date, description')
			->values("8, 'testRollback', '1972-01-01', 'testRollbackSp'");
		self::$driver->setQuery($queryIns)->execute();

		self::$driver->transactionRollback((boolean) $toSavepoint);

		/* release savepoint and commit only if a savepoint exists */
		if (!is_null($toSavepoint))
		{
			self::$driver->transactionCommit();
		}

		/* find how many rows have description='testRollbackSp' :
		 *   - 0 if a savepoint doesn't exist
		 *   - 1 if a savepoint exists
		 */
		$queryCheck = self::$driver->getQuery(true)
			->select('*')
			->from('#__dbtest')
			->where("description = 'testRollbackSp'");

		$result = self::$driver->setQuery($queryCheck)->loadRowList();

		$this->assertCount($tupleCount, $result);
	}

	/**
	 * Test isSupported method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testIsSupported()
	{
		$this->assertThat(MysqlDriver::isSupported(), $this->isTrue(), __LINE__);
	}

	/**
	 * Test updateObject method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testUpdateObject()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
}
