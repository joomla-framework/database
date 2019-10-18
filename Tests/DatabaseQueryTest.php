<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\DatabaseQuery
 */
class DatabaseQueryTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  MockObject|DatabaseQuery
	 */
	private $query;

	/**
	 * Mock database driver
	 *
	 * @var  MockObject|DatabaseInterface
	 */
	private $db;

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

		$this->db    = $this->createMock(DatabaseInterface::class);
		$this->query = $this->getMockForAbstractClass(
			DatabaseQuery::class,
			[$this->db]
		);
	}

	/**
	 * @testdox  The call method correctly creates and manages a CALL query element
	 */
	public function testCall()
	{
		$this->assertSame($this->query, $this->query->call('foo'), 'The query builder supports method chaining');
		$this->query->call('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->call->getElements()
		);
	}

	/**
	 * @testdox  A string is cast as a character string for the driver
	 */
	public function testCastAsChar()
	{
		$this->assertSame('foo', $this->query->castAsChar('foo'));
	}

	/**
	 * Data provider for character length test cases
	 *
	 * @return  \Generator
	 */
	public function dataCharLength(): \Generator
	{
		yield 'field without comparison' => ['a.title', null, null, 'CHAR_LENGTH(a.title)'];
		yield 'field with comparison' => ['a.title', '!=', '0', 'CHAR_LENGTH(a.title) != 0'];
	}

	/**
	 * @testdox  A SQL statement for checking the character length of a field is generated
	 *
	 * @param   string       $field      A value.
	 * @param   string|null  $operator   Comparison operator between charLength integer value and $condition
	 * @param   string|null  $condition  Integer value to compare charLength with.
	 * @param   string       $expected   The expected query string.
	 *
	 * @dataProvider  dataCharLength
	 */
	public function testCharLength(string $field, ?string $operator, ?string $condition, string $expected)
	{
		$this->assertSame(
			$expected,
			$this->query->charLength($field, $operator, $condition)
		);
	}

	/**
	 * @testdox  The columns method correctly creates and manages a list of columns
	 */
	public function testColumns()
	{
		$this->assertSame($this->query, $this->query->columns('foo'), 'The query builder supports method chaining');
		$this->query->columns('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->columns->getElements()
		);
	}

	/**
	 * Data provider for concatenate test cases
	 *
	 * @return  \Generator
	 */
	public function dataConcatenate(): \Generator
	{
		yield 'values without separator' => [['foo', 'bar'], null, 'CONCATENATE(foo || bar)'];
		yield 'values with separator' => [['foo', 'bar'], ' and ', "CONCATENATE(foo || ' and ' || bar)"];
	}

	/**
	 * @testdox  A SQL statement for concatenating values is generated
	 *
	 * @param   string[]     $values     An array of values to concatenate.
	 * @param   string|null  $separator  As separator to place between each value.
	 * @param   string       $expected   The expected query string.
	 *
	 * @dataProvider  dataConcatenate
	 */
	public function testConcatenate(array $values, ?string $separator, string $expected)
	{
		$this->db->expects($this->any())
			->method('quote')
			->willReturnCallback(function ($text, $escape = true) {
				return "'" . $text . "'";
			});

		$this->assertSame(
			$expected,
			$this->query->concatenate($values, $separator)
		);
	}

	/**
	 * @testdox  A SQL statement for the current timestamp is generated
	 */
	public function testCurrentTimestamp()
	{
		$this->assertSame(
			'CURRENT_TIMESTAMP()',
			$this->query->currentTimestamp()
		);
	}

	/**
	 * Data provider for dateAdd test cases
	 *
	 * @return  \Generator
	 */
	public function dataDateAdd(): \Generator
	{
		yield 'date with positive interval' => ["'2019-10-13'", '1', 'DAY', "DATE_ADD('2019-10-13', INTERVAL 1 DAY)"];
		yield 'date with negative interval' => ["'2019-10-13'", '-1', 'DAY', "DATE_ADD('2019-10-13', INTERVAL -1 DAY)"];
	}

	/**
	 * @testdox  A SQL statement for adding date values is generated
	 *
	 * @param   string  $date      The db quoted string representation of the date to add to. May be date or datetime
	 * @param   string  $interval  The string representation of the appropriate number of units
	 * @param   string  $datePart  The part of the date to perform the addition on
	 * @param   string  $expected  The expected query string.
	 *
	 * @dataProvider  dataDateAdd
	 */
	public function testDateAdd(string $date, string $interval, string $datePart, string $expected)
	{
		$this->assertSame(
			$expected,
			$this->query->dateAdd($date, $interval, $datePart)
		);
	}

	/**
	 * @testdox  The delete method correctly creates a DELETE query element without a table name
	 */
	public function testDeleteWithoutTable()
	{
		$this->assertSame($this->query, $this->query->delete(), 'The query builder supports method chaining');

		$this->assertNotNull($this->query->delete);
		$this->assertNull($this->query->from);
	}

	/**
	 * @testdox  The delete method correctly creates a DELETE and FROM query element with a table name
	 */
	public function testDeleteWithTable()
	{
		$this->assertSame($this->query, $this->query->delete('#__content'), 'The query builder supports method chaining');

		$this->assertNotNull($this->query->delete);
		$this->assertNotNull($this->query->from);
	}

	/**
	 * @testdox  The exec method correctly creates and manages a EXEC query element
	 */
	public function testExec()
	{
		$this->assertSame($this->query, $this->query->exec('foo'), 'The query builder supports method chaining');
		$this->query->exec('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->exec->getElements()
		);
	}

	/**
	 * @testdox  A SQL statement for the MySQL find_in_set() function is generated
	 */
	public function testFindInSet()
	{
		$this->assertSame(
			'',
			$this->query->findInSet('foo', 'a.data')
		);
	}

	/**
	 * @testdox  The from method correctly creates and manages a FROM query element
	 */
	public function testFrom()
	{
		$this->assertSame($this->query, $this->query->from('foo'), 'The query builder supports method chaining');
		$this->query->from('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->from->getElements()
		);
	}

	/**
	 * @testdox  The query can be aliased
	 */
	public function testAlias()
	{
		$this->assertSame($this->query, $this->query->alias('foo'), 'The query builder supports method chaining');

		$this->assertSame(
			'foo',
			$this->query->alias
		);
	}

	/**
	 * @testdox  A SQL statement to extract the year from a date is generated
	 */
	public function testYear()
	{
		$this->assertSame(
			'YEAR(a.created)',
			$this->query->year('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the month from a date is generated
	 */
	public function testMonth()
	{
		$this->assertSame(
			'MONTH(a.created)',
			$this->query->month('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the day from a date is generated
	 */
	public function testDay()
	{
		$this->assertSame(
			'DAY(a.created)',
			$this->query->day('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the hour from a date is generated
	 */
	public function testHour()
	{
		$this->assertSame(
			'HOUR(a.created)',
			$this->query->hour('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the minute from a date is generated
	 */
	public function testMinute()
	{
		$this->assertSame(
			'MINUTE(a.created)',
			$this->query->minute('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the second from a date is generated
	 */
	public function testSecond()
	{
		$this->assertSame(
			'SECOND(a.created)',
			$this->query->second('a.created')
		);
	}

	/**
	 * @testdox  The group method correctly creates and manages a GROUP BY query element
	 */
	public function testGroup()
	{
		$this->assertSame($this->query, $this->query->group('foo'), 'The query builder supports method chaining');
		$this->query->group('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->group->getElements()
		);
	}

	/**
	 * @testdox  The having method correctly creates and manages a HAVING query element
	 */
	public function testHaving()
	{
		$this->assertSame($this->query, $this->query->having('foo'), 'The query builder supports method chaining');
		$this->query->having('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->having->getElements()
		);
	}

	/**
	 * @testdox  The insert method correctly creates a INSERT query element
	 */
	public function testInsert()
	{
		$this->assertSame($this->query, $this->query->insert('foo'), 'The query builder supports method chaining');

		$this->assertNotNull($this->query->insert);
	}

	/**
	 * @testdox  The join method correctly creates a JOIN query element
	 */
	public function testJoin()
	{
		$this->assertSame($this->query, $this->query->join('inner', 'foo'), 'The query builder supports method chaining');
		$this->query->join('inner', 'bar');

		$this->assertCount(
			2,
			$this->query->join
		);
	}

	/**
	 * @testdox  The innerJoin method correctly creates a INNER JOIN query element
	 */
	public function testInnerJoin()
	{
		$this->assertSame($this->query, $this->query->innerJoin('foo'), 'The query builder supports method chaining');
		$this->query->innerJoin('bar');

		$this->assertCount(
			2,
			$this->query->join
		);
	}

	/**
	 * @testdox  The outerJoin method correctly creates a OUTER JOIN query element
	 */
	public function testOuterJoin()
	{
		$this->assertSame($this->query, $this->query->outerJoin('foo'), 'The query builder supports method chaining');
		$this->query->outerJoin('bar');

		$this->assertCount(
			2,
			$this->query->join
		);
	}

	/**
	 * @testdox  The leftJoin method correctly creates a LEFT JOIN query element
	 */
	public function testLeftJoin()
	{
		$this->assertSame($this->query, $this->query->leftJoin('foo'), 'The query builder supports method chaining');
		$this->query->leftJoin('bar');

		$this->assertCount(
			2,
			$this->query->join
		);
	}

	/**
	 * @testdox  The rightJoin method correctly creates a RIGHT JOIN query element
	 */
	public function testRightJoin()
	{
		$this->assertSame($this->query, $this->query->rightJoin('foo'), 'The query builder supports method chaining');
		$this->query->rightJoin('bar');

		$this->assertCount(
			2,
			$this->query->join
		);
	}

	/**
	 * @testdox  A SQL statement to get the length of a field is generated
	 */
	public function testLength()
	{
		$this->assertSame(
			'LENGTH(a.created)',
			$this->query->length('a.created')
		);
	}

	/**
	 * Data provider for null date test cases
	 *
	 * @return  \Generator
	 */
	public function dataNullDate(): \Generator
	{
		yield 'null date with quote' => [true, "'0000-00-00 00:00:00'"];
		yield 'null date without quote' => [false, '0000-00-00 00:00:00'];
	}

	/**
	 * @testdox  The null date from the database driver is retrieved
	 *
	 * @param   boolean  $quoted    Optionally wraps the null date in database quotes (true by default).
	 * @param   string   $expected  The expected query string.
	 *
	 * @dataProvider  dataNullDate
	 */
	public function testNullDate(bool $quoted, string $expected)
	{
		$this->db->expects($this->once())
			->method('getNullDate')
			->willReturn('0000-00-00 00:00:00');

		$this->db->expects($this->any())
			->method('quote')
			->willReturnCallback(function ($text, $escape = true) {
				return "'" . $text . "'";
			});

		$this->assertSame(
			$expected,
			$this->query->nullDate($quoted)
		);
	}

	/**
	 * @testdox  The null date cannot be retrieved from the database driver if no driver is present
	 */
	public function testNullDateException()
	{
		$this->expectException(\RuntimeException::class);

		$query = $this->getMockForAbstractClass(
			DatabaseQuery::class,
			[]
		);

		$query->nullDate();
	}

	/**
	 * @testdox  A SQL statement to determine if a field contains a null date is generated when the query has no known null dates
	 */
	public function testIsNullDatetimeNoDates()
	{
		$this->assertSame(
			'a.created IS NULL',
			$this->query->isNullDatetime('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to determine if a field contains a null date is generated when the query has known null dates
	 */
	public function testIsNullDatetimeWithDates()
	{
		$this->db->expects($this->any())
			->method('quote')
			->willReturnCallback(function ($text, $escape = true) {
				foreach ($text as $k => $v)
				{
					$text[$k] = "'" . $v . "'";
				}

				return $text;
			});

		$query = new class($this->db) extends DatabaseQuery
		{
			protected $nullDatetimeList = ['0000-00-00 00:00:00', '1000-01-01 00:00:00'];

			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				return $query;
			}
		};

		$this->assertSame(
			"(a.created IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') OR a.created IS NULL)",
			$query->isNullDatetime('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to determine if a field contains a null date cannot be retrieved from the database driver if no driver is present
	 */
	public function testIsNullDatetimeException()
	{
		$this->expectException(\RuntimeException::class);

		$query = $this->getMockForAbstractClass(
			DatabaseQuery::class,
			[]
		);

		$query->isNullDatetime('a.created');
	}

	/**
	 * @testdox  The order method correctly creates and manages a ORDER BY query element
	 */
	public function testOrder()
	{
		$this->assertSame($this->query, $this->query->order('foo'), 'The query builder supports method chaining');
		$this->query->order('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->order->getElements()
		);
	}

	/**
	 * @testdox  A string can be quoted
	 */
	public function testQuote()
	{
		$this->db->expects($this->any())
			->method('quote')
			->willReturnCallback(function ($text, $escape = true) {
				return "'" . $text . "'";
			});

		$this->assertSame(
			"'foo'",
			$this->query->quote('foo')
		);
	}

	/**
	 * @testdox  A string cannot be quoted if no database driver is present
	 */
	public function testQuoteException()
	{
		$this->expectException(\RuntimeException::class);

		$query = $this->getMockForAbstractClass(
			DatabaseQuery::class,
			[]
		);

		$query->quote('foo');
	}

	/**
	 * @testdox  A string can be quoted as a field identifier
	 */
	public function testQuoteName()
	{
		$this->db->expects($this->any())
			->method('quoteName')
			->willReturnCallback(function ($text, $escape = true) {
				return "`" . $text . "`";
			});

		$this->assertSame(
			"`foo`",
			$this->query->quoteName('foo')
		);
	}

	/**
	 * @testdox  A string cannot be quoted as a field identifier if no database driver is present
	 */
	public function testQuoteNameException()
	{
		$this->expectException(\RuntimeException::class);

		$query = $this->getMockForAbstractClass(
			DatabaseQuery::class,
			[]
		);

		$query->quoteName('foo');
	}

	/**
	 * @testdox  A SQL statement to get a random floating point value is generated
	 */
	public function testRand()
	{
		$this->assertSame(
			'',
			$this->query->rand()
		);
	}

	/**
	 * @testdox  A SQL statement to prepend a string with a regex operator is generated
	 */
	public function testRegexp()
	{
		$this->assertSame(
			' foo',
			$this->query->regexp('foo')
		);
	}

	/**
	 * @testdox  The select method correctly creates and manages a SELECT query element
	 */
	public function testSelect()
	{
		$this->assertSame($this->query, $this->query->select('foo'), 'The query builder supports method chaining');
		$this->query->select('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->select->getElements()
		);
	}

	/**
	 * @testdox  The set method correctly creates and manages a SET query element
	 */
	public function testSet()
	{
		$this->assertSame($this->query, $this->query->set('foo'), 'The query builder supports method chaining');
		$this->query->set('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->set->getElements()
		);
	}

	/**
	 * @testdox  The setLimit method correctly manages the limit and offset for a query
	 */
	public function testSetLimit()
	{
		$this->assertSame($this->query, $this->query->setLimit(10, 25), 'The query builder supports method chaining');

		$this->assertSame(
			10,
			$this->query->limit
		);

		$this->assertSame(
			25,
			$this->query->offset
		);
	}

	/**
	 * @testdox  The setQuery method correctly manages an injected SQL query
	 */
	public function testSetQuery()
	{
		$query = 'SELECT foo FROM bar';

		$this->assertSame($this->query, $this->query->setQuery($query), 'The query builder supports method chaining');

		$this->assertSame(
			$query,
			$this->query->sql
		);
	}

	/**
	 * @testdox  The update method correctly creates a UPDATE query element
	 */
	public function testUpdate()
	{
		$this->assertSame($this->query, $this->query->update('foo'), 'The query builder supports method chaining');

		$this->assertNotNull($this->query->update);
	}

	/**
	 * @testdox  The values method correctly creates and manages a list of values
	 */
	public function testValues()
	{
		$this->assertSame($this->query, $this->query->values('foo'), 'The query builder supports method chaining');
		$this->query->values('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->values->getElements()
		);
	}

	/**
	 * @testdox  The where method correctly creates and manages a WHERE query element
	 */
	public function testWhere()
	{
		$this->assertSame($this->query, $this->query->where('foo'), 'The query builder supports method chaining');
		$this->query->where('bar');

		$this->assertSame(
			['foo', 'bar'],
			$this->query->where->getElements()
		);
	}

	/**
	 * @testdox  The whereIn method correctly creates and manages a WHERE query element with parameter binding
	 */
	public function testWhereIn()
	{
		$this->assertSame($this->query, $this->query->whereIn('foo', [1, 2]), 'The query builder supports method chaining');
		$this->query->whereIn('bar', [3, 4]);

		$this->assertSame(
			['foo IN (:preparedArray1,:preparedArray2)', 'bar IN (:preparedArray3,:preparedArray4)'],
			$this->query->where->getElements()
		);
	}

	/**
	 * @testdox  The whereNotIn method correctly creates and manages a WHERE query element with parameter binding
	 */
	public function testWhereNotIn()
	{
		$this->assertSame($this->query, $this->query->whereNotIn('foo', [1, 2]), 'The query builder supports method chaining');
		$this->query->whereNotIn('bar', [3, 4]);

		$this->assertSame(
			['foo NOT IN (:preparedArray1,:preparedArray2)', 'bar NOT IN (:preparedArray3,:preparedArray4)'],
			$this->query->where->getElements()
		);
	}

	/**
	 * @testdox  The extendWhere method correctly overrides a WHERE query element
	 */
	public function testExtendWhere()
	{
		$this->query->where('foo');
		$this->assertSame($this->query, $this->query->extendWhere('OR', 'bar'), 'The query builder supports method chaining');

		$this->assertCount(
			2,
			$this->query->where->getElements()
		);
	}

	/**
	 * @testdox  The orWhere method correctly overrides a WHERE query element
	 */
	public function testOrWhere()
	{
		$this->query->where('foo');
		$this->assertSame($this->query, $this->query->orWhere('bar'), 'The query builder supports method chaining');

		$this->assertCount(
			2,
			$this->query->where->getElements()
		);
	}

	/**
	 * @testdox  The andWhere method correctly overrides a WHERE query element
	 */
	public function testAndWhere()
	{
		$this->query->where('foo');
		$this->assertSame($this->query, $this->query->andWhere('bar'), 'The query builder supports method chaining');

		$this->assertCount(
			2,
			$this->query->where->getElements()
		);
	}

	/**
	 * Data provider for bind test cases
	 *
	 * @return  \Generator
	 */
	public function dataBind(): \Generator
	{
		yield 'string field' => ['foo', 'bar', ParameterType::STRING];
		yield 'numeric field' => ['foo', 42, ParameterType::INTEGER];
		yield 'numeric key' => [1, 'bar', ParameterType::STRING];
		yield 'array of data' => [[1, 'foo'], [42, 'bar'], [ParameterType::INTEGER, ParameterType::STRING]];
	}

	/**
	 * @testdox  The bind method records a bound parameter for the query
	 *
	 * @param   array|string|integer  $key            The key that will be used in your SQL query to reference the value. Usually of
	 *                                                the form ':key', but can also be an integer.
	 * @param   mixed                 $value          The value that will be bound. It can be an array, in this case it has to be
	 *                                                same length of $key; The value is passed by reference to support output
	 *                                                parameters such as those possible with stored procedures.
	 * @param   array|string          $dataType       Constant corresponding to a SQL datatype. It can be an array, in this case it
	 *                                                has to be same length of $key
	 *
	 * @dataProvider  dataBind
	 */
	public function testBind($key, $value, $dataType)
	{
		$this->assertSame($this->query, $this->query->bind($key, $value, $dataType), 'The query builder supports method chaining');

		$this->assertCount(
			is_array($key) ? count($key) : 1,
			$this->query->bounded
		);
	}

	/**
	 * @testdox  The bind method does not record bound parameters when the keys and values are an unbalanced number of items
	 */
	public function testBindUnbalancedKeyValue()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Array length of $key and $value are not equal');

		$keys      = [1, 2, 3];
		$values    = ['bar'];
		$dataTypes = [ParameterType::STRING, ParameterType::STRING, ParameterType::STRING];

		$this->query->bind($keys, $values, $dataTypes);
	}

	/**
	 * @testdox  The bind method does not record bound parameters when the keys and data types are an unbalanced number of items
	 */
	public function testBindUnbalancedKeyDataType()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Array length of $key and $dataType are not equal');

		$keys      = [1, 2, 3];
		$values    = ['bar', 'car', 'far'];
		$dataTypes = [ParameterType::STRING];

		$this->query->bind($keys, $values, $dataTypes);
	}

	/**
	 * @testdox  The bindArray method creates bound parameters for an array and returns the parameter names
	 */
	public function testBindArray()
	{
		$this->assertSame(
			[':preparedArray1', ':preparedArray2', ':preparedArray3'],
			$this->query->bindArray([1, 2, 3], ParameterType::INTEGER)
		);
	}

	/**
	 * @testdox  The union method correctly creates and manages a merge query element
	 */
	public function testUnion()
	{
		$this->assertSame($this->query, $this->query->union('foo'), 'The query builder supports method chaining');
		$this->query->union('bar');

		$this->assertCount(
			2,
			$this->query->merge
		);
	}

	/**
	 * @testdox  The unionAll method correctly creates and manages a merge query element
	 */
	public function testUnionAll()
	{
		$this->assertSame($this->query, $this->query->unionAll('foo'), 'The query builder supports method chaining');
		$this->query->unionAll('bar');

		$this->assertCount(
			2,
			$this->query->merge
		);
	}

	/**
	 * @testdox  The querySet method correctly marks the query type
	 */
	public function testQuerySet()
	{
		$this->assertSame($this->query, $this->query->querySet('SELECT foo FROM bar'), 'The query builder supports method chaining');

		$this->assertSame(
			'SELECT foo FROM bar',
			$this->query->querySet
		);
	}

	/**
	 * @testdox  The query is converted to a querySet type
	 */
	public function testToQuerySet()
	{
		$this->query->setQuery('SELECT foo FROM bar');

		$querySetQuery = $this->query->toQuerySet();

		$this->assertNotSame($querySetQuery, $this->query);
	}

	/**
	 * @testdox  A query object containing a SELECT query is converted to a proper SQL string
	 */
	public function testCastingToStringSelect()
	{
		$query = new class($this->db) extends DatabaseQuery
		{
			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				if ($limit > 0 && $offset > 0)
				{
					$query .= ' LIMIT ' . $offset . ', ' . $limit;
				}
				elseif ($limit > 0)
				{
					$query .= ' LIMIT ' . $limit;
				}

				return $query;
			}
		};

		$query->select(['a.*', 'COUNT(b.a_id) AS b_things'])
			->from('foo a')
			->leftJoin('bar b', 'a.id = b.a_id')
			->where($query->isNullDatetime('a.created'))
			->group('a.language')
			->having('b_things > 3')
			->order(['a.id ASC'])
			->setLimit(10, 1);

		$expected = PHP_EOL . 'SELECT a.*,COUNT(b.a_id) AS b_things';
		$expected .= PHP_EOL . 'FROM foo a';
		$expected .= PHP_EOL . 'LEFT JOIN bar b ON a.id = b.a_id';
		$expected .= PHP_EOL . 'WHERE a.created IS NULL';
		$expected .= PHP_EOL . 'GROUP BY a.language';
		$expected .= PHP_EOL . 'HAVING b_things > 3';
		$expected .= PHP_EOL . 'ORDER BY a.id ASC LIMIT 1, 10';

		$this->assertSame($expected, (string) $query);
	}

	/**
	 * @testdox  A query object containing an aliased SELECT query is converted to a proper SQL string
	 */
	public function testCastingToStringSelectAliased()
	{
		$query = new class($this->db) extends DatabaseQuery
		{
			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				return $query;
			}
		};

		$query->select(['a.*', 'COUNT(b.a_id) AS b_things'])
			->from('foo a')
			->leftJoin('bar b', 'a.id = b.a_id')
			->alias('sub');

		$expected = '(';
		$expected .= PHP_EOL . 'SELECT a.*,COUNT(b.a_id) AS b_things';
		$expected .= PHP_EOL . 'FROM foo a';
		$expected .= PHP_EOL . 'LEFT JOIN bar b ON a.id = b.a_id) AS sub';

		$this->assertSame($expected, (string) $query);
	}

	/**
	 * @testdox  A query object containing a DELETE query is converted to a proper SQL string
	 */
	public function testCastingToStringDelete()
	{
		$query = new class($this->db) extends DatabaseQuery
		{
			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				return $query;
			}
		};

		$query->delete('foo a')
			->leftJoin('bar b', 'a.id = b.a_id')
			->where($query->isNullDatetime('a.created'));

		// There is an expected trailing whitespace after the DELETE statement
		$expected = PHP_EOL . 'DELETE ';
		$expected .= PHP_EOL . 'FROM foo a';
		$expected .= PHP_EOL . 'LEFT JOIN bar b ON a.id = b.a_id';
		$expected .= PHP_EOL . 'WHERE a.created IS NULL';

		$this->assertSame($expected, (string) $query);
	}

	/**
	 * @testdox  A query object containing a UPDATE query is converted to a proper SQL string
	 */
	public function testCastingToStringUpdate()
	{
		$query = new class($this->db) extends DatabaseQuery
		{
			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				return $query;
			}
		};

		$query->update('foo a')
			->leftJoin('bar b', 'a.id = b.a_id')
			->set('a.updated = ' . $query->currentTimestamp())
			->whereIn('b.id', [1, 2, 3]);

		$expected = PHP_EOL . 'UPDATE foo a';
		$expected .= PHP_EOL . 'LEFT JOIN bar b ON a.id = b.a_id';
		$expected .= PHP_EOL . 'SET a.updated = CURRENT_TIMESTAMP()';
		$expected .= PHP_EOL . 'WHERE b.id IN (:preparedArray1,:preparedArray2,:preparedArray3)';

		$this->assertSame($expected, (string) $query);
	}

	/**
	 * @testdox  A query object containing a INSERT query with SET notation is converted to a proper SQL string
	 */
	public function testCastingToStringInsertSet()
	{
		$this->db->expects($this->any())
			->method('quote')
			->willReturnCallback(function ($text, $escape = true) {
				return "'" . $text . "'";
			});

		$query = new class($this->db) extends DatabaseQuery
		{
			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				return $query;
			}
		};

		$query->insert('foo a')
			->set('a.data = ' . $query->quote(json_encode(['hello' => 'world'])))
			->set('a.updated = ' . $query->currentTimestamp());

		$expected = PHP_EOL . 'INSERT INTO foo a';
		$expected .= PHP_EOL . 'SET a.data = \'{"hello":"world"}\'';
		$expected .= PHP_EOL . "\t, a.updated = CURRENT_TIMESTAMP()";

		$this->assertSame($expected, (string) $query);
	}

	/**
	 * @testdox  A query object containing a INSERT query with COLUMNS/VALUES notation is converted to a proper SQL string
	 */
	public function testCastingToStringInsertColumnsValues()
	{
		$this->db->expects($this->any())
			->method('quote')
			->willReturnCallback(function ($text, $escape = true) {
				return "'" . $text . "'";
			});

		$query = new class($this->db) extends DatabaseQuery
		{
			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				return $query;
			}
		};

		$query->insert('foo a')
			->columns(['a.data', 'a.updated'])
			->values([$query->quote(json_encode(['hello' => 'world'])) . ', ' . $query->currentTimestamp()]);

		// There is an expected trailing whitespace after the VALUES statement
		$expected = PHP_EOL . 'INSERT INTO foo a';
		$expected .= PHP_EOL . '(a.data,a.updated) VALUES ';
		$expected .= PHP_EOL . '(\'{"hello":"world"}\', CURRENT_TIMESTAMP())';

		$this->assertSame($expected, (string) $query);
	}

	/**
	 * @testdox  A query object containing a CALL query is converted to a proper SQL string
	 */
	public function testCastingToStringCall()
	{
		$query = new class($this->db) extends DatabaseQuery
		{
			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				return $query;
			}
		};

		$query->call('a.foo');

		$expected = PHP_EOL . 'CALL a.foo';

		$this->assertSame($expected, (string) $query);
	}

	/**
	 * @testdox  A query object containing a EXEC query is converted to a proper SQL string
	 */
	public function testCastingToStringExec()
	{
		$query = new class($this->db) extends DatabaseQuery
		{
			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				return $query;
			}
		};

		$query->exec('a.foo');

		$expected = PHP_EOL . 'EXEC a.foo';

		$this->assertSame($expected, (string) $query);
	}

	/**
	 * @testdox  A query object containing an injected query is converted to a proper SQL string
	 */
	public function testCastingToStringInjectedQuery()
	{
		$query = new class($this->db) extends DatabaseQuery
		{
			public function groupConcat($column, $separator = ',')
			{
				return '';
			}

			public function processLimit($query, $limit, $offset = 0)
			{
				return $query;
			}
		};

		$query->setQuery('SELECT foo FROM bar');

		$this->assertSame('SELECT foo FROM bar', (string) $query);
	}
}
