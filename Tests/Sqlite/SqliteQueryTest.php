<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Sqlite;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\Sqlite\SqliteQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Sqlite\SqliteQuery.
 */
class SqliteQueryTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  SqliteQuery
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
		$this->query = new SqliteQuery($this->db);
	}

	/**
	 * Data provider for character length test cases
	 *
	 * @return  \Generator
	 */
	public function dataCharLength(): \Generator
	{
		yield 'field without comparison' => ['a.title', null, null, 'length(a.title)'];
		yield 'field with comparison' => ['a.title', '!=', '0', 'length(a.title) != 0'];
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
			"group_concat(a.foo, ',')",
			$this->query->groupConcat('a.foo')
		);
	}
}
