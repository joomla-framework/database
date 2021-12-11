<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Pgsql;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Pgsql\PgsqlQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Pgsql\PgsqlQuery.
 */
class PgsqlQueryTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  PgsqlQuery
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
		$this->query = new PgsqlQuery($this->db);
	}

	/**
	 * @testdox  A string is cast as a character string for the driver
	 */
	public function testCastAsChar()
	{
		$this->assertSame('foo::text', $this->query->castAsChar('foo'));
	}

	/**
	 * @testdox  A string is cast as a character string for the driver
	 */
	public function testCastAsWithChar()
	{
		$this->assertSame('foo::text', $this->query->castAs('CHAR', 'foo'));
	}

	/**
	 * @testdox  The length param is added to the CAST statement when provided
	 */
	public function testCastAsWithCharAndLengthParam()
	{
		$this->assertSame(
			'CAST(foo AS CHAR(2))',
			$this->query->castAs('CHAR', 'foo', 2)
		);
	}

	/**
	 * @testdox  Test castAs behaviour with INT
	 */
	public function testCastAsWithIntegerType()
	{
		$this->assertSame(
			'CAST(123 AS INTEGER)',
			$this->query->castAs('INT', '123')
		);
	}

	/**
	 * Data provider for concatenate test cases
	 *
	 * @return  \Generator
	 */
	public function dataConcatenate(): \Generator
	{
		yield 'values without separator' => [['foo', 'bar'], null, 'foo || bar'];
		yield 'values with separator' => [['foo', 'bar'], ' and ', "foo || ' and ' || bar"];
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
			'NOW()',
			$this->query->currentTimestamp()
		);
	}

	/**
	 * @testdox  A SQL statement for the MySQL find_in_set() function is generated
	 */
	public function testFindInSet()
	{
		$this->assertSame(
			" foo = ANY (string_to_array(a.data, ',')::integer[]) ",
			$this->query->findInSet('foo', 'a.data')
		);
	}

	/**
	 * @testdox  A SQL statement to concatenate a group of values is generated
	 */
	public function testGroupConcat()
	{
		$this->db->expects($this->any())
			->method('quote')
			->willReturnCallback(function ($text, $escape = true) {
				return "'" . $text . "'";
			});

		$this->assertSame(
			"string_agg(a.foo, ',')",
			$this->query->groupConcat('a.foo')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the year from a date is generated
	 */
	public function testYear()
	{
		$this->assertSame(
			'EXTRACT (YEAR FROM a.created)',
			$this->query->year('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the month from a date is generated
	 */
	public function testMonth()
	{
		$this->assertSame(
			'EXTRACT (MONTH FROM a.created)',
			$this->query->month('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the day from a date is generated
	 */
	public function testDay()
	{
		$this->assertSame(
			'EXTRACT (DAY FROM a.created)',
			$this->query->day('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the hour from a date is generated
	 */
	public function testHour()
	{
		$this->assertSame(
			'EXTRACT (HOUR FROM a.created)',
			$this->query->hour('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the minute from a date is generated
	 */
	public function testMinute()
	{
		$this->assertSame(
			'EXTRACT (MINUTE FROM a.created)',
			$this->query->minute('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement to extract the second from a date is generated
	 */
	public function testSecond()
	{
		$this->assertSame(
			'EXTRACT (SECOND FROM a.created)',
			$this->query->second('a.created')
		);
	}

	/**
	 * Data provider for dateAdd test cases
	 *
	 * @return  \Generator
	 */
	public function dataDateAdd(): \Generator
	{
		yield 'date with positive interval' => ["'2019-10-13'", '1', 'DAY', "timestamp '2019-10-13' + interval '1 DAY'"];
		yield 'date with negative interval' => ["'2019-10-13'", '-1', 'DAY', "timestamp '2019-10-13' - interval '1 DAY'"];
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
	 * @testdox  A SQL statement to get a random floating point value is generated
	 */
	public function testRand()
	{
		$this->assertSame(
			' RANDOM() ',
			$this->query->rand()
		);
	}

	/**
	 * @testdox  A SQL statement to prepend a string with a regex operator is generated
	 */
	public function testRegexp()
	{
		$this->assertSame(
			' ~* foo',
			$this->query->regexp('foo')
		);
	}
}
