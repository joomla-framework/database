<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\ParameterType;
use Joomla\Database\Tests\Cases\MysqliCase;

/**
 * Test class for Joomla\Database\Mysqli\MysqliDriver.
 *
 * @since  1.0
 */
class MysqliDriverTest extends MysqliCase
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
			array("'%_abc123", true, '\\\'\\%\_abc123'),
			array(3, false, 3),
			array(3.14, false, '3.14'),
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
	 * Test __destruct method.
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
	 * Test connected method.
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
		$this->assertThat(
			self::$driver->dropTable('#__bar', true),
			$this->isInstanceOf('\\Joomla\\Database\\Mysqli\\MysqliDriver'),
			'The table is dropped if present.'
		);
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
	 * @since         1.0
	 */
	public function testEscape($text, $extra, $expected)
	{
		$this->assertThat(
			self::$driver->escape($text, $extra),
			$this->equalTo($expected),
			'The string was not escaped properly'
		);
	}

	/**
	 * Tests the escape method 2.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testEscapeNonLocaleAware()
	{
		$origin = setLocale(LC_NUMERIC, 0);

		// Test with decimal_point equals to comma
		setLocale(LC_NUMERIC, 'pl_PL');

		$this->assertThat(
			self::$driver->escape(3.14),
			$this->equalTo('3.14'),
			'The string was not escaped properly'
		);

		// Test with C locale
		setLocale(LC_NUMERIC, 'C');

		$this->assertThat(
			self::$driver->escape(3.14),
			$this->equalTo('3.14'),
			'The string was not escaped properly'
		);

		// Revert to origin locale
		setLocale(LC_NUMERIC, $origin);
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
		$query = self::$driver->getQuery(true);
		$query->delete();
		$query->from('dbtest');
		self::$driver->setQuery($query);

		self::$driver->execute();

		$this->assertThat(self::$driver->getAffectedRows(), $this->equalTo(4), __LINE__);
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
		$this->assertThat(
			self::$driver->getCollation(),
			$this->equalTo('utf8_general_ci'),
			'Line:' . __LINE__ . ' The getCollation method should return the collation of the database.'
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
		$this->assertThat(
			self::$driver->getExporter(),
			$this->isInstanceOf('\\Joomla\\Database\\Mysqli\\MysqliExporter'),
			'Line:' . __LINE__ . ' The getExporter method should return the correct exporter.'
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
		$this->assertThat(
			self::$driver->getImporter(),
			$this->isInstanceOf('\\Joomla\\Database\\Mysqli\\MysqliImporter'),
			'Line:' . __LINE__ . ' The getImporter method should return the correct importer.'
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
		$query = self::$driver->getQuery(true);
		$query->select('*');
		$query->from('dbtest');
		$query->where('description = ' . self::$driver->quote('one'));
		self::$driver->setQuery($query);

		$res = self::$driver->execute();

		$this->assertThat(self::$driver->getNumRows($res), $this->equalTo(2), __LINE__);
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
		$this->assertThat(
			self::$driver->getTableCreate('#__dbtest'),
			$this->isType('array'),
			'The statement to create the table is returned in an array.'
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

		$this->assertThat(
			self::$driver->getTableColumns('dbtest'),
			$this->equalTo($tableCol),
			__LINE__
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

		$this->assertThat(
			self::$driver->getTableColumns('dbtest', false),
			$this->equalTo(
				array(
					'id' => $id,
					'title' => $title,
					'start_date' => $start_date,
					'description' => $description
				)
			),
			__LINE__
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
		$this->assertThat(
			self::$driver->getTableKeys('#__dbtest'),
			$this->isType('array'),
			'The list of keys for the table is returned in an array.'
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
		$this->assertThat(
			self::$driver->getTableList(),
			$this->isType('array'),
			'The list of tables for the database is returned in an array.'
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
		$this->assertThat(
			strlen(self::$driver->getVersion()),
			$this->greaterThan(0),
			'Line:' . __LINE__ . ' The getVersion method should return something without error.'
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
	 * Test insertObject method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testInsertObject()
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
		$query = self::$driver->getQuery(true);
		$query->select('title');
		$query->from('dbtest');
		self::$driver->setQuery($query);
		$result = self::$driver->loadAssoc();

		$this->assertThat($result, $this->equalTo(array('title' => 'Testing')), __LINE__);
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
		$query = self::$driver->getQuery(true);
		$query->select('title');
		$query->from('dbtest');
		self::$driver->setQuery($query);
		$result = self::$driver->loadAssocList();

		$this->assertThat(
			$result,
			$this->equalTo(
				array(
					array('title' => 'Testing'),
					array('title' => 'Testing2'),
					array('title' => 'Testing3'),
					array('title' => 'Testing4')
				)
			),
			__LINE__
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
		$query = self::$driver->getQuery(true);
		$query->select('title');
		$query->from('dbtest');
		self::$driver->setQuery($query);
		$result = self::$driver->loadColumn();

		$this->assertThat($result, $this->equalTo(array('Testing', 'Testing2', 'Testing3', 'Testing4')), __LINE__);
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
		$query = self::$driver->getQuery(true);
		$query->select('*');
		$query->from('dbtest');
		$query->where('description=' . self::$driver->quote('three'));
		self::$driver->setQuery($query);
		$result = self::$driver->loadObject();

		$objCompare = new \stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->description = 'three';

		$this->assertThat($result, $this->equalTo($objCompare), __LINE__);
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
		$query = self::$driver->getQuery(true);
		$query->select('*');
		$query->from('dbtest');
		$query->order('id');
		self::$driver->setQuery($query);
		$result = self::$driver->loadObjectList();

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

		$this->assertThat($result, $this->equalTo($expected), __LINE__);
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
		$query = self::$driver->getQuery(true);
		$query->select('id');
		$query->from('dbtest');
		$query->where('title=' . self::$driver->quote('Testing2'));

		self::$driver->setQuery($query);
		$result = self::$driver->loadResult();

		$this->assertThat($result, $this->equalTo(2), __LINE__);
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
		$query = self::$driver->getQuery(true);
		$query->select('*');
		$query->from('dbtest');
		$query->where('description=' . self::$driver->quote('three'));
		self::$driver->setQuery($query);
		$result = self::$driver->loadRow();

		$expected = array(3, 'Testing3', '1980-04-18 00:00:00', 'three');

		$this->assertThat($result, $this->equalTo($expected), __LINE__);
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
		$query = self::$driver->getQuery(true);
		$query->select('*');
		$query->from('dbtest');
		$query->where('description=' . self::$driver->quote('one'));
		self::$driver->setQuery($query);
		$result = self::$driver->loadRowList();

		$expected = array(array(1, 'Testing', '1980-04-18 00:00:00', 'one'), array(2, 'Testing2', '1980-04-18 00:00:00', 'one'));

		$this->assertThat($result, $this->equalTo($expected), __LINE__);
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
		$newTableName = 'bak_dbtest';

		self::$driver->renameTable('dbtest', $newTableName);

		// Check name change
		$tableList = self::$driver->getTableList();
		$this->assertThat(in_array($newTableName, $tableList), $this->isTrue(), __LINE__);

		// Restore initial state
		self::$driver->renameTable($newTableName, 'dbtest');
	}

	/**
	 * Test the execute method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testExecute()
	{
		self::$driver->setQuery(
			"REPLACE INTO `dbtest` SET `id` = 5, `title` = 'testTitle', `start_date` = '1980-04-18 00:00:00', `description` = 'Testing'"
		);

		$this->assertThat(self::$driver->execute(), $this->isTrue(), __LINE__);

		$this->assertThat(self::$driver->insertid(), $this->equalTo(5), __LINE__);
	}

	/**
	 * Test the execute method with an unnamed prepared statement
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testExecuteUnnamedPreparedStatement()
	{
		$id          = 5;
		$title       = 'testTitle';
		$startDate   = '1980-04-18 00:00:00';
		$description = 'Testing';

		/** @var \Joomla\Database\Mysqli\MysqliQuery $query */
		$query = self::$driver->getQuery(true);
		$query->setQuery(
			'REPLACE INTO `dbtest` SET `id` = ?, `title` = ?, `start_date` = ?, `description` = ?'
		);
		$query->bind(1, $id, ParameterType::INTEGER);
		$query->bind(2, $title);
		$query->bind(3, $startDate);
		$query->bind(4, $description);

		self::$driver->setQuery($query);

		$this->assertThat(self::$driver->execute(), $this->isTrue(), __LINE__);

		$this->assertThat(self::$driver->insertid(), $this->equalTo(5), __LINE__);
	}


	/**
	 * Test the execute method with a named prepared statement
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testExecuteNamedPreparedStatement()
	{
		$id          = 6;
		$title       = 'testTitleNamed';
		$startDate   = '1980-04-19 00:00:00';
		$description = 'NamedTesting';

		/** @var \Joomla\Database\Mysqli\MysqliQuery $query */
		$query = self::$driver->getQuery(true);
		$query->setQuery(
			'REPLACE INTO `dbtest` SET `id` = :id, `title` = :title, `start_date` = :start_date, `description` = :description123'
		);
		$query->bind(":description123", $description);
		$query->bind(":id", $id, ParameterType::INTEGER);
		$query->bind(":title", $title);
		$query->bind(":start_date", $startDate);

		self::$driver->setQuery($query);

		$this->assertThat(self::$driver->execute(), $this->isTrue(), __LINE__);

		$this->assertThat(self::$driver->insertid(), $this->equalTo(6), __LINE__);
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
		$queryIns = self::$driver->getQuery(true);
		$queryIns->insert('#__dbtest')
			->columns('id, title, start_date, description')
			->values("6, 'testTitle', '1970-01-01', 'testDescription'");

		self::$driver->setQuery($queryIns)->execute();

		self::$driver->transactionCommit();

		/* check if value is present */
		$queryCheck = self::$driver->getQuery(true);
		$queryCheck->select('*')
			->from('#__dbtest')
			->where('id = 6');
		self::$driver->setQuery($queryCheck);
		$result = self::$driver->loadRow();

		$expected = array('6', 'testTitle', '1970-01-01 00:00:00', 'testDescription');

		$this->assertThat($result, $this->equalTo($expected), __LINE__);
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
		$queryIns = self::$driver->getQuery(true);
		$queryIns->insert('#__dbtest')
			->columns('id, title, start_date, description')
			->values("7, 'testRollback', '1970-01-01', 'testRollbackSp'");
		self::$driver->setQuery($queryIns)->execute();

		/* create savepoint only if is passed by data provider */
		if (!is_null($toSavepoint))
		{
			self::$driver->transactionStart((boolean) $toSavepoint);
		}

		/* try to insert this tuple, always rolled back */
		$queryIns = self::$driver->getQuery(true);
		$queryIns->insert('#__dbtest')
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
		$queryCheck = self::$driver->getQuery(true);
		$queryCheck->select('*')
			->from('#__dbtest')
			->where("description = 'testRollbackSp'");
		self::$driver->setQuery($queryCheck);
		$result = self::$driver->loadRowList();

		$this->assertThat(count($result), $this->equalTo($tupleCount), __LINE__);
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
		$this->assertThat(\Joomla\Database\Mysqli\MysqliDriver::isSupported(), $this->isTrue(), __LINE__);
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

	/**
	 * Test getNullDate method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testgetNullDate()
	{
		$query    = self::$driver->getQuery(true);
		$result   = self::$driver->setQuery('SELECT @@SESSION.sql_mode;')->loadResult();
		$expected = '0000-00-00 00:00:00';
		
		if (strpos($result, 'NO_ZERO_DATE') !== false)
		{
			$expected = '1000-01-01 00:00:00';
		}

		$this->assertThat($expected, $this->equalTo(self::$driver->getNullDate()), __LINE__);
	}

	/**
	 * Test querySet method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testQuerySetWithUnionAll()
	{
		$query  = self::$driver->getQuery(true);
		$union1 = self::$driver->getQuery(true);
		$union2 = self::$driver->getQuery(true);

		$union1->select('id, title')->from('dbtest')->where('id = 4')->setLimit(1);

		$union2->select('id, title')->from('dbtest')->where('id < 4')->order('id DESC');
		$union2->setLimit(2, 1);

		$query->querySet($union1)->unionAll($union2)->order('id');

		$result = self::$driver->setQuery($query, 0, 3)->loadAssocList();

		$this->assertThat(
			$result,
			$this->equalTo(
				array(
					array('id' => '1', 'title' => 'Testing'),
					array('id' => '2', 'title' => 'Testing2'),
					array('id' => '4', 'title' => 'Testing4'),
				)
			),
			__LINE__
		);
	}

	/**
	 * Test toQuerySet method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testSelectToQuerySetWithUnionAll()
	{
		$query = self::$driver->getQuery(true);
		$union = self::$driver->getQuery(true);

		$query->select('id, title')->from('dbtest')->where('id = 4');
		$query = $query->setLimit(1)->toQuerySet();

		$union->select('id, title')->from('dbtest')->where('id < 4')->order('id DESC');
		$union->setLimit(2, 1);

		$query->unionAll($union)->order('id');

		$result = self::$driver->setQuery($query)->loadAssocList();

		$this->assertThat(
			$result,
			$this->equalTo(
				array(
					array('id' => '1', 'title' => 'Testing'),
					array('id' => '2', 'title' => 'Testing2'),
					array('id' => '4', 'title' => 'Testing4'),
				)
			),
			__LINE__
		);
	}
}
