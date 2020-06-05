<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mysqli;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Mysqli\MysqliQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Mysqli\MysqliQuery.
 */
class MysqliQueryTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  MysqliQuery
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
		$this->query = new MysqliQuery($this->db);
	}

	/**
	 * Data provider for concatenate test cases
	 *
	 * @return  \Generator
	 */
	public function dataConcatenate(): \Generator
	{
		yield 'values without separator' => [['foo', 'bar'], null, 'CONCAT(foo,bar)'];
		yield 'values with separator' => [['foo', 'bar'], ' and ', "CONCAT_WS(' and ', foo, bar)"];
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
	 * @testdox  A SQL statement for the MySQL find_in_set() function is generated
	 */
	public function testFindInSet()
	{
		$this->assertSame(
			' find_in_set(foo, a.data)',
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
			"GROUP_CONCAT(a.foo SEPARATOR ',')",
			$this->query->groupConcat('a.foo')
		);
	}

	/**
	 * @testdox  A SQL statement to get a random floating point value is generated
	 */
	public function testRand()
	{
		$this->assertSame(
			' RAND() ',
			$this->query->rand()
		);
	}

	/**
	 * @testdox  A SQL statement to prepend a string with a regex operator is generated
	 */
	public function testRegexp()
	{
		$this->assertSame(
			' REGEXP foo',
			$this->query->regexp('foo')
		);
	}

	/**
	 * @testdox  A string is cast as a character string for the driver
	 */
	public function testCastAsWithChar()
	{
		$this->assertSame('123', $this->query->castAs('CHAR', '123'));
	}

	/**
	 * @testdox  The length param is added to the CAST statement when provided
	 */
	public function testCastAsWithCharAndLengthParam()
	{
		$this->assertSame(
			'CAST(123 AS CHAR(2))',
			$this->query->castAs('CHAR', '123', 2)
		);
	}

	/**
	 * @testdox  Test castAs behaviour with INT (adds 0 to the input)
	 */
	public function testCastAsWithIntegerType()
	{
		$this->assertSame(
			'(123 + 0)',
			$this->query->castAs('INT', '123')
		);
	}
}
