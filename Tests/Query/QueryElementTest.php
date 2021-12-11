<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Query;

use Joomla\Database\Query\QueryElement;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Query\QueryElement
 */
class QueryElementTest extends TestCase
{
	/**
	 * Data provider for instantiation test cases
	 *
	 * Each test case provides
	 * - array   $element   the base element for the test, given as an array
	 *                      name => element_name,
	 *                      elements => array or string
	 *                      glue => glue
	 * - array   $expected  values in same array format
	 *
	 * @return  \Generator
	 */
	public function dataInstantiation(): \Generator
	{
		yield 'array-element' => [
			[
				'name'     => 'FROM',
				'elements' => ['field1', 'field2'],
				'glue'     => ',',
			],
			[
				'name'     => 'FROM',
				'elements' => ['field1', 'field2'],
				'glue'     => ',',
			],
		];

		yield 'non-array-element' => [
			[
				'name'     => 'TABLE',
				'elements' => 'my_table_name',
				'glue'     => ',',
			],
			[
				'name'     => 'TABLE',
				'elements' => ['my_table_name'],
				'glue'     => ',',
			],
		];
	}

	/**
	 * @testdox  The object is correctly configured when instantiated
	 *
	 * @param   array  $element   values for base element
	 * @param   array  $expected  values for expected fields
	 *
	 * @dataProvider  dataInstantiation
	 */
	public function testInstantiation(array $element, array $expected)
	{
		$baseElement = new QueryElement($element['name'], $element['elements'], $element['glue']);

		$this->assertEquals(
			$expected['name'],
			$baseElement->getName()
		);

		$this->assertEquals(
			$expected['elements'],
			$baseElement->getElements()
		);

		$this->assertEquals(
			$expected['glue'],
			$baseElement->getGlue()
		);
	}

	/**
	 * Data provider for string casting test cases
	 *
	 * Each test case provides
	 * - string        $name      the name of the element
	 * - array|string  $elements  the element data
	 * - string        $glue      the element glue
	 * - string        $expected  expected result
	 *
	 * @return  \Generator
	 */
	public function dataCastingToString(): \Generator
	{
		yield [
			'FROM',
			'table1',
			',',
			PHP_EOL . 'FROM table1',
		];

		yield [
			'SELECT',
			['column1', 'column2'],
			',',
			PHP_EOL . 'SELECT column1,column2',
		];

		yield [
			'()',
			['column1', 'column2'],
			',',
			PHP_EOL . '(column1,column2)',
		];

		yield [
			'CONCAT()',
			['column1', 'column2'],
			',',
			PHP_EOL . 'CONCAT(column1,column2)',
		];
	}

	/**
	 * @testdox  A query element is converted to a string
	 *
	 * @param   string  $name      The name of the element.
	 * @param   mixed   $elements  String or array.
	 * @param   string  $glue      The glue for elements.
	 * @param   string  $expected  The expected value.
	 *
	 * @dataProvider  dataCastingToString
	 */
	public function testCastingToString($name, $elements, $glue, $expected)
	{
		$this->assertThat(
			(string) new QueryElement($name, $elements, $glue),
			$this->equalTo($expected)
		);
	}

	/**
	 * Data provider for append test cases
	 *
	 * Each test case provides
	 * - array    $element    the base element for the test, given as an array
	 *                        name => element_name,
	 *                        elements => element array,
	 *                        glue => glue
	 * - array    $append     the element to be appended (same format as above)
	 * - array    $expected   array of elements that should be the value of the elements attribute after the merge
	 * - string   $string     value of __toString() for element after append
	 *
	 * @return  \Generator
	 */
	public function dataAppend(): \Generator
	{
		yield 'array-element' => [
			[
				'name'     => 'SELECT',
				'elements' => [],
				'glue'     => ',',
			],
			[
				'name'     => 'FROM',
				'elements' => ['my_table_name'],
				'glue'     => ',',
			],
			[
				'name'     => 'FROM',
				'elements' => ['my_table_name'],
				'glue'     => ',',
			],
			PHP_EOL . 'SELECT ' . PHP_EOL . 'FROM my_table_name',
		];

		yield 'non-array-element' => [
			[
				'name'     => 'SELECT',
				'elements' => [],
				'glue'     => ',',
			],
			[
				'name'     => 'FROM',
				'elements' => ['my_table_name'],
				'glue'     => ',',
			],
			[
				'name'     => 'FROM',
				'elements' => ['my_table_name'],
				'glue'     => ',',
			],
			PHP_EOL . 'SELECT ' . PHP_EOL . 'FROM my_table_name',
		];
	}

	/**
	 * @testdox  Data can be appended to a query element
	 *
	 * @param   array   $element   base element values
	 * @param   array   $append    append element values
	 * @param   array   $expected  expected element values for elements field after append
	 * @param   string  $string    expected value of toString (not used in this test)
	 *
	 * @dataProvider  dataAppend
	 */
	public function testAppend($element, $append, $expected, $string)
	{
		$baseElement     = new QueryElement($element['name'], $element['elements'], $element['glue']);
		$appendElement   = new QueryElement($append['name'], $append['elements'], $append['glue']);
		$expectedElement = new QueryElement($expected['name'], $expected['elements'], $expected['glue']);

		$baseElement->append($appendElement);

		$this->assertEquals(
			[$expectedElement],
			$baseElement->getElements()
		);
	}

	/**
	 * @testdox  A query element can be cloned with a custom array property
	 */
	public function testCloneWithCustomArrayProperty()
	{
		$baseElement            = new QueryElement(null, null);
		$baseElement->testArray = [];

		$cloneElement = clone $baseElement;

		$baseElement->testArray[] = 'a';

		$this->assertNotSame($baseElement, $cloneElement);
		$this->assertCount(0, $cloneElement->testArray);
	}

	/**
	 * @testdox  A query element can be cloned with a custom object property
	 */
	public function testCloneWithCustomObjectProperty()
	{
		$baseElement             = new QueryElement(null, null);
		$baseElement->testObject = new \stdClass;

		$cloneElement = clone $baseElement;

		$this->assertNotSame($baseElement, $cloneElement);
		$this->assertNotSame($baseElement->testObject, $cloneElement->testObject);
	}
}
