<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlsrv;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Sqlsrv\SqlsrvQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Sqlsrv\SqlsrvQuery.
 */
class SqlsrvQueryTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  SqlsrvQuery
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
		$this->query = new SqlsrvQuery($this->db);
	}

	/**
	 * @testdox  A string is cast as a character string for the driver
	 */
	public function testCastAsChar()
	{
		$this->assertSame('CAST(foo as NVARCHAR(10))', $this->query->castAsChar('foo'));
	}

	/**
	 * @testdox  A string is cast as a character string for the driver
	 */
	public function testCastAsWithChar()
	{
		$this->assertSame('CAST(foo as NVARCHAR(10))', $this->query->castAs('CHAR', 'foo'));
	}

	/**
	 * @testdox  The length param is added to the CAST statement when provided
	 */
	public function testCastAsWithCharAndLengthParam()
	{
		$this->assertSame(
			'CAST(foo as NVARCHAR(2))',
			$this->query->castAs('CHAR', 'foo', 2)
		);
	}

	/**
	 * @testdox  Test castAs behaviour with INT
	 */
	public function testCastAsWithIntegerType()
	{
		$this->assertSame(
			'CAST(123 AS INT)',
			$this->query->castAs('INT', '123')
		);
	}

	/**
	 * Data provider for character length test cases
	 *
	 * @return  \Generator
	 */
	public function dataCharLength(): \Generator
	{
		yield 'field without comparison' => ['a.title', null, null, 'DATALENGTH(a.title)'];
		yield 'field with comparison' => ['a.title', '!=', '0', 'DATALENGTH(a.title) != 0'];
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
	 * Data provider for concatenate test cases
	 *
	 * @return  \Generator
	 */
	public function dataConcatenate(): \Generator
	{
		yield 'values without separator' => [['foo', 'bar'], null, '(foo+bar)'];
		yield 'values with separator' => [['foo', 'bar'], ' and ', "(foo+' and '+bar)"];
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
			'GETDATE()',
			$this->query->currentTimestamp()
		);
	}

	/**
	 * @testdox  A SQL statement to get the length of a field is generated
	 */
	public function testLength()
	{
		$this->assertSame(
			'LEN(a.created)',
			$this->query->length('a.created')
		);
	}

	/**
	 * @testdox  A SQL statement for the MySQL find_in_set() function is generated
	 */
	public function testFindInSet()
	{
		$this->assertSame(
			"CHARINDEX(',foo,', ',' + a.data + ',') > 0",
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
	 * @testdox  A SQL statement to get a random floating point value is generated
	 */
	public function testRand()
	{
		$this->assertSame(
			' NEWID() ',
			$this->query->rand()
		);
	}
}
