<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\ParameterType;
use Joomla\Database\Sqlsrv\SqlsrvQuery;
use Joomla\Database\Tests\Mock\Driver;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Database\Sqlsrv\SqlsrvQuery.
 *
 * @since  1.1
 */
class SqlsrvQueryTest extends TestCase
{
	/**
	 * @var    \Joomla\Database\DatabaseDriver  A mock of the DatabaseDriver object for testing purposes.
	 * @since  1.1
	 */
	protected $dbo;

	/**
	 * Data for the testNullDate test.
	 *
	 * @return  array
	 *
	 * @since   1.1
	 */
	public function dataTestNullDate()
	{
		return array(
			// Quoted, expected
			array(true, "'_0000-00-00 00:00:00_'"),
			array(false, '0000-00-00 00:00:00'),
		);
	}

	/**
	 * Tests the isNullDatetime method.
	 *
	 * @return  void
	 *
	 * @covers     \Joomla\Database\Sqlsrv\SqlsrvQuery::isNullDatetime
	 * @since      __DEPLOY_VERSION__
	 */
	public function testIsNullDatetime()
	{
		$query = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$query->isNullDatetime('publish_up'),
			$this->equalTo(
				'(publish_up IN (\'_1900-01-01 00:00:00_\')' .
				' OR publish_up IS NULL)'
			),
			'Test isNullDatetime failed.'
		);
	}

	/**
	 * Data for the testNullDate test.
	 *
	 * @return  array
	 *
	 * @since   1.1
	 */
	public function dataTestQuote()
	{
		return array(
			// Text, escaped, expected
			array('text', false, '\'text\''),
		);
	}

	/**
	 * Data for the testJoin test.
	 *
	 * @return  array
	 *
	 * @since   1.1
	 */
	public function dataTestJoin()
	{
		return array(
			// $type, $conditions
			array('', 		'b ON b.id = a.id'),
			array('INNER',	'b ON b.id = a.id'),
			array('OUTER',	'b ON b.id = a.id'),
			array('LEFT',	'b ON b.id = a.id'),
			array('RIGHT',	'b ON b.id = a.id'),
		);
	}

	/**
	 * A mock callback for the database escape method.
	 *
	 * We use this method to ensure that DatabaseQuery's escape method uses the
	 * the database object's escape method.
	 *
	 * @param   string  $text  The input text.
	 *
	 * @return  string
	 *
	 * @since   1.1
	 */
	public function mockEscape($text)
	{
		return "{$text}";
	}

	/**
	 * A mock callback for the database quoteName method.
	 *
	 * We use this method to ensure that DatabaseQuery's quoteName method uses the
	 * the database object's quoteName method.
	 *
	 * @param   string  $text  The input text.
	 *
	 * @return  string
	 *
	 * @since   1.1
	 */
	public function mockQuoteName($text)
	{
		return '"' . $text . '"';
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->dbo = Driver::create($this);

		// Mock the escape method to ensure the API is calling the DBO's escape method.
		TestHelper::assignMockCallbacks(
			$this->dbo,
			$this,
			array('escape' => array($this, 'mockEscape'))
		);
	}

	/**
	 * Test for the SqlsrvQuery::__string method for a 'select' case.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function test__toStringSelect()
	{
		$this->markTestSkipped('Fails with new GROUP method');

		$q = new SqlsrvQuery($this->dbo);

		$q->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.id = 1')
			->group('a.id')
			->having('COUNT(a.id) > 3')
			->order('a.id');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'SELECT a.id' .
				PHP_EOL . 'FROM a' .
				PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
				PHP_EOL . 'WHERE b.id = 1' .
				PHP_EOL . 'GROUP BY a.id' .
				PHP_EOL . 'HAVING COUNT(a.id) > 3' .
				PHP_EOL . 'ORDER BY a.id'
			),
			'Tests for correct rendering.'
		);
	}

	/**
	 * Test for the \Joomla\Database\Sqlsrv\SqlsrvQuery::__string method for a 'select' case.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function test__toStringSelectWithUnion()
	{
		$thisQuery = new SqlsrvQuery($this->dbo);

		$thisQuery->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.id = 1');

		$union = new SqlsrvQuery($this->dbo);

		$union->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.name = ' . $union->quote('name'));

		$thisQuery->union($union);

		$this->assertThat(
			(string) $thisQuery,
			$this->equalTo(
				PHP_EOL . 'SELECT a.id' .
					PHP_EOL . 'FROM a' .
					PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
					PHP_EOL . 'WHERE b.id = 1' .
					PHP_EOL . 'UNION SELECT * FROM (' .
					PHP_EOL . 'SELECT a.id' .
					PHP_EOL . 'FROM a' .
					PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
					PHP_EOL . 'WHERE b.name = \'_name_\'' .
					PHP_EOL . '/*ORDER BY (SELECT 0)*/) AS merge_1' .
					PHP_EOL . '/*ORDER BY (SELECT 0)*/'
			),
			'Tests for correct rendering unions.'
		);
	}

	/**
	 * Test for the \Joomla\Database\Sqlsrv\SqlsrvQuery::__string method for a 'select' case.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function test__toStringSelectWithUnionAndOrderBy()
	{
		$thisQuery = new SqlsrvQuery($this->dbo);

		$thisQuery->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.id = 1')
			->order('b.name');

		$union = new SqlsrvQuery($this->dbo);

		$union->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.name = ' . $union->quote('name'));

		$thisQuery->union($union);

		$this->assertThat(
			(string) $thisQuery,
			$this->equalTo(
				PHP_EOL . 'SELECT a.id' .
					PHP_EOL . 'FROM a' .
					PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
					PHP_EOL . 'WHERE b.id = 1' .
					PHP_EOL . 'UNION SELECT * FROM (' .
					PHP_EOL . 'SELECT a.id' .
					PHP_EOL . 'FROM a' .
					PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
					PHP_EOL . 'WHERE b.name = \'_name_\'' .
					PHP_EOL . '/*ORDER BY (SELECT 0)*/) AS merge_1' .
					PHP_EOL . 'ORDER BY b.name'
			),
			'Tests for correct rendering unions with order by.'
		);
	}

	/**
	 * Test for the \Joomla\Database\Sqlsrv\SqlsrvQuery::__string method for a 'querySet' case.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function test__toStringQuerySetWithIndividualOrderBy()
	{
		$query = new SqlsrvQuery($this->dbo);

		$query->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.id = 1')
			->order('a.id');

		$union = new SqlsrvQuery($this->dbo);

		$union->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.name = ' . $union->quote('name'))
			->order('a.id');

		$thisQuery = new SqlsrvQuery($this->dbo);

		$thisQuery->querySet($query)
			->union($union)
			->order('id');

		$this->assertThat(
			(string) $thisQuery,
			$this->equalTo(
				PHP_EOL . 'SELECT * FROM (' .
					PHP_EOL . 'SELECT a.id' .
					PHP_EOL . 'FROM a' .
					PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
					PHP_EOL . 'WHERE b.id = 1' .
					PHP_EOL . 'ORDER BY a.id) AS merge_0' .
				PHP_EOL . 'UNION SELECT * FROM (' .
					PHP_EOL . 'SELECT a.id' .
					PHP_EOL . 'FROM a' .
					PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
					PHP_EOL . 'WHERE b.name = \'_name_\'' .
					PHP_EOL . 'ORDER BY a.id) AS merge_1' .
				PHP_EOL . 'ORDER BY id'
			),
			'Tests for correct rendering querySet with global order by.'
		);
	}

	/**
	 * Test for the \Joomla\Database\Sqlsrv\SqlsrvQuery::__string method for a 'toQuerySet' case.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function test__toStringQuerySetWithIndividualOrderBy2()
	{
		$thisQuery = new SqlsrvQuery($this->dbo);

		$thisQuery->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.id = 1')
			->order('a.id');

		$union = new SqlsrvQuery($this->dbo);

		$union->select('a.id')
			->from('a')
			->innerJoin('b ON b.id = a.id')
			->where('b.name = ' . $union->quote('name'))
			->order('a.id');

		$query = $thisQuery->toQuerySet()
			->union($union)
			->order('id');

		$this->assertThat(
			(string) $query,
			$this->equalTo(
				PHP_EOL . 'SELECT * FROM (' .
					PHP_EOL . 'SELECT a.id' .
					PHP_EOL . 'FROM a' .
					PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
					PHP_EOL . 'WHERE b.id = 1' .
					PHP_EOL . 'ORDER BY a.id) AS merge_0' .
				PHP_EOL . 'UNION SELECT * FROM (' .
					PHP_EOL . 'SELECT a.id' .
					PHP_EOL . 'FROM a' .
					PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
					PHP_EOL . 'WHERE b.name = \'_name_\'' .
					PHP_EOL . 'ORDER BY a.id) AS merge_1' .
				PHP_EOL . 'ORDER BY id'
			),
			'Tests for correct rendering querySet with global order by.'
		);
	}

	/**
	 * Test for the SqlsrvQuery::__string method for a 'update' case.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function test__toStringUpdate()
	{
		$q = new SqlsrvQuery($this->dbo);

		$q->update('#__foo AS a')
			->join('INNER', 'b ON b.id = a.id')
			->set('a.id = 2')
			->where('b.id = 1');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'UPDATE a' .
				PHP_EOL . 'SET a.id = 2' .
				PHP_EOL . 'FROM #__foo AS a' .
				PHP_EOL . 'INNER JOIN b ON b.id = a.id' .
				PHP_EOL . 'WHERE b.id = 1'
			),
			'Tests for correct rendering.'
		);
	}

	/**
	 * Test for year extraction from date.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function test__toStringYear()
	{
		$q = new SqlsrvQuery($this->dbo);

		$q->select($q->year($q->quoteName('col')))->from('table');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'SELECT YEAR("col") AS "columnAlias0"' .
				PHP_EOL . 'FROM table' .
				PHP_EOL . '/*ORDER BY (SELECT 0)*/'
			)
		);
	}

	/**
	 * Test for month extraction from date.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function test__toStringMonth()
	{
		$q = new SqlsrvQuery($this->dbo);

		$q->select($q->month($q->quoteName('col')))->from('table');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'SELECT MONTH("col") AS "columnAlias0"' .
				PHP_EOL . 'FROM table' .
				PHP_EOL . '/*ORDER BY (SELECT 0)*/'
			)
		);
	}

	/**
	 * Test for day extraction from date.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function test__toStringDay()
	{
		$q = new SqlsrvQuery($this->dbo);

		$q->select($q->day($q->quoteName('col')))->from('table');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'SELECT DAY("col") AS "columnAlias0"' .
				PHP_EOL . 'FROM table' .
				PHP_EOL . '/*ORDER BY (SELECT 0)*/'
			)
		);
	}

	/**
	 * Test for hour extraction from date.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function test__toStringHour()
	{
		$q = new SqlsrvQuery($this->dbo);

		$q->select($q->hour($q->quoteName('col')))->from('table');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'SELECT HOUR("col") AS "columnAlias0"' .
				PHP_EOL . 'FROM table' .
				PHP_EOL . '/*ORDER BY (SELECT 0)*/'
			)
		);
	}

	/**
	 * Test for minute extraction from date.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function test__toStringMinute()
	{
		$q = new SqlsrvQuery($this->dbo);

		$q->select($q->minute($q->quoteName('col')))->from('table');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'SELECT MINUTE("col") AS "columnAlias0"' .
				PHP_EOL . 'FROM table' .
				PHP_EOL . '/*ORDER BY (SELECT 0)*/'
			)
		);
	}

	/**
	 * Test for seconds extraction from date.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function test__toStringSecond()
	{
		$q = new SqlsrvQuery($this->dbo);

		$q->select($q->second($q->quoteName('col')))->from('table');

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'SELECT SECOND("col") AS "columnAlias0"' .
				PHP_EOL . 'FROM table' .
				PHP_EOL . '/*ORDER BY (SELECT 0)*/'
			)
		);
	}

	/**
	 * Test for FROM clause with subquery.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function test__toStringFrom_subquery()
	{
		$subq = new SqlsrvQuery($this->dbo);
		$subq->select('col AS col2')->from('table')->where('a=1')->setLimit(1);

		$q = new SqlsrvQuery($this->dbo);
		$q->select('col2')->from($subq->alias('alias'));

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'SELECT col2' .
				PHP_EOL . 'FROM (' .
				PHP_EOL . 'SELECT TOP 1 col AS col2' .
				PHP_EOL . 'FROM table' .
				PHP_EOL . 'WHERE a=1' .
				PHP_EOL . '/*ORDER BY (SELECT 0)*/) AS alias' .
				PHP_EOL . '/*ORDER BY (SELECT 0)*/'
			)
		);
	}

	/**
	 * Test for INSERT INTO clause with subquery.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function test__toStringInsert_subquery()
	{
		$q = new SqlsrvQuery($this->dbo);
		$subq = new SqlsrvQuery($this->dbo);
		$subq->select('col2')->where('a=1');

		$q->insert('table')->columns('col')->values($subq);

		$this->assertThat(
			(string) $q,
			$this->equalTo(
				PHP_EOL . 'INSERT INTO table' .
				PHP_EOL . '(col)VALUES ' .
				PHP_EOL . '(' .
				PHP_EOL . 'SELECT col2' .
				PHP_EOL . 'WHERE a=1' .
				PHP_EOL . '/*ORDER BY (SELECT 0)*/)'
			)
		);

		$q->clear();
		$q->insert('table')->columns('col')->values('3');
		$this->assertThat(
			(string) $q,
			$this->equalTo(PHP_EOL . 'INSERT INTO table' . PHP_EOL . '(col)VALUES ' . PHP_EOL . '(3)')
		);
	}

	/**
	 * Test for the castAsChar method.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testCastAsChar()
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->castAsChar('123'),
			$this->equalTo('CAST(123 as NVARCHAR(10))'),
			'The default castAsChar behaviour is quote the input.'
		);
	}

	/**
	 * Test for the charLength method.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testCharLength()
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->charLength('a.title'),
			$this->equalTo('DATALENGTH(a.title)')
		);
	}

	/**
	 * Test chaining.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testChaining()
	{
		$q = $this->dbo->getQuery(true)->select('foo');

		$this->assertThat(
			$q,
			$this->isInstanceOf('\Joomla\Database\DatabaseQuery')
		);
	}

	/**
	 * Test for the clear method (clearing all types and clauses).
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testClear_all()
	{
		$properties = array(
			'select',
			'delete',
			'update',
			'insert',
			'from',
			'join',
			'set',
			'where',
			'group',
			'having',
			'order',
			'columns',
			'values',
		);

		$q = new SqlsrvQuery($this->dbo);

		// First pass - set the values.
		foreach ($properties as $property)
		{
			TestHelper::setValue($q, $property, $property);
		}

		// Clear the whole query.
		$q->clear();

		// Check that all properties have been cleared
		foreach ($properties as $property)
		{
			$this->assertThat(
				$q->$property,
				$this->equalTo(null)
			);
		}

		// And check that the type has been cleared.
		$this->assertThat(
			$q->type,
			$this->equalTo(null)
		);
	}

	/**
	 * Test for the clear method (clearing each clause).
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testClear_clause()
	{
		$clauses = array(
			'from',
			'join',
			'set',
			'where',
			'group',
			'having',
			'order',
			'columns',
			'values',
		);

		// Test each clause.
		foreach ($clauses as $clause)
		{
			$q = new SqlsrvQuery($this->dbo);

			// Set the clauses
			foreach ($clauses as $clause2)
			{
				TestHelper::setValue($q, $clause2, $clause2);
			}

			// Clear the clause.
			$q->clear($clause);

			// Check that clause was cleared.
			$this->assertThat(
				$q->$clause,
				$this->equalTo(null)
			);

			// Check the state of the other clauses.
			foreach ($clauses as $clause2)
			{
				if ($clause !== $clause2)
				{
					$this->assertThat(
						$q->$clause2,
						$this->equalTo($clause2),
						"Clearing '$clause' resulted in '$clause2' having a value of " . $q->$clause2 . '.'
					);
				}
			}
		}
	}

	/**
	 * Test for the clear method (clearing each query type).
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testClear_type()
	{
		$types = array(
			'select',
			'delete',
			'update',
			'insert',
		);

		$clauses = array(
			'from',
			'join',
			'set',
			'where',
			'group',
			'having',
			'order',
			'columns',
			'values',
		);

		$q = new SqlsrvQuery($this->dbo);

		// Set the clauses.
		foreach ($clauses as $clause)
		{
			TestHelper::setValue($q, $clause, $clause);
		}

		// Check that all properties have been cleared
		foreach ($types as $type)
		{
			// Set the type.
			TestHelper::setValue($q, $type, $type);

			// Clear the type.
			$q->clear($type);

			// Check the type has been cleared.
			$this->assertThat(
				$q->$type,
				$this->equalTo(null)
			);

			// Now check the claues have not been affected.
			foreach ($clauses as $clause)
			{
				$this->assertThat(
					$q->$clause,
					$this->equalTo($clause)
				);
			}
		}
	}

	/**
	 * Test for "concatenate" words.
	 *
	 * @return  void
	 */
	public function testConcatenate()
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->concatenate(array('foo', 'bar')),
			$this->equalTo('(foo+bar)'),
			'Tests without separator.'
		);

		$this->assertThat(
			$q->concatenate(array('foo', 'bar'), ' and '),
			$this->equalTo("(foo+'_ and _'+bar)"),
			'Tests without separator.'
		);
	}

	/**
	 * Test for FROM clause.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testFrom()
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->from('#__foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->from),
			$this->equalTo('FROM #__foo'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->from('#__bar');

		$this->assertThat(
			trim($q->from),
			$this->equalTo('FROM #__foo,#__bar'),
			'Tests rendered value after second use.'
		);
	}

	/**
	 * Test for GROUP clause.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testGroup()
	{
		$this->markTestSkipped('Fails with new GROUP method');

		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->group('foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->group),
			$this->equalTo('GROUP BY foo'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->group('bar');

		$this->assertThat(
			trim($q->group),
			$this->equalTo('GROUP BY foo,bar'),
			'Tests rendered value after second use.'
		);
	}

	/**
	 * Test for HAVING clause using a simple condition and with glue for second one.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testHaving()
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->having('COUNT(foo) > 1'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->having),
			$this->equalTo('HAVING COUNT(foo) > 1'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->having('COUNT(bar) > 2');

		$this->assertThat(
			trim($q->having),
			$this->equalTo('HAVING COUNT(foo) > 1 AND COUNT(bar) > 2'),
			'Tests rendered value after second use.'
		);

		// Reset the field to test the glue.
		TestHelper::setValue($q, 'having', null);
		$q->having('COUNT(foo) > 1', 'OR');
		$q->having('COUNT(bar) > 2');

		$this->assertThat(
			trim($q->having),
			$this->equalTo('HAVING COUNT(foo) > 1 OR COUNT(bar) > 2'),
			'Tests rendered value with OR glue.'
		);
	}

	/**
	 * Test for INNER JOIN clause.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testInnerJoin()
	{
		$q = new SqlsrvQuery($this->dbo);
		$q2 = new SqlsrvQuery($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q->innerJoin($condition),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$q2->join('INNER', $condition);

		$this->assertThat(
			$q->join,
			$this->equalTo($q2->join),
			'Tests that innerJoin is an alias for join.'
		);
	}

	/**
	 * Test for JOIN clause using dataprovider to test all types of join.
	 *
	 * @param   string  $type        Type of JOIN, could be INNER, OUTER, LEFT, RIGHT
	 * @param   string  $conditions  Join condition
	 *
	 * @return  void
	 *
	 * @since   1.1
	 * @dataProvider  dataTestJoin
	 */
	public function testJoin($type, $conditions)
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->join('INNER', 'foo ON foo.id = bar.id'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->join[0]),
			$this->equalTo('INNER JOIN foo ON foo.id = bar.id'),
			'Tests that first join renders correctly.'
		);

		$q->join('OUTER', 'goo ON goo.id = car.id');

		$this->assertThat(
			trim($q->join[1]),
			$this->equalTo('OUTER JOIN goo ON goo.id = car.id'),
			'Tests that second join renders correctly.'
		);
	}

	/**
	 * Test for LEFT JOIN clause.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testLeftJoin()
	{
		$q = new SqlsrvQuery($this->dbo);
		$q2 = new SqlsrvQuery($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q->leftJoin($condition),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$q2->join('LEFT', $condition);

		$this->assertThat(
			$q->join,
			$this->equalTo($q2->join),
			'Tests that innerJoin is an alias for join.'
		);
	}

	/**
	 * Tests the quoteName method.
	 *
	 * @param   boolean  $quoted    The value of the quoted argument.
	 * @param   string   $expected  The expected result.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 * @dataProvider  dataTestNullDate
	 */
	public function testNullDate($quoted, $expected)
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->nullDate($quoted),
			$this->equalTo($expected),
			'The nullDate method should be a proxy for the JDatabase::getNullDate method.'
		);
	}

	/**
	 * Test for ORDER clause.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testOrder()
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->order('column'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->order),
			$this->equalTo('ORDER BY column'),
			'Tests rendered value.'
		);

		$q->order('col2');
		$this->assertThat(
			trim($q->order),
			$this->equalTo('ORDER BY column,col2'),
			'Tests rendered value.'
		);
	}

	/**
	 * Test for OUTER JOIN clause.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testOuterJoin()
	{
		$q = new SqlsrvQuery($this->dbo);
		$q2 = new SqlsrvQuery($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q->outerJoin($condition),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$q2->join('OUTER', $condition);

		$this->assertThat(
			$q->join,
			$this->equalTo($q2->join),
			'Tests that innerJoin is an alias for join.'
		);
	}

	/**
	 * Tests the quoteName method.
	 *
	 * @param   boolean  $text      The value to be quoted.
	 * @param   boolean  $escape    True to escape the string, false to leave it unchanged.
	 * @param   string   $expected  The expected result.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 * @dataProvider  dataTestQuote
	 */
	public function testQuote($text, $escape, $expected)
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->quote('test'),
			$this->equalTo("'_test_'"),
			'The quote method should be a proxy for the DatabaseDriver::quote method.'
		);
	}

	/**
	 * Tests the quoteName method.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testQuoteName()
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->quoteName('test'),
			$this->equalTo('"test"'),
			'The quoteName method should be a proxy for the DatabaseDriver::quoteName method.'
		);
	}

	/**
	 * Test for RIGHT JOIN clause.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testRightJoin()
	{
		$q = new SqlsrvQuery($this->dbo);
		$q2 = new SqlsrvQuery($this->dbo);
		$condition = 'foo ON foo.id = bar.id';

		$this->assertThat(
			$q->rightJoin($condition),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$q2->join('RIGHT', $condition);

		$this->assertThat(
			$q->join,
			$this->equalTo($q2->join),
			'Tests that innerJoin is an alias for join.'
		);
	}

	/**
	 * Test for SELECT clause.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testSelect()
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->select('foo'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			$q->type,
			$this->equalTo('select'),
			'Tests the type property is set correctly.'
		);

		$this->assertThat(
			trim($q->select),
			$this->equalTo('SELECT foo'),
			'Tests the select element is set correctly.'
		);

		$q->select('bar');

		$this->assertThat(
			trim($q->select),
			$this->equalTo('SELECT foo,bar'),
			'Tests the second use appends correctly.'
		);

		$q->select(
			array(
				'goo', 'car'
			)
		);

		$this->assertThat(
			trim($q->select),
			$this->equalTo('SELECT foo,bar,goo,car'),
			'Tests the second use appends correctly.'
		);
	}

	/**
	 * Test for WHERE clause using a simple condition and with glue for second one.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testWhere()
	{
		$q = new SqlsrvQuery($this->dbo);
		$this->assertThat(
			$q->where('foo = 1'),
			$this->identicalTo($q),
			'Tests chaining.'
		);

		$this->assertThat(
			trim($q->where),
			$this->equalTo('WHERE foo = 1'),
			'Tests rendered value.'
		);

		// Add another column.
		$q->where(
			array(
				'bar = 2',
				'goo = 3',
			)
		);

		$this->assertThat(
			trim($q->where),
			$this->equalTo('WHERE foo = 1 AND bar = 2 AND goo = 3'),
			'Tests rendered value after second use and array input.'
		);

		// Clear the where
		TestHelper::setValue($q, 'where', null);
		$q->where(
			array(
				'bar = 2',
				'goo = 3',
			),
			'OR'
		);

		$this->assertThat(
			trim($q->where),
			$this->equalTo('WHERE bar = 2 OR goo = 3'),
			'Tests rendered value with glue.'
		);
	}

	/**
	 * Tests WHERE IN clause.
	 *
	 * @return  void
	 */
	public function testWhereIn()
	{
		$q = new SqlsrvQuery($this->dbo);
		$q->whereIn('id', [1, 2, 3]);

		$this->assertThat(
			trim($q->where),
			$this->equalTo('WHERE id IN (:preparedArray1,:preparedArray2,:preparedArray3)'),
			'Tests rendered value.'
		);
	}

	/**
	 * Tests bindArray function.
	 *
	 * @return  void
	 */
	public function testBindArray()
	{
		$q = new SqlsrvQuery($this->dbo);
		$result = $q->bindArray([1, 2, 3], ParameterType::INTEGER);

		$this->assertThat(
			$result,
			$this->equalTo([':preparedArray1', ':preparedArray2', ':preparedArray3']),
			'Tests rendered value.'
		);

		$this->assertThat(
			count($q->getBounded()),
			$this->equalTo(3),
			'Tests rendered value.'
		);

		$this->assertThat(
			$q->getBounded(':preparedArray1')->value,
			$this->equalTo(1),
			'Tests rendered value.'
		);

		$this->assertThat(
			$q->getBounded(':preparedArray2')->value,
			$this->equalTo(2),
			'Tests rendered value.'
		);

		$this->assertThat(
			$q->getBounded(':preparedArray3')->value,
			$this->equalTo(3),
			'Tests rendered value.'
		);
	}

	/**
	 * Tests the SqlsrvQuery::escape method.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function testEscape()
	{
		$q = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$q->escape('foo'),
			$this->equalTo('foo')
		);
	}

	/**
	 * Test for the SqlsrvQuery::processLimit method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testProcessLimit()
	{
		$q = new SqlsrvQuery($this->dbo);

		$q->select('id, COUNT(*) AS count')
			->from('a')
			->where('id = 1');

		$this->assertEquals(
			PHP_EOL . 'SELECT id,COUNT(*) AS count' .
			PHP_EOL . 'FROM a' .
			PHP_EOL . 'WHERE id = 1' .
			PHP_EOL . '/*ORDER BY (SELECT 0)*/',
			$q->processLimit((string) $q, 0)
		);

		$this->assertEquals(
			PHP_EOL . 'SELECT TOP 30 id,COUNT(*) AS count' .
			PHP_EOL . 'FROM a' .
			PHP_EOL . 'WHERE id = 1' .
			PHP_EOL . '/*ORDER BY (SELECT 0)*/',
			$q->processLimit((string) $q, 30)
		);

		$this->assertEquals(
			PHP_EOL . 'SELECT id,COUNT(*) AS count' .
			PHP_EOL . 'FROM a' .
			PHP_EOL . 'WHERE id = 1' .
			PHP_EOL . 'ORDER BY (SELECT 0)' .
			PHP_EOL . 'OFFSET 3 ROWS' .
			PHP_EOL . 'FETCH NEXT 1 ROWS ONLY',
			$q->processLimit((string) $q, 1, 3)
		);

		$this->assertEquals(
			PHP_EOL . 'SELECT id,COUNT(*) AS count' .
			PHP_EOL . 'FROM a' .
			PHP_EOL . 'WHERE id = 1' .
			PHP_EOL . 'ORDER BY (SELECT 0)' .
			PHP_EOL . 'OFFSET 3 ROWS',
			$q->processLimit((string) $q, 0, 3)
		);

		// Test if ORDER BY is correctly recognised in query 1
		$q->clear('where')->where('id IN (select id from b order by 1)');

		$this->assertEquals(
			PHP_EOL . 'SELECT id,COUNT(*) AS count' .
			PHP_EOL . 'FROM a' .
			PHP_EOL . 'WHERE id IN (select id from b order by 1)' .
			PHP_EOL . 'ORDER BY (SELECT 0)' .
			PHP_EOL . 'OFFSET 3 ROWS',
			$q->processLimit((string) $q, 0, 3)
		);

		// Test if ORDER BY is correctly recognised in query 2
		$q->order('id DESC');

		$this->assertEquals(
			PHP_EOL . 'SELECT id,COUNT(*) AS count' .
			PHP_EOL . 'FROM a' .
			PHP_EOL . 'WHERE id IN (select id from b order by 1)' .
			PHP_EOL . 'ORDER BY id DESC' .
			PHP_EOL . 'OFFSET 3 ROWS',
			$q->processLimit((string) $q, 0, 3)
		);

		// Test if ORDER BY is correctly recognised in query 3
		$q->clear('where')->where('id IN (SELECT id FROM b /*ORDER BY (SELECT 0)*/)');

		$this->assertEquals(
			PHP_EOL . 'SELECT id,COUNT(*) AS count' .
			PHP_EOL . 'FROM a' .
			PHP_EOL . 'WHERE id IN (SELECT id FROM b /*ORDER BY (SELECT 0)*/)' .
			PHP_EOL . 'ORDER BY id DESC' .
			PHP_EOL . 'OFFSET 3 ROWS',
			$q->processLimit((string) $q, 0, 3)
		);

		// Test if ORDER BY is correctly recognised in query 4
		$q->clear('order');

		$this->assertEquals(
			PHP_EOL . 'SELECT id,COUNT(*) AS count' .
			PHP_EOL . 'FROM a' .
			PHP_EOL . 'WHERE id IN (SELECT id FROM b /*ORDER BY (SELECT 0)*/)' .
			PHP_EOL . 'ORDER BY (SELECT 0)' .
			PHP_EOL . 'OFFSET 3 ROWS',
			$q->processLimit((string) $q, 0, 3)
		);
	}

	/**
	 * Tests the \Joomla\Database\Sqlsrv\SqlsrvQuery::union method.
	 *
	 * @return  void
	 *
	 * @covers  \Joomla\Database\Sqlsrv\SqlsrvQuery::union
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUnionChain()
	{
		$thisQuery = new SqlsrvQuery($this->dbo);

		$this->assertThat(
			$thisQuery->union($thisQuery),
			$this->identicalTo($thisQuery),
			'Tests chaining.'
		);
	}

	/**
	 * Tests the \Joomla\Database\Sqlsrv\SqlsrvQuery::union method.
	 *
	 * @return  void
	 *
	 * @covers  \Joomla\Database\Sqlsrv\SqlsrvQuery::union
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUnion()
	{
		$thisQuery = new SqlsrvQuery($this->dbo);

		$thisQuery->union('SELECT name FROM foo');

		$string = implode('', $thisQuery->merge);

		$this->assertThat(
			$string,
			$this->equalTo(PHP_EOL . 'UNION SELECT * FROM (SELECT name FROM foo)'),
			'Tests rendered query with union.'
		);
	}

	/**
	 * Tests the \Joomla\Database\Sqlsrv\SqlsrvQuery::unionAll method.
	 *
	 * @return  void
	 *
	 * @covers  \Joomla\Database\Sqlsrv\SqlsrvQuery::unionAll
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUnionAll()
	{
		$thisQuery = new SqlsrvQuery($this->dbo);

		$thisQuery->unionAll('SELECT name FROM foo');

		$string = implode('', $thisQuery->merge);

		$this->assertThat(
			$string,
			$this->equalTo(PHP_EOL . 'UNION ALL SELECT * FROM (SELECT name FROM foo)'),
			'Tests rendered query with union all.'
		);
	}

	/**
	 * Tests the \Joomla\Database\Sqlsrv\SqlsrvQuery::union method.
	 *
	 * @return  void
	 *
	 * @covers  \Joomla\Database\Sqlsrv\SqlsrvQuery::union
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUnionTwo()
	{
		$thisQuery = new SqlsrvQuery($this->dbo);

		$thisQuery->union('SELECT name FROM foo');
		$thisQuery->union('SELECT name FROM bar');

		$string = implode('', $thisQuery->merge);

		$this->assertThat(
			$string,
			$this->equalTo(
				PHP_EOL . 'UNION SELECT * FROM (SELECT name FROM foo)' .
				PHP_EOL . 'UNION SELECT * FROM (SELECT name FROM bar)'
			),
			'Tests rendered query with two unions sequentially.'
		);
	}

	/**
	 * Tests the \Joomla\Database\Sqlsrv\SqlsrvQuery::union method.
	 *
	 * @return  void
	 *
	 * @covers  \Joomla\Database\Sqlsrv\SqlsrvQuery::union
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUnionsOrdering()
	{
		$thisQuery = new SqlsrvQuery($this->dbo);

		$thisQuery->unionAll('SELECT name FROM foo');
		$thisQuery->union('SELECT name FROM bar');

		$string = implode('', $thisQuery->merge);

		$this->assertThat(
			$string,
			$this->equalTo(
				PHP_EOL . 'UNION ALL SELECT * FROM (SELECT name FROM foo)' .
				PHP_EOL . 'UNION SELECT * FROM (SELECT name FROM bar)'
			),
			'Tests rendered query with two different unions sequentially.'
		);
	}

	/**
	 * Tests the \Joomla\Database\Sqlsrv\SqlsrvQuery::union method.
	 *
	 * @return  void
	 *
	 * @covers  \Joomla\Database\Sqlsrv\SqlsrvQuery::union
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUnionsOrdering2()
	{
		$thisQuery = new SqlsrvQuery($this->dbo);

		$thisQuery->union('SELECT name FROM foo');
		$thisQuery->unionAll('SELECT name FROM bar');

		$string = implode('', $thisQuery->merge);

		$this->assertThat(
			$string,
			$this->equalTo(
				PHP_EOL . 'UNION SELECT * FROM (SELECT name FROM foo)' .
				PHP_EOL . 'UNION ALL SELECT * FROM (SELECT name FROM bar)'
			),
			'Tests rendered query with two different unions sequentially.'
		);
	}
}
