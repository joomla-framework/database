<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlsrv;

use Joomla\Database\Sqlsrv\SqlsrvDriver;
use Joomla\Database\Tests\Cases\SqlsrvCase;

/**
 * Test class for \Joomla\Database\Sqlsrv\SqlsrvDriver.
 *
 * @since  1.0
 */
class SqlsrvDriverTest extends SqlsrvCase
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
			array("'%_abc123[]", false, "''%_abc123[]"),
			array("'%_abc123[]", true, "''[%][_]abc123[[]]"),
			array("binary\000data", false, "binary' + CHAR(0) + N'data"),
			array(3, false, 3),
			array(3.14, false, '3.14'),
		);
	}

	/**
	 * Data for the testQuoteBinary test.
	 *
	 * @return  array
	 *
	 * @since   1.7.0
	 */
	public function dataTestQuoteBinary()
	{
		return array(
			array('DATA', "0x" . bin2hex('DATA')),
			array("\x00\x01\x02\xff", "0x000102ff"),
			array("\x01\x01\x02\xff", "0x010102ff"),
		);
	}

	/**
	 * Data for the testQuoteName test.
	 *
	 * @return  array
	 *
	 * @since   1.7.0
	 */
	public function dataTestQuoteName()
	{
		return array(
			array('protected`title', null, '[protected`title]'),
			array('protected"title', null, '[protected"title]'),
			array('protected]title', null, '[protected]]title]'),
		);
	}

	/**
	 * Tests the destructor
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement test__destruct().
	 */
	public function test__destruct()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test the connected method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testConnected().
	 */
	public function testConnected()
	{
		// Remove the following lines when you implement this test.
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
			$this->isInstanceOf('\\Joomla\\Database\\Sqlsrv\\SqlsrvDriver'),
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
	 * Test the quoteBinary method.
	 *
	 * @param   string  $data  The binary quoted input string.
	 *
	 * @return  void
	 *
	 * @dataProvider  dataTestQuoteBinary
	 * @since         1.7.0
	 */
	public function testQuoteBinary($data, $expected)
	{
		$this->assertThat(
			self::$driver->quoteBinary($data),
			$this->equalTo($expected),
			'The binary data was not quoted properly'
		);
	}

	/**
	 * Test the quoteName method.
	 *
	 * @param   string  $text      The column name or alias to be quote.
	 * @param   string  $asPart    String used for AS query part.
	 * @param   string  $expected  The expected result.
	 *
	 * @return  void
	 *
	 * @dataProvider  dataTestQuoteName
	 * @since         1.7.0
	 */
	public function testQuoteName($text, $asPart, $expected)
	{
		$this->assertThat(
			self::$driver->quoteName($text, $asPart),
			$this->equalTo($expected),
			'The name was not quoted properly'
		);
	}

	/**
	 * Tests the getAffectedRows method
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
	 * Tests the getCollation method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testGetCollation().
	 */
	public function testGetCollation()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the getExporter method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testGetExporter().
	 */
	public function testGetExporter()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('Implement this test when the exporter is added.');
	}

	/**
	 * Tests the getImporter method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testGetImporter().
	 */
	public function testGetImporter()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('Implement this test when the importer is added.');
	}

	/**
	 * Tests the getNumRows method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testGetNumRows().
	 */
	public function testGetNumRows()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
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
			$this->isType('string'),
			'A blank string is returned since this is not supported on SQL Server.'
		);
	}

	/**
	 * Tests the getTableColumns method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testGetTableColumns().
	 */
	public function testGetTableColumns()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
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
	 * Tests the getVersion method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetVersion()
	{
		$this->assertThat(
			self::$driver->getVersion(),
			$this->isType('string'),
			'Line:' . __LINE__ . ' The getVersion method should return a string containing the driver version.'
		);
	}

	/**
	 * Tests the insertid method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testInsertid().
	 */
	public function testInsertid()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the loadAssoc method
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
	 * Tests the loadAssocList method
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
	 * Tests the loadColumn method
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
	 * Tests the loadObject method
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
		$objCompare->start_date = '1980-04-18 00:00:00.000';
		$objCompare->description = 'three';
		$objCompare->data = null;

		$this->assertThat($result, $this->equalTo($objCompare), __LINE__);
	}

	/**
	 * Tests the loadObjectList method
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
		$objCompare->start_date = '1980-04-18 00:00:00.000';
		$objCompare->description = 'one';
		$objCompare->data = null;

		$expected[] = clone $objCompare;

		$objCompare = new \stdClass;
		$objCompare->id = 2;
		$objCompare->title = 'Testing2';
		$objCompare->start_date = '1980-04-18 00:00:00.000';
		$objCompare->description = 'one';
		$objCompare->data = null;

		$expected[] = clone $objCompare;

		$objCompare = new \stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00.000';
		$objCompare->description = 'three';
		$objCompare->data = null;

		$expected[] = clone $objCompare;

		$objCompare = new \stdClass;
		$objCompare->id = 4;
		$objCompare->title = 'Testing4';
		$objCompare->start_date = '1980-04-18 00:00:00.000';
		$objCompare->description = 'four';
		$objCompare->data = null;

		$expected[] = clone $objCompare;

		$this->assertThat($result, $this->equalTo($expected), __LINE__);
	}

	/**
	 * Tests the loadResult method
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
	 * Tests the loadRow method
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

		$expected = array(3, 'Testing3', '1980-04-18 00:00:00.000', 'three', null);

		$this->assertThat($result, $this->equalTo($expected), __LINE__);
	}

	/**
	 * Tests the loadRowList method
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

		$expected = array(
			array(1, 'Testing', '1980-04-18 00:00:00.000', 'one', null),
			array(2, 'Testing2', '1980-04-18 00:00:00.000', 'one', null)
		);

		$this->assertThat($result, $this->equalTo($expected), __LINE__);
	}

	/**
	 * Test quoteBinary and decodeBinary methods
	 *
	 * @return  void
	 *
	 * @since   1.7.0
	 */
	public function testLoadBinary()
	{
		// Add binary data with null byte
		$query = self::$driver->getQuery(true)
			->update('dbtest')
			->set('data = ' . self::$driver->quoteBinary("\x00\x01\x02\xff"))
			->where('id = 3');

		self::$driver->setQuery($query)->execute();

		// Add binary data with invalid UTF-8
		$query = self::$driver->getQuery(true)
			->update('dbtest')
			->set('data = ' . self::$driver->quoteBinary("\x01\x01\x02\xff"))
			->where('id = 4');

		self::$driver->setQuery($query)->execute();

		$selectRow3 = self::$driver->getQuery(true)
			->select('id')
			->from('dbtest')
			->where('data = ' . self::$driver->quoteBinary("\x00\x01\x02\xff"));

		$selectRow4 = self::$driver->getQuery(true)
			->select('id')
			->from('dbtest')
			->where('data = '. self::$driver->quoteBinary("\x01\x01\x02\xff"));

		$result = self::$driver->setQuery($selectRow3)->loadResult();
		$this->assertThat($result, $this->equalTo(3), __LINE__);

		$result = self::$driver->setQuery($selectRow4)->loadResult();
		$this->assertThat($result, $this->equalTo(4), __LINE__);

		$selectRows = self::$driver->getQuery(true)
			->select('data')
			->from('dbtest')
			->order('id');

		// Test loadColumn
		$result = self::$driver->setQuery($selectRows)->loadColumn();

		foreach ($result as $i => $v)
		{
			$result[$i] = self::$driver->decodeBinary($v);
		}

		$expected = array(null, null, "\x00\x01\x02\xff", "\x01\x01\x02\xff");
		$this->assertThat($result, $this->equalTo($expected), __LINE__);

		// Test loadAssocList
		$result = self::$driver->setQuery($selectRows)->loadAssocList();

		foreach ($result as $i => $v)
		{
			$result[$i]['data'] = self::$driver->decodeBinary($v['data']);
		}

		$expected = array(
			array('data' => null),
			array('data' => null),
			array('data' => "\x00\x01\x02\xff"),
			array('data' => "\x01\x01\x02\xff"),
		);
		$this->assertThat($result, $this->equalTo($expected), __LINE__);

		// Test loadObjectList
		$result = self::$driver->setQuery($selectRows)->loadObjectList();

		foreach ($result as $i => $v)
		{
			$result[$i]->data = self::$driver->decodeBinary($v->data);
		}

		$expected = array(
			(object) array('data' => null),
			(object) array('data' => null),
			(object) array('data' => "\x00\x01\x02\xff"),
			(object) array('data' => "\x01\x01\x02\xff"),
		);
		$this->assertThat($result, $this->equalTo($expected), __LINE__);
	}

	/**
	 * Tests the execute method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testExecute()
	{
		self::$driver->setQuery(
			"INSERT INTO [dbtest] ([title],[start_date],[description]) VALUES ('testTitle','2013-04-01 00:00:00.000','description')"
		);

		$this->assertNotEquals(self::$driver->execute(), false, __LINE__);
	}

	/**
	 * Test the execute method with a prepared statement
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testExecutePreparedStatement()
	{
		$title       = 'testTitle';
		$startDate   = '2013-04-01 00:00:00.000';
		$description = 'description';

		/** @var \Joomla\Database\Sqlsrv\SqlsrvQuery $query */
		$query = self::$driver->getQuery(true);
		$query->insert('dbtest')
			->columns('title,start_date,description')
			->values('?, ?, ?');
		$query->bind(1, $title);
		$query->bind(2, $startDate);
		$query->bind(3, $description);

		self::$driver->setQuery($query);

		$this->assertNotEquals(self::$driver->execute(), false, __LINE__);
	}

	/**
	 * Tests the renameTable method
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
		$this->assertThat(\in_array($newTableName, $tableList), $this->isTrue(), __LINE__);

		// Restore initial state
		self::$driver->renameTable($newTableName, 'dbtest');
	}

	/**
	 * Tests the select method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testSelect().
	 */
	public function testSelect()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the setUtf method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testSetUtf().
	 */
	public function testSetUtf()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Tests the isSupported method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testIsSupported()
	{
		$this->assertThat(
			\Joomla\Database\Sqlsrv\SqlsrvDriver::isSupported(),
			$this->isTrue(),
			__LINE__
		);
	}

	/**
	 * Tests the updateObject method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @todo    Implement testUpdateObject().
	 */
	public function testUpdateObject()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
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
