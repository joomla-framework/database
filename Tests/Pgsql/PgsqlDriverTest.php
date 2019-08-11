<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Pgsql;

use Joomla\Database\Tests\Cases\PgsqlCase;

/**
 * Test class for Joomla\Database\Pgsql\PgsqlDriver.
 *
 * @since  1.0
 */
class PgsqlDriverTest extends PgsqlCase
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
			/* ' will be escaped and become '' */
			array("'%_abc123", false, '\'\'%_abc123'),
			array("'%_abc123", true, '\'\'%_abc123'),
			/* ' and \ will be escaped: the first become '', the latter \\ */
			array("\'%_abc123", false, '\\\\\'\'%_abc123'),
			array("\'%_abc123", true, '\\\\\'\'%_abc123'),
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
			array('DATA', "decode('" . bin2hex('DATA') . "', 'hex')"),
			array("\x00\x01\x02\xff", "decode('000102ff', 'hex')"),
			array("\x01\x01\x02\xff", "decode('010102ff', 'hex')"),
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
	 * Data for the getCreateDbQuery test.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function dataGetCreateDbQuery()
	{
		$obj = new \stdClass;
		$obj->db_user = 'testName';
		$obj->db_name = 'testDb';

		return array(array($obj, false), array($obj, true));
	}

	/**
	 * Data for the TestReplacePrefix test.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function dataTestReplacePrefix()
	{
		return array(
			/* no prefix inside, no change */
			array('SELECT * FROM table', '#__', 'SELECT * FROM table'),
			/* the prefix inside double quote has to be changed */
			array('SELECT * FROM "#__table"', '#__', 'SELECT * FROM "table"'),
			/* the prefix inside single quote hasn't to be changed */
			array('SELECT * FROM \'#__table\'', '#__', 'SELECT * FROM \'#__table\''),
			/* mixed quote case */
			array('SELECT * FROM \'#__table\', "#__tableSecond"', '#__', 'SELECT * FROM \'#__table\', "tableSecond"'),
			/* the prefix used in sequence name (single quote) has to be changed */
			array('SELECT * FROM currval(\'#__table_id_seq\'::regclass)', '#__', 'SELECT * FROM currval(\'table_id_seq\'::regclass)'),
			/* using another prefix */
			array('SELECT * FROM "#!-_table"', '#!-_', 'SELECT * FROM "table"'));
	}

	/**
	 * Data for testQuoteName test.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function dataTestQuoteName()
	{
		return array(
			/* test escape double quote */
			array('protected`title', null, '"protected`title"'),
			array('protected"title', null, '"protected""title"'),
			array('protected]title', null, '"protected]title"'),
			/* no dot inside var */
			array('dbtest', null, '"dbtest"'),
			/* a dot inside var */
			array('public.dbtest', null, '"public"."dbtest"'),
			/* two dot inside var */
			array('joomla_ut.public.dbtest', null, '"joomla_ut"."public"."dbtest"'),
			/* using an array */
			array(array('joomla_ut', 'dbtest'), null, array('"joomla_ut"', '"dbtest"')),
			/* using an array with dotted name */
			array(array('joomla_ut.dbtest', 'public.dbtest'), null, array('"joomla_ut"."dbtest"', '"public"."dbtest"')),
			/* using an array with two dot in name */
			array(array('joomla_ut.public.dbtest', 'public.dbtest.col'), null, array('"joomla_ut"."public"."dbtest"', '"public"."dbtest"."col"')),

			/*** same tests with AS part ***/
			array('dbtest', 'test', '"dbtest" AS "test"'),
			array('public.dbtest', 'tst', '"public"."dbtest" AS "tst"'),
			array('joomla_ut.public.dbtest', 'tst', '"joomla_ut"."public"."dbtest" AS "tst"'),
			array(array('joomla_ut', 'dbtest'), array('j_ut', 'tst'), array('"joomla_ut" AS "j_ut"', '"dbtest" AS "tst"')),
			array(
				array('joomla_ut.dbtest', 'public.dbtest'),
				array('j_ut_db', 'pub_tst'),
				array('"joomla_ut"."dbtest" AS "j_ut_db"', '"public"."dbtest" AS "pub_tst"')),
			array(
				array('joomla_ut.public.dbtest', 'public.dbtest.col'),
				array('j_ut_p_db', 'pub_tst_col'),
				array('"joomla_ut"."public"."dbtest" AS "j_ut_p_db"', '"public"."dbtest"."col" AS "pub_tst_col"')),
			/* last test but with one null inside array */
			array(
				array('joomla_ut.public.dbtest', 'public.dbtest.col'),
				array('j_ut_p_db', null),
				array('"joomla_ut"."public"."dbtest" AS "j_ut_p_db"', '"public"."dbtest"."col"')));
	}

	/**
	 * Test destruct
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
	 * Check if connected() method returns true.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testConnected()
	{
		$this->assertThat(
			self::$driver->connected(),
			$this->equalTo(true),
			'Not connected to database'
		);
	}

	/**
	 * Tests the escape method.
	 *
	 * @param   string  $text    The string to be escaped.
	 * @param   bool    $extra   Optional parameter to provide extra escaping.
	 * @param   string  $result  Correct string escaped
	 *
	 * @return  void
	 *
	 * @since         1.0
	 * @dataProvider  dataTestEscape
	 */
	public function testEscape($text, $extra, $result)
	{
		$this->assertThat(
			self::$driver->escape($text, $extra),
			$this->equalTo($result),
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
	 * Tests the getCollation method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetCollation()
	{
		$this->assertNotEmpty(self::$driver->getCollation(), __LINE__);
	}

	/**
	 * Tests the getNumRows method.
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
		$query->where('description=' . self::$driver->quote('one'));
		self::$driver->setQuery($query);

		$res = self::$driver->execute();

		$this->assertThat(self::$driver->getNumRows($res), $this->equalTo(2), __LINE__);
	}

	/**
	 * Test getTableCreate function
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTableCreate()
	{
		$this->assertThat(
			self::$driver->getTableCreate('dbtest'),
			$this->equalTo(''),
			__LINE__
		);
	}

	/**
	 * Test getTableColumns function.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTableColumns()
	{
		$tableCol = array(
			'id' => 'integer',
			'title' => 'character varying',
			'start_date' => 'timestamp without time zone',
			'end_date' => 'timestamp without time zone',
			'description' => 'text',
			'data' => 'bytea',
		);

		$this->assertThat(self::$driver->getTableColumns('dbtest'), $this->equalTo($tableCol), __LINE__);

		/* not only type field */
		$id = new \stdClass;
		$id->column_name = 'id';
		$id->Field = 'id';
		$id->type = 'integer';
		$id->Type = 'integer';
		$id->null = 'NO';
		$id->Null = 'NO';
		$id->Default = 'nextval(\'dbtest_id_seq\'::regclass)';
		$id->comments = '';

		$title = new \stdClass;
		$title->column_name = 'title';
		$title->Field = 'title';
		$title->type = 'character varying(50)';
		$title->Type = 'character varying(50)';
		$title->null = 'NO';
		$title->Null = 'NO';
		$title->Default = null;
		$title->comments = '';

		$start_date = new \stdClass;
		$start_date->column_name = 'start_date';
		$start_date->Field = 'start_date';
		$start_date->type = 'timestamp without time zone';
		$start_date->Type = 'timestamp without time zone';
		$start_date->null = 'NO';
		$start_date->Null = 'NO';
		$start_date->Default = null;
		$start_date->comments = '';

		$end_date = new \stdClass;
		$end_date->column_name = 'end_date';
		$end_date->Field = 'end_date';
		$end_date->type = 'timestamp without time zone';
		$end_date->Type = 'timestamp without time zone';
		$end_date->null = 'NO';
		$end_date->Null = 'NO';
		$end_date->Default = '1970-01-01 00:00:00';
		$end_date->comments = '';

		$description = new \stdClass;
		$description->column_name = 'description';
		$description->Field = 'description';
		$description->type = 'text';
		$description->Type = 'text';
		$description->null = 'NO';
		$description->Null = 'NO';
		$description->Default = null;
		$description->comments = '';

		$data = new \stdClass;
		$data->column_name = 'data';
		$data->Field = 'data';
		$data->type = 'bytea';
		$data->Type = 'bytea';
		$data->null = 'YES';
		$data->Null = 'YES';
		$data->Default = null;
		$data->comments = '';

		$this->assertThat(
			self::$driver->getTableColumns('dbtest', false),
			$this->equalTo(
				array(
					'id' => $id,
					'title' => $title,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'description' => $description,
					'data' => $data,
				)
			),
			__LINE__
		);
	}

	/**
	 * Test getTableKeys function.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTableKeys()
	{
		$pkey = new \stdClass;
		$pkey->idxName = 'assets_pkey';
		$pkey->isPrimary = true;
		$pkey->isUnique = true;
		$pkey->indKey = '1';
		$pkey->Query = 'ALTER TABLE assets ADD PRIMARY KEY (id)';

		$asset = new \stdClass;
		$asset->idxName = 'idx_asset_name';
		$asset->isPrimary = false;
		$asset->isUnique = true;
		$asset->indKey = '6';
		$asset->Query = 'CREATE UNIQUE INDEX idx_asset_name ON assets USING btree (name)';

		$lftrgt = new \stdClass;
		$lftrgt->idxName = 'assets_idx_lft_rgt';
		$lftrgt->isPrimary = false;
		$lftrgt->isUnique = false;
		$lftrgt->indKey = '3 4';
		$lftrgt->Query = 'CREATE INDEX assets_idx_lft_rgt ON assets USING btree (lft, rgt)';

		$id = new \stdClass;
		$id->idxName = 'assets_idx_parent_id';
		$id->isPrimary = false;
		$id->isUnique = false;
		$id->indKey = '2';
		$id->Query = 'CREATE INDEX assets_idx_parent_id ON assets USING btree (parent_id)';

		$this->assertThat(self::$driver->getTableKeys('assets'), $this->equalTo(array($pkey, $id, $lftrgt, $asset)), __LINE__);
	}

	/**
	 * Test getTableSequences function.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetTableSequences()
	{
		$seq = new \stdClass;
		$seq->sequence = 'dbtest_id_seq';
		$seq->schema = 'public';
		$seq->table = 'dbtest';
		$seq->column = 'id';
		$seq->data_type = 'bigint';
		$seq->start_value = '1';
		$seq->minimum_value = '1';
		$seq->maximum_value = '9223372036854775807';
		$seq->increment = '1';
		$seq->cycle_option = 'NO';

		if (version_compare(self::$driver->getVersion(), '10', 'ge'))
		{
			$seq->data_type = 'integer';
			$seq->maximum_value = '2147483647';
		}

		$this->assertThat(self::$driver->getTableSequences('dbtest'), $this->equalTo(array($seq)), __LINE__);
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
		$expected = array(
			'0'  => 'assets',
			'1'  => 'categories',
			'2'  => 'content',
			'3'  => 'core_log_searches',
			'4'  => 'dbtest',
			'5'  => 'extensions',
			'6'  => 'languages',
			'7'  => 'log_entries',
			'8'  => 'menu',
			'9'  => 'menu_types',
			'10' => 'modules',
			'11' => 'modules_menu',
			'12' => 'schemas',
			'13' => 'session',
			'14' => 'update_categories',
			'15' => 'update_sites',
			'16' => 'update_sites_extensions',
			'17' => 'updates',
			'18' => 'user_profiles',
			'19' => 'user_usergroup_map',
			'20' => 'usergroups',
			'21' => 'users',
			'22' => 'viewlevels'
		);

		$result = self::$driver->getTableList();

		// Assert array size
		$this->assertThat(\count($result), $this->equalTo(count($expected)), __LINE__);

		// Clear found element to check if all elements are present in any order
		foreach ($result as $k => $v)
		{
			if (\in_array($v, $expected))
			{
				// Ok case, value found so set value to zero
				$result[$k] = '0';
			}
			else
			{
				// Error case, value NOT found so set value to one
				$result[$k] = '1';
			}
		}

		// If there's a one it will return true and test fails
		$this->assertThat(\in_array('1', $result), $this->equalTo(false), __LINE__);
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
		$versionRow = self::$driver->setQuery('SELECT version();')->loadRow();
		preg_match('/\d+(?:\.\d+)+/', $versionRow[0], $versionArray);

		$this->assertGreaterThanOrEqual($versionArray[0], self::$driver->getVersion(), __LINE__);
	}

	/**
	 * Tests the insertId method.
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
	 * Test insertObject function
	 *
	 * @return   void
	 *
	 * @since    1.0
	 */
	public function testInsertObject()
	{
		self::$driver->setQuery('ALTER SEQUENCE dbtest_id_seq RESTART WITH 1')->execute();

		self::$driver->setQuery('TRUNCATE TABLE "dbtest"')->execute();

		$tst = new \stdClass;
		$tst->title = 'PostgreSQL test insertObject';
		$tst->start_date = '2012-04-07 15:00:00';
		$tst->description = 'Test insertObject';

		// Insert object without retrieving key
		$ret = self::$driver->insertObject('#__dbtest', $tst);

		$checkQuery = self::$driver->getQuery(true);
		$checkQuery->select('COUNT(*)')
			->from('#__dbtest')
			->where('start_date = \'2012-04-07 15:00:00\'', 'AND')
			->where('description = \'Test insertObject\'')
			->where('title = \'PostgreSQL test insertObject\'');
		self::$driver->setQuery($checkQuery);

		$this->assertThat(self::$driver->loadResult(), $this->equalTo(1), __LINE__);
		$this->assertThat($ret, $this->equalTo(true), __LINE__);

		// Insert object retrieving the key
		$tstK = new \stdClass;
		$tstK->title = 'PostgreSQL test insertObject with key';
		$tstK->start_date = '2012-04-07 15:00:00';
		$tstK->description = 'Test insertObject with key';
		$retK = self::$driver->insertObject('#__dbtest', $tstK, 'id');

		$this->assertThat($tstK->id, $this->equalTo(2), __LINE__);
		$this->assertThat($retK, $this->equalTo(true), __LINE__);
	}

	/**
	 * Test isSupported function.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testIsSupported()
	{
		$this->assertThat(\Joomla\Database\Pgsql\PgsqlDriver::isSupported(), $this->isTrue(), __LINE__);
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
		$query->from('#__dbtest');
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
		$query->from('#__dbtest');
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
		$query->from('#__dbtest');
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
		$query->from('#__dbtest');
		$query->where('description=' . self::$driver->quote('three'));
		self::$driver->setQuery($query);
		$result = self::$driver->loadObject();

		$objCompare = new \stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->end_date = '1970-01-01 00:00:00';
		$objCompare->description = 'three';
		$objCompare->data = null;

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
		$query->from('#__dbtest');
		$query->order('id');
		self::$driver->setQuery($query);
		$result = self::$driver->loadObjectList();

		$expected = array();

		$objCompare = new \stdClass;
		$objCompare->id = 1;
		$objCompare->title = 'Testing';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->end_date = '1970-01-01 00:00:00';
		$objCompare->description = 'one';
		$objCompare->data = null;

		$expected[] = clone $objCompare;

		$objCompare = new \stdClass;
		$objCompare->id = 2;
		$objCompare->title = 'Testing2';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->end_date = '1970-01-01 00:00:00';
		$objCompare->description = 'one';
		$objCompare->data = null;

		$expected[] = clone $objCompare;

		$objCompare = new \stdClass;
		$objCompare->id = 3;
		$objCompare->title = 'Testing3';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->end_date = '1970-01-01 00:00:00';
		$objCompare->description = 'three';
		$objCompare->data = null;

		$expected[] = clone $objCompare;

		$objCompare = new \stdClass;
		$objCompare->id = 4;
		$objCompare->title = 'Testing4';
		$objCompare->start_date = '1980-04-18 00:00:00';
		$objCompare->end_date = '1970-01-01 00:00:00';
		$objCompare->description = 'four';
		$objCompare->data = null;

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
		$query->from('#__dbtest');
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
		$query->from('#__dbtest');
		$query->where('description=' . self::$driver->quote('three'));
		self::$driver->setQuery($query);
		$result = self::$driver->loadRow();

		$expected = array(3, 'Testing3', '1980-04-18 00:00:00', '1970-01-01 00:00:00', 'three', null);

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
		$query->from('#__dbtest');
		$query->where('description=' . self::$driver->quote('one'));
		self::$driver->setQuery($query);
		$result = self::$driver->loadRowList();

		$expected = array(
			array(1, 'Testing', '1980-04-18 00:00:00', '1970-01-01 00:00:00', 'one', null),
			array(2, 'Testing2', '1980-04-18 00:00:00', '1970-01-01 00:00:00', 'one', null)
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
	 * Test the query method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testQuery()
	{
		/* REPLACE is not present in PostgreSQL */
		$query = self::$driver->getQuery(true);
		$query->delete();
		$query->from('#__dbtest')->where('id=5');
		self::$driver->setQuery($query)->execute();

		$query = self::$driver->getQuery(true);
		$query->insert('#__dbtest')
			->columns('id,title,start_date, description')
			->values("5, 'testTitle','1970-01-01','testDescription'")
			->returning('id');

		self::$driver->setQuery($query);
		$arr = self::$driver->loadResult();

		$this->assertThat($arr, $this->equalTo(5), __LINE__);
	}

	/**
	 * Test quoteName function, with and without dot notation.
	 *
	 * @param   string  $quoteMe   String to be quoted
	 * @param   string  $asPart    String used for AS query part
	 * @param   string  $expected  Expected string
	 *
	 * @return  void
	 *
	 * @since        1.0
	 * @dataProvider dataTestQuoteName
	 */
	public function testQuoteName($quoteMe, $asPart, $expected)
	{
		$this->assertThat(self::$driver->quoteName($quoteMe, $asPart), $this->equalTo($expected), __LINE__);
	}

	/**
	 * Tests the select method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSelect()
	{
		/* it's not possible to select a database, already done during connection, return true */
		$this->assertThat(self::$driver->select('database'), $this->isTrue(), __LINE__);
	}

	/**
	 * Tests the sqlValue method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSqlValue()
	{
		// Array of columns' description as that returned by getTableColumns
		$tablCol = array(
			'id' => 'integer',
			'charVar' => 'character varying',
			'timeStamp' => 'timestamp without time zone',
			'nullDate' => 'timestamp without time zone',
			'txt' => 'text',
			'boolTrue' => 'boolean',
			'boolFalse' => 'boolean',
			'num' => 'numeric,',
			'nullInt' => 'integer'
		);

		$values = array();

		// Object containing fields of integer, character varying, timestamp and text type
		$tst = new \stdClass;
		$tst->id = '5';
		$tst->charVar = 'PostgreSQL test insertObject';
		$tst->timeStamp = '2012-04-07 15:00:00';
		$tst->nullDate = null;
		$tst->txt = 'Test insertObject';
		$tst->boolTrue = true;
		$tst->boolFalse = false;
		$tst->num = '43.2';
		$tst->nullInt = '';

		foreach (get_object_vars($tst) as $key => $val)
		{
			$values[] = self::$driver->sqlValue($tablCol, $key, $val);
		}

		$this->assertThat(
			implode(',', $values),
			$this->equalTo(
				"5,'PostgreSQL test insertObject','2012-04-07 15:00:00','1970-01-01 00:00:00','Test insertObject',TRUE,FALSE,43.2,NULL"
			),
			__LINE__
		);
	}

	/**
	 * Test setUtf function
	 *
	 * @return   void
	 */
	public function testSetUtf()
	{
		$this->assertThat(self::$driver->setUtf(), $this->equalTo(0), __LINE__);
	}

	/**
	 * Test updateObject function.
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
			->columns('id,title,start_date,description')
			->values("6, 'testTitle','1970-01-01','testDescription'");

		self::$driver->setQuery($queryIns)->execute();

		self::$driver->transactionCommit();

		/* check if value is present */
		$queryCheck = self::$driver->getQuery(true);
		$queryCheck->select('*')
			->from('#__dbtest')
			->where('id=6');
		self::$driver->setQuery($queryCheck);
		$result = self::$driver->loadRow();

		$expected = array(6, 'testTitle', '1970-01-01 00:00:00', '1970-01-01 00:00:00', 'testDescription', null);

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
		if (!\is_null($toSavepoint))
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
		if (!\is_null($toSavepoint))
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

		$this->assertThat(\count($result), $this->equalTo($tupleCount), __LINE__);
	}

	/**
	 * Tests the transactionStart method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testTransactionStart()
	{
		self::$driver->transactionStart();
		$queryIns = self::$driver->getQuery(true);
		$queryIns->insert('#__dbtest')
			->columns('id,title,start_date,description')
			->values("6, 'testTitle','1970-01-01','testDescription'");

		self::$driver->setQuery($queryIns)->execute();

		/* check if is present an exclusive lock, it means a transaction is running */
		$queryCheck = self::$driver->getQuery(true);
		$queryCheck->select('*')
			->from('pg_catalog.pg_locks')
			->where('transactionid NOTNULL');
		self::$driver->setQuery($queryCheck);
		$result = self::$driver->loadAssocList();

		$this->assertThat(\count($result), $this->equalTo(1), __LINE__);
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

		/* check name change */
		$tableList = self::$driver->getTableList();
		$this->assertThat(\in_array($newTableName, $tableList), $this->isTrue(), __LINE__);

		/* check index change */
		self::$driver->setQuery(
			'SELECT relname
							FROM pg_class
							WHERE oid IN (
								SELECT indexrelid
								FROM pg_index, pg_class
								WHERE pg_class.relname=\'' . $newTableName . '\' AND pg_class.oid=pg_index.indrelid );');

		$oldIndexes = self::$driver->loadColumn();
		$this->assertThat($oldIndexes[0], $this->equalTo('bak_dbtest_pkey'), __LINE__);

		/* check sequence change */
		self::$driver->setQuery(
			'SELECT relname
							FROM pg_class
							WHERE relkind = \'S\'
							AND relnamespace IN (
								SELECT oid
								FROM pg_namespace
								WHERE nspname NOT LIKE \'pg_%\'
								AND nspname != \'information_schema\'
							)
							AND relname LIKE \'%' . $newTableName . '%\' ;');

		$oldSequences = self::$driver->loadColumn();
		$this->assertThat($oldSequences[0], $this->equalTo('bak_dbtest_id_seq'), __LINE__);

		/* restore initial state */
		self::$driver->renameTable($newTableName, 'dbtest');
	}

	/**
	 * Tests the JDatabasePostgresql replacePrefix method.
	 *
	 * @param   string  $stringToReplace  The string in which replace the prefix.
	 * @param   string  $prefix           The prefix.
	 * @param   string  $expected         The string expected.
	 *
	 * @return  void
	 *
	 * @since         1.0
	 * @dataProvider  dataTestReplacePrefix
	 */
	public function testReplacePrefix($stringToReplace, $prefix, $expected)
	{
		$result = self::$driver->replacePrefix($stringToReplace, $prefix);

		$this->assertThat($result, $this->equalTo($expected), __LINE__);
	}

	/**
	 * Tests the getCreateDbQuery method.
	 *
	 * @param   \stdClass  $options  stdClass coming from "initialise" function to pass user
	 * 									and database name to database driver.
	 * @param   boolean    $utf      True if the database supports the UTF-8 character set.
	 *
	 * @return  void
	 *
	 * @since         1.0
	 * @dataProvider  dataGetCreateDbQuery
	 */
	public function testGetCreateDbQuery($options, $utf)
	{
		$expected = 'CREATE DATABASE ' . self::$driver->quoteName($options->db_name) . ' OWNER ' . self::$driver->quoteName($options->db_user);

		if ($utf)
		{
			$expected .= ' ENCODING ' . self::$driver->quote('UTF-8');
		}

		$result = self::$driver->getCreateDbQuery($options, $utf);

		$this->assertThat($result, $this->equalTo($expected), __LINE__);
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
