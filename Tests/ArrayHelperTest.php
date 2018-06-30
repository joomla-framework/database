<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Utilities\ArrayHelper;
use PHPUnit\Framework\TestCase;

/**
 * ArrayHelperTest
 *
 * @since  1.0
 */
class ArrayHelperTest extends TestCase
{
	/**
	 * Data provider for testArrayUnique.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestArrayUnique()
	{
		return array(
			'Case 1' => array(
				// Input
				array(
					array(1, 2, 3, array(4)),
					array(2, 2, 3, array(4)),
					array(3, 2, 3, array(4)),
					array(2, 2, 3, array(4)),
					array(3, 2, 3, array(4)),
				),
				// Expected
				array(
					array(1, 2, 3, array(4)),
					array(2, 2, 3, array(4)),
					array(3, 2, 3, array(4)),
				),
			)
		);
	}

	/**
	 * Data provider for from object inputs
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestFromObject()
	{
		// Define a common array.
		$common = array('integer' => 12, 'float' => 1.29999, 'string' => 'A Test String');

		return array(
			'Invalid input' => array(
				// Array    The array being input
				null,
				// Boolean  Recurse through multiple dimensions
				null,
				// String   Regex to select only some attributes
				null,
				// String   The expected return value
				array(),
				// Boolean  Use function defaults (true) or full argument list
				true
			),
			'To single dimension array' => array(
				(object) $common,
				null,
				null,
				$common,
				true
			),
			'Object with nested arrays and object.' => array(
				(object) array(
					'foo' => $common,
					'bar' => (object) array(
						'goo' => $common,
					),
				),
				null,
				null,
				array(
					'foo' => $common,
					'bar' => array(
						'goo' => $common,
					),
				),
				true
			),
			'To single dimension array with recursion' => array(
				(object) $common,
				true,
				null,
				$common,
				false
			),
			'To single dimension array using regex on keys' => array(
				(object) $common,
				true,
				// Only get the 'integer' and 'float' keys.
				'/^(integer|float)/',
				array(
					'integer' => 12, 'float' => 1.29999
				),
				false
			),
			'Nested objects to single dimension array' => array(
				(object) array(
					'first' => (object) $common,
					'second' => (object) $common,
					'third' => (object) $common,
				),
				null,
				null,
				array(
					'first' => (object) $common,
					'second' => (object) $common,
					'third' => (object) $common,
				),
				false
			),
			'Nested objects into multiple dimension array' => array(
				(object) array(
					'first' => (object) $common,
					'second' => (object) $common,
					'third' => (object) $common,
				),
				null,
				null,
				array(
					'first' => $common,
					'second' => $common,
					'third' => $common,
				),
				true
			),
			'Nested objects into multiple dimension array 2' => array(
				(object) array(
					'first' => (object) $common,
					'second' => (object) $common,
					'third' => (object) $common,
				),
				true,
				null,
				array(
					'first' => $common,
					'second' => $common,
					'third' => $common,
				),
				true
			),
			'Nested objects into multiple dimension array 3' => array(
				(object) array(
					'first' => (object) $common,
					'second' => (object) $common,
					'third' => (object) $common,
				),
				false,
				null,
				array(
					'first' => (object) $common,
					'second' => (object) $common,
					'third' => (object) $common,
				),
				false
			),
			'multiple 4' => array(
				(object) array(
					'first' => 'Me',
					'second' => (object) $common,
					'third' => (object) $common,
				),
				false,
				null,
				array(
					'first' => 'Me',
					'second' => (object) $common,
					'third' => (object) $common,
				),
				false
			),
			'Nested objects into multiple dimension array of int and string' => array(
				(object) array(
					'first' => (object) $common,
					'second' => (object) $common,
					'third' => (object) $common,
				),
				true,
				'/(first|second|integer|string)/',
				array(
					'first' => array(
						'integer' => 12, 'string' => 'A Test String'
					), 'second' => array(
					'integer' => 12, 'string' => 'A Test String'
				),
				),
				false
			),
			'multiple 6' => array(
				(object) array(
					'first' => array(
						'integer' => 12,
						'float' => 1.29999,
						'string' => 'A Test String',
						'third' => (object) $common,
					),
					'second' => $common,
				),
				null,
				null,
				array(
					'first' => array(
						'integer' => 12,
						'float' => 1.29999,
						'string' => 'A Test String',
						'third' => $common,
					),
					'second' => $common,
				),
				true
			),
			'Array with nested arrays and object.' => array(
				array(
					'foo' => $common,
					'bar' => (object) array(
						'goo' => $common,
					),
				),
				null,
				null,
				array(
					'foo' => $common,
					'bar' => array(
						'goo' => $common,
					),
				),
				true
			),
		);
	}

	/**
	 * Data provider for add column
	 *
	 * @return  array
	 *
	 * @since   1.5.0
	 */
	public function seedTestAddColumn()
	{
		return array(
			'generic array' => array(
				array(
					array(
						1, 2, 3, 4, 5
					), array(
						6, 7, 8, 9, 10
					), array(
						11, 12, 13, 14, 15
					), array(
						16, 17, 18, 19, 20
					)
				),
				array(101, 106, 111, 116),
				null,
				null,
				array(
					array(
						1, 2, 3, 4, 5, 101
					), array(
						6, 7, 8, 9, 10, 106
					), array(
						11, 12, 13, 14, 15, 111
					), array(
						16, 17, 18, 19, 20, 116
					)
				),
				'Should add column #5'
			),
			'associative array' => array(
				array(
					'a' => array(
						1, 2, 3, 4, 5
					),
					'b' => array(
						6, 7, 8, 9, 10
					),
					'c' => array(
						11, 12, 13, 14, 15
					),
					'd' => array(
						16, 17, 18, 19, 20
					)
				),
				array('a' => 101, 'c' => 111, 'd' => 116, 'b' => 106),
				null,
				null,
				array(
					'a' => array(
						1, 2, 3, 4, 5, 101
					),
					'b' => array(
						6, 7, 8, 9, 10, 106
					),
					'c' => array(
						11, 12, 13, 14, 15, 111
					),
					'd' => array(
						16, 17, 18, 19, 20, 116
					)
				),
				'Should add column #5 in correct associative order'
			),
			'generic array with lookup key' => array(
				array(
					array(
						1, 2, 3, 4, 5
					), array(
						6, 7, 8, 9, 10
					), array(
						11, 12, 13, 14, 15
					), array(
						16, 17, 18, 19, 20
					)
				),
				array(11 => 111, 1 => 101, 6 => 106, 16 => 116),
				null,
				0,
				array(
					array(
						1, 2, 3, 4, 5, 101
					), array(
						6, 7, 8, 9, 10, 106
					), array(
						11, 12, 13, 14, 15, 111
					), array(
						16, 17, 18, 19, 20, 116
					)
				),
				'Should add column #5 [101, 106, 111, 116] with column #0 as matching keys'
			),
			'generic array with existing key as column name' => array(
				array(
					array(
						1, 2, 3, 4, 5
					), array(
						6, 7, 8, 9, 10
					), array(
						11, 12, 13, 14, 15
					), array(
						16, 17, 18, 19, 20
					)
				),
				array(11 => 111, 1 => 101, 6 => 106, 16 => 116),
				3,
				0,
				array(
					array(
						1, 2, 3, 101, 5,
					), array(
						6, 7, 8, 106, 10,
					), array(
						11, 12, 13, 111, 15,
					), array(
						16, 17, 18, 116, 20,
					)
				),
				'Should replace column #3 [4, 9, 14, 19] with [101, 106, 111, 116] respective to column #0 as matching keys'
			),
			'array of associative arrays' => array(
				array(
					array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				array(104, 109, 114, 119),
				'six',
				null,
				array(
					array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104
					),
					array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => 109
					),
					array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114
					),
					array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119
					)
				),
				'Should add column \'six\''
			),
			'array of associative array with key' => array(
				array(
					array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				array(4 => 104, 9 => 109, 14 => 114, 19 => 119),
				'six',
				'four',
				array(
					array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104
					),
					array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => 109
					),
					array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114
					),
					array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119
					)
				),
				'Should add column \'six\' with respective match from column \'four\''
			),
			'object array' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				array(104, 109, 114, 119),
				'six',
				null,
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => 109
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119
					)
				),
				'Should add column \'six\''
			),
			'object array with key' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				array(4 => 104, 9 => 109, 14 => 114, 19 => 119),
				'six',
				'four',
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => 109
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119
					)
				),
				'Should add column \'six\' with respective match from column \'four\''
			),
			'object array with invalid key' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => array('array is invalid for key'), 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				array(1 => 101, 6 => 106, 11 => 111, 16 => 116),
				'six',
				'one',
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 101
					),
					(object) array(
						'one' => array('array is invalid for key'), 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => null
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 111
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 116
					)
				),
				'Should add column \'six\' with keys from column \'one\' and invalid key should introduce an null value added in the new column'
			),
			'object array with one missing key' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				array(1 => 101, 6 => 106, 11 => 111, 16 => 116),
				'six',
				'one',
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 101
					),
					(object) array(
						'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10, 'six' => null
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 111
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 116
					)
				),
				'Should add column \'six\' with keys from column \'one\' and the missing key should add a null value in the new column'
			),
			'object array with one non matching value' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => -9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				array(4 => 104, 9 => 109, 14 => 114, 19 => 119),
				'six',
				'four',
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 104
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => -9, 'five' => 10, 'six' => null,
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15, 'six' => 114
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20, 'six' => 119
					)
				),
				'Should get column \'six\' with keys from column \'four\' and item with missing referenced value should set null in new column'
			),
			'object array with null column name' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				array(1 => 101,6 => 102, 11 => 103, 16 => 104),
				null,
				'one',
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'Should skip entire set and return the original value as automatic key is not possible with objects'
			),
		);
	}

	/**
	 * Data provider for add column
	 *
	 * @return  array
	 *
	 * @since   1.5.0
	 */
	public function seedTestDropColumn()
	{
		return array(
			'generic array' => array(
				array(
					array(
						1, 2, 3, 4, 5
					), array(
						6, 7, 8, 9, 10
					), array(
						11, 12, 13, 14, 15
					), array(
						16, 17, 18, 19, 20
					)
				),
				4,
				array(
					array(
						1, 2, 3, 4,
					), array(
						6, 7, 8, 9,
					), array(
						11, 12, 13, 14,
					), array(
						16, 17, 18, 19,
					)
				),
				'Should drop column #4'
			),
			'associative array' => array(
				array(
					array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'one',
				array(
					array(
						'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
					),
					array(
						'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
					),
					array(
						'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
					),
					array(
						'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
					)
				),
				'Should drop column \'one\''
			),
			'object array' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'one',
				array(
					(object) array(
						'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
					),
					(object) array(
						'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10,
					),
					(object) array(
						'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15,
					),
					(object) array(
						'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20,
					)
				),
				'Should drop column \'one\''
			),
			'array with non existing column' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'seven',
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'Should not drop any column when target column does not exist'
			),
		);
	}

	/**
	 * Data provider for get column
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestGetColumn()
	{
		return array(
			'generic array' => array(
				array(
					array(
						1, 2, 3, 4, 5
					), array(
					6, 7, 8, 9, 10
				), array(
					11, 12, 13, 14, 15
				), array(
					16, 17, 18, 19, 20
				)
				),
				2,
				null,
				array(
					3, 8, 13, 18
				),
				'Should get column #2'
			),
			'generic array with key' => array(
				array(
					array(
						1, 2, 3, 4, 5
					), array(
					6, 7, 8, 9, 10
				), array(
					11, 12, 13, 14, 15
				), array(
					16, 17, 18, 19, 20
				)
				),
				2,
				0,
				array(
					1 => 3, 6 => 8, 11 => 13, 16 => 18
				),
				'Should get column #2 with column #0 as keys'
			),
			'associative array' => array(
				array(
					array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'four',
				null,
				array(
					4, 9, 14, 19
				),
				'Should get column \'four\''
			),
			'associative array with key' => array(
				array(
					array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'four',
				'one',
				array(
					1 => 4, 6 => 9, 11 => 14, 16 => 19
				),
				'Should get column \'four\' with keys from column \'one\''
			),
			'object array' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'four',
				null,
				array(
					4, 9, 14, 19
				),
				'Should get column \'four\''
			),
			'object array with key' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'four',
				'one',
				array(
					1 => 4, 6 => 9, 11 => 14, 16 => 19
				),
				'Should get column \'four\' with keys from column \'one\''
			),
			'object array with invalid key' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => array('array is invalid for key'), 'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'four',
				'one',
				array(
					1 => 4, 9, 11 => 14, 16 => 19
				),
				'Should get column \'four\' with keys from column \'one\' and invalid key should introduce an automatic index'
			),
			'object array with one missing key' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'two' => 7, 'three' => 8, 'four' => 9, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'four',
				'one',
				array(
					1 => 4, 9, 11 => 14, 16 => 19
				),
				'Should get column \'four\' with keys from column \'one\' and the missing key should introduce an automatic index'
			),
			'object array with one missing value' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'four',
				'one',
				array(
					1 => 4, 11 => 14, 16 => 19
				),
				'Should get column \'four\' with keys from column \'one\' and item with missing value should be skipped'
			),
			'object array with null value-col' => array(
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'five' => 10
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				null,
				'one',
				array(
					1 => (object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					6 => (object) array(
						'one' => 6, 'two' => 7, 'three' => 8, 'five' => 10
					),
					11 => (object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					16 => (object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'Should get whole objects with keys from column \'one\''
			),
			'object array with null value-col and key-col' => array(
				array(
					'a' => (object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					'b' => (object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					'c' => (object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				null,
				null,
				array(
					(object) array(
						'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5
					),
					(object) array(
						'one' => 11, 'two' => 12, 'three' => 13, 'four' => 14, 'five' => 15
					),
					(object) array(
						'one' => 16, 'two' => 17, 'three' => 18, 'four' => 19, 'five' => 20
					)
				),
				'Should get whole objects with automatic indexes'
			),
		);
	}

	/**
	 * Data provider for get value
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestGetValue()
	{
		$input = array(
			'one' => 1,
			'two' => 2,
			'three' => 3,
			'four' => 4,
			'five' => 5,
			'six' => 6,
			'seven' => 7,
			'eight' => 8,
			'nine' => 'It\'s nine',
			'ten' => 10,
			'eleven' => 11,
			'twelve' => 12,
			'thirteen' => 13,
			'fourteen' => 14,
			'fifteen' => 15,
			'sixteen' => 16,
			'seventeen' => 17,
			'eightteen' => 'eighteen ninety-five',
			'nineteen' => 19,
			'twenty' => 20,
			'level' => array(
				'a' => 'Level 2 A',
				'b' => 'Level 2 B',
			),
			'level.b' => 'Index with dot',
		);

		return array(
			'defaults' => array(
				$input, 'five', null, null, 5, 'Should get 5', true
			),
			'get non-value' => array(
				$input, 'fiveio', 198, null, 198, 'Should get the default value', false
			),
			'get int 5' => array(
				$input, 'five', 198, 'int', (int) 5, 'Should get an int', false
			),
			'get float six' => array(
				$input, 'six', 198, 'float', (float) 6, 'Should get a float', false
			),
			'get get boolean seven' => array(
				$input, 'seven', 198, 'bool', (bool) 7, 'Should get a boolean', false
			),
			'get array eight' => array(
				$input, 'eight', 198, 'array', array(
					8
				), 'Should get an array', false
			),
			'get string nine' => array(
				$input, 'nine', 198, 'string', 'It\'s nine', 'Should get string', false
			),
			'get word' => array(
				$input, 'eightteen', 198, 'word', 'eighteenninetyfive', 'Should get it as a single word', false
			),
			'get level 2' => array(
				$input, 'level.a', 'default level a', 'string', 'Level 2 A', 'Should get the value from 2nd level', false
			),
			'get level 1 skip level 2' => array(
				$input, 'level.b', 'default level b', 'string', 'Index with dot', 'Should get the value from 1st level if exists ignoring 2nd', false
			),
			'get default if path invalid' => array(
				$input, 'level.c', 'default level c', 'string', 'default level c', 'Should get the default value if index or path not found', false
			),
		);
	}

	/**
	 * Data provider for invert
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestInvert()
	{
		return array(
			'Case 1' => array(
				// Input
				array(
					'New' => array('1000', '1500', '1750'),
					'Used' => array('3000', '4000', '5000', '6000')
				),
				// Expected
				array(
					'1000' => 'New',
					'1500' => 'New',
					'1750' => 'New',
					'3000' => 'Used',
					'4000' => 'Used',
					'5000' => 'Used',
					'6000' => 'Used'
				)
			),
			'Case 2' => array(
				// Input
				array(
					'New' => array(1000, 1500, 1750),
					'Used' => array(2750, 3000, 4000, 5000, 6000),
					'Refurbished' => array(2000, 2500),
					'Unspecified' => array()
				),
				// Expected
				array(
					'1000' => 'New',
					'1500' => 'New',
					'1750' => 'New',
					'2750' => 'Used',
					'3000' => 'Used',
					'4000' => 'Used',
					'5000' => 'Used',
					'6000' => 'Used',
					'2000' => 'Refurbished',
					'2500' => 'Refurbished'
				)
			),
			'Case 3' => array(
				// Input
				array(
					'New' => array(1000, 1500, 1750),
					'valueNotAnArray' => 2750,
					'withNonScalarValue' => array(2000, array(1000 , 3000))
				),
				// Expected
				array(
					'1000' => 'New',
					'1500' => 'New',
					'1750' => 'New',
					'2000' => 'withNonScalarValue'
				)
			)
		);
	}

	/**
	 * Data provider for testPivot
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestPivot()
	{
		return array(
			'A scalar array' => array(
				// Source
				array(
					1 => 'a',
					2 => 'b',
					3 => 'b',
					4 => 'c',
					5 => 'a',
					6 => 'a',
				),
				// Key
				null,
				// Expected
				array(
					'a' => array(
						1, 5, 6
					),
					'b' => array(
						2, 3
					),
					'c' => 4,
				)
			),
			'An array of associative arrays' => array(
				// Source
				array(
					1 => array('id' => 41, 'title' => 'boo'),
					2 => array('id' => 42, 'title' => 'boo'),
					3 => array('title' => 'boo'),
					4 => array('id' => 42, 'title' => 'boo'),
					5 => array('id' => 43, 'title' => 'boo'),
				),
				// Key
				'id',
				// Expected
				array(
					41 => array('id' => 41, 'title' => 'boo'),
					42 => array(
						array('id' => 42, 'title' => 'boo'),
						array('id' => 42, 'title' => 'boo'),
					),
					43 => array('id' => 43, 'title' => 'boo'),
				)
			),
			'An array of objects' => array(
				// Source
				array(
					1 => (object) array('id' => 41, 'title' => 'boo'),
					2 => (object) array('id' => 42, 'title' => 'boo'),
					3 => (object) array('title' => 'boo'),
					4 => (object) array('id' => 42, 'title' => 'boo'),
					5 => (object) array('id' => 43, 'title' => 'boo'),
				),
				// Key
				'id',
				// Expected
				array(
					41 => (object) array('id' => 41, 'title' => 'boo'),
					42 => array(
						(object) array('id' => 42, 'title' => 'boo'),
						(object) array('id' => 42, 'title' => 'boo'),
					),
					43 => (object) array('id' => 43, 'title' => 'boo'),
				)
			),
		);
	}

	/**
	 * Data provider for sorting objects
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestSortObject()
	{
		$input1 = array(
			(object) array(
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
			),
			(object) array(
				'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
			),
			(object) array(
				'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
			),
			(object) array(
				'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
			),
			(object) array(
				'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String'
			),
			(object) array(
				'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
			),
			(object) array(
				'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String'
			),
			(object) array(
				'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
			),
		);
		$input2 = array(
			(object) array(
				'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
			),
			(object) array(
				'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
			),
			(object) array(
				'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
			),
			(object) array(
				'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
			),
			(object) array(
				'integer' => 5, 'float' => 1.29999, 'string' => 't Test String'
			),
			(object) array(
				'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
			),
			(object) array(
				'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String'
			),
			(object) array(
				'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
			),
		);

		if (substr(php_uname(), 0, 6) != 'Darwin')
		{
			$input3 = array(
				(object) array(
					'string' => 'A Test String', 'integer' => 1,
				),
				(object) array(
					'string' => 'é Test String', 'integer' => 2,
				),
				(object) array(
					'string' => 'è Test String', 'integer' => 3,
				),
				(object) array(
					'string' => 'É Test String', 'integer' => 4,
				),
				(object) array(
					'string' => 'È Test String', 'integer' => 5,
				),
				(object) array(
					'string' => 'Œ Test String', 'integer' => 6,
				),
				(object) array(
					'string' => 'œ Test String', 'integer' => 7,
				),
				(object) array(
					'string' => 'L Test String', 'integer' => 8,
				),
				(object) array(
					'string' => 'P Test String', 'integer' => 9,
				),
				(object) array(
					'string' => 'p Test String', 'integer' => 10,
				),
			);
		}
		else
		{
			$input3 = array();
		}

		return array(
			'by int defaults' => array(
				$input1,
				'integer',
				null,
				false,
				false,
				array(
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
				),
				'Should be sorted by the integer field in ascending order',
				true
			),
			'by int ascending' => array(
				$input1,
				'integer',
				1,
				false,
				false,
				array(
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
				),
				'Should be sorted by the integer field in ascending order full argument list',
				false
			),
			'by int descending' => array(
				$input1,
				'integer',
				-1,
				false,
				false,
				array(
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String'
					),
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
				),
				'Should be sorted by the integer field in descending order',
				false
			),
			'by string ascending' => array(
				$input1,
				'string',
				1,
				false,
				false,
				array(
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String'
					),
				),
				'Should be sorted by the string field in ascending order full argument list',
				false,
				array(1, 2)
			),
			'by string descending' => array(
				$input1,
				'string',
				-1,
				false,
				false,
				array(
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 'T Test String'
					),
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'G Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				'Should be sorted by the string field in descending order',
				false,
				array(5, 6)
			),
			'by casesensitive string ascending' => array(
				$input2,
				'string',
				1,
				true,
				false,
				array(
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String'
					),
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 't Test String'
					),
				),
				'Should be sorted by the string field in ascending order with casesensitive comparisons',
				false,
				array(1, 2)
			),
			'by casesensitive string descending' => array(
				$input2,
				'string',
				-1,
				true,
				false,
				array(
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 't Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String'
					),
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				'Should be sorted by the string field in descending order with casesensitive comparisons',
				false,
				array(5, 6)
			),
			'by casesensitive string,integer ascending' => array(
				$input2,
				array(
					'string', 'integer'
				),
				1,
				true,
				false,
				array(
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String'
					),
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 't Test String'
					),
				),
				'Should be sorted by the string,integer field in descending order with casesensitive comparisons',
				false
			),
			'by casesensitive string,integer descending' => array(
				$input2,
				array(
					'string', 'integer'
				),
				-1,
				true,
				false,
				array(
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 't Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String'
					),
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				'Should be sorted by the string,integer field in descending order with casesensitive comparisons',
				false
			),
			'by casesensitive string,integer ascending,descending' => array(
				$input2,
				array(
					'string', 'integer'
				),
				array(
					1, -1
				),
				true,
				false,
				array(
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String'
					),
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 't Test String'
					),
				),
				'Should be sorted by the string,integer field in ascending,descending order with casesensitive comparisons',
				false
			),
			'by casesensitive string,integer descending,ascending' => array(
				$input2,
				array(
					'string', 'integer'
				),
				array(
					-1, 1
				),
				true,
				false,
				array(
					(object) array(
						'integer' => 5, 'float' => 1.29999, 'string' => 't Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'g Test String'
					),
					(object) array(
						'integer' => 1, 'float' => 1.29999, 'string' => 'N Test String'
					),
					(object) array(
						'integer' => 6, 'float' => 1.29999, 'string' => 'L Test String'
					),
					(object) array(
						'integer' => 22, 'float' => 1.29999, 'string' => 'E Test String'
					),
					(object) array(
						'integer' => 15, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 35, 'float' => 1.29999, 'string' => 'C Test String'
					),
					(object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				'Should be sorted by the string,integer field in descending,ascending order with casesensitive comparisons',
				false
			),
			'by casesensitive string ascending' => array(
				$input3,
				'string',
				1,
				true,
				array(
					'fr_FR.utf8', 'fr_FR.UTF-8', 'fr_FR.UTF-8@euro', 'French_Standard', 'french', 'fr_FR', 'fre_FR'
				),
				array(
					(object) array(
						'string' => 'A Test String', 'integer' => 1,
					),
					(object) array(
						'string' => 'é Test String', 'integer' => 2,
					),
					(object) array(
						'string' => 'É Test String', 'integer' => 4,
					),
					(object) array(
						'string' => 'è Test String', 'integer' => 3,
					),
					(object) array(
						'string' => 'È Test String', 'integer' => 5,
					),
					(object) array(
						'string' => 'L Test String', 'integer' => 8,
					),
					(object) array(
						'string' => 'œ Test String', 'integer' => 7,
					),
					(object) array(
						'string' => 'Œ Test String', 'integer' => 6,
					),
					(object) array(
						'string' => 'p Test String', 'integer' => 10,
					),
					(object) array(
						'string' => 'P Test String', 'integer' => 9,
					),
				),
				'Should be sorted by the string field in ascending order with casesensitive comparisons and fr_FR locale',
				false
			),
			'by caseinsensitive string, integer ascending' => array(
				$input3,
				array(
					'string', 'integer'
				),
				1,
				false,
				array(
					'fr_FR.utf8', 'fr_FR.UTF-8', 'fr_FR.UTF-8@euro', 'French_Standard', 'french', 'fr_FR', 'fre_FR'
				),
				array(
					(object) array(
						'string' => 'A Test String', 'integer' => 1,
					),
					(object) array(
						'string' => 'é Test String', 'integer' => 2,
					),
					(object) array(
						'string' => 'É Test String', 'integer' => 4,
					),
					(object) array(
						'string' => 'è Test String', 'integer' => 3,
					),
					(object) array(
						'string' => 'È Test String', 'integer' => 5,
					),
					(object) array(
						'string' => 'L Test String', 'integer' => 8,
					),
					(object) array(
						'string' => 'Œ Test String', 'integer' => 6,
					),
					(object) array(
						'string' => 'œ Test String', 'integer' => 7,
					),
					(object) array(
						'string' => 'P Test String', 'integer' => 9,
					),
					(object) array(
						'string' => 'p Test String', 'integer' => 10,
					),
				),
				'Should be sorted by the string,integer field in ascending order with caseinsensitive comparisons and fr_FR locale',
				false
			),
		);
	}

	/**
	 * Data provider for numeric inputs
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestToInteger()
	{
		return array(
			'floating with single argument' => array(
				array(
					0.9, 3.2, 4.9999999, 7.5
				), null, array(
					0, 3, 4, 7
				), 'Should truncate numbers in array'
			),
			'floating with default array' => array(
				array(
					0.9, 3.2, 4.9999999, 7.5
				), array(
					1, 2, 3
				), array(
					0, 3, 4, 7
				), 'Supplied default should not be used'
			),
			'non-array with single argument' => array(
				12, null, array(), 'Should replace non-array input with empty array'
			),
			'non-array with default array' => array(
				12, array(
					1.5, 2.6, 3
				), array(
					1, 2, 3
				), 'Should replace non-array input with array of truncated numbers'
			),
			'non-array with default single' => array(
				12, 3.5, array(
					3
				), 'Should replace non-array with single-element array of truncated number'
			),
		);
	}

	/**
	 * Data provider for object inputs
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestToObject()
	{
		return array(
			'single object' => array(
				array(
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
				),
				null,
				(object) array(
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
				),
				'Should turn array into single object'
			),
			'multiple objects' => array(
				array(
					'first' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'second' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'third' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				null,
				(object) array(
					'first' => (object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'second' => (object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'third' => (object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				'Should turn multiple dimension array into nested objects'
			),
			'single object with class' => array(
				array(
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
				),
				'stdClass',
				(object) array(
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
				),
				'Should turn array into single object'
			),
			'multiple objects with class' => array(
				array(
					'first' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'second' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'third' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				'stdClass',
				(object) array(
					'first' => (object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'second' => (object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'third' => (object) array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				'Should turn multiple dimension array into nested objects'
			),
		);
	}

	/**
	 * Data provider for string inputs
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function seedTestToString()
	{
		return array(
			'single dimension 1' => array(
				array(
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
				),
				null,
				null,
				false,
				'integer="12" float="1.29999" string="A Test String"',
				'Should turn array into single string with defaults',
				true
			),
			'single dimension 2' => array(
				array(
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
				),
				" = ",
				null,
				true,
				'integer = "12"float = "1.29999"string = "A Test String"',
				'Should turn array into single string with " = " and no spaces',
				false
			),
			'single dimension 3' => array(
				array(
					'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
				),
				' = ',
				' then ',
				true,
				'integer = "12" then float = "1.29999" then string = "A Test String"',
				'Should turn array into single string with " = " and then between elements',
				false
			),
			'multiple dimensions 1' => array(
				array(
					'first' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'second' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'third' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				null,
				null,
				false,
				'integer="12" float="1.29999" string="A Test String" ' . 'integer="12" float="1.29999" string="A Test String" '
					. 'integer="12" float="1.29999" string="A Test String"',
				'Should turn multiple dimension array into single string',
				true
			),
			'multiple dimensions 2' => array(
				array(
					'first' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'second' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'third' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				' = ',
				null,
				false,
				'integer = "12"float = "1.29999"string = "A Test String"' . 'integer = "12"float = "1.29999"string = "A Test String"'
					. 'integer = "12"float = "1.29999"string = "A Test String"',
				'Should turn multiple dimension array into single string with " = " and no spaces',
				false
			),
			'multiple dimensions 3' => array(
				array(
					'first' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'second' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'third' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				' = ',
				' ',
				false,
				'integer = "12" float = "1.29999" string = "A Test String" ' . 'integer = "12" float = "1.29999" string = "A Test String" '
					. 'integer = "12" float = "1.29999" string = "A Test String"',
				'Should turn multiple dimension array into single string with " = " and a space',
				false
			),
			'multiple dimensions 4' => array(
				array(
					'first' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'second' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
					'third' => array(
						'integer' => 12, 'float' => 1.29999, 'string' => 'A Test String'
					),
				),
				' = ',
				null,
				true,
				'firstinteger = "12"float = "1.29999"string = "A Test String"' . 'secondinteger = "12"float = "1.29999"string = "A Test String"'
					. 'thirdinteger = "12"float = "1.29999"string = "A Test String"',
				'Should turn multiple dimension array into single string with " = " and no spaces with outer key',
				false
			),
		);
	}

	/**
	 * Tests the ArrayHelper::arrayUnique method.
	 *
	 * @param   array   $input     The array being input.
	 * @param   string  $expected  The expected return value.
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestArrayUnique
	 * @covers        Joomla\Utilities\ArrayHelper::arrayUnique
	 * @since         1.0
	 */
	public function testArrayUnique($input, $expected)
	{
		$this->assertThat(
			ArrayHelper::arrayUnique($input),
			$this->equalTo($expected)
		);
	}

	/**
	 * Tests conversion of object to string.
	 *
	 * @param   array    $input     The array being input
	 * @param   boolean  $recurse   Recurse through multiple dimensions?
	 * @param   string   $regex     Regex to select only some attributes
	 * @param   string   $expect    The expected return value
	 * @param   boolean  $defaults  Use function defaults (true) or full argument list
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestFromObject
	 * @covers        Joomla\Utilities\ArrayHelper::fromObject
	 * @covers        Joomla\Utilities\ArrayHelper::arrayFromObject
	 * @since         1.0
	 */
	public function testFromObject($input, $recurse, $regex, $expect, $defaults)
	{
		if ($defaults)
		{
			$output = ArrayHelper::fromObject($input);
		}
		else
		{
			$output = ArrayHelper::fromObject($input, $recurse, $regex);
		}

		$this->assertEquals($expect, $output);
	}

	/**
	 * Test adding a column from an array (by index or association).
	 *
	 * @param   array   $input    The source array
	 * @param   array   $column   The array to be used as new column
	 * @param   string  $colName  The index of the new column or name of the new object property
	 * @param   string  $keyCol   The index of the column or name of object property to be used for mapping with the new column
	 * @param   array   $expect   The expected results
	 * @param   string  $message  The failure message
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestAddColumn
	 * @covers        Joomla\Utilities\ArrayHelper::addColumn
	 * @since         1.5.0
	 */
	public function testAddColumn($input, $column, $colName, $keyCol, $expect, $message)
	{
		$this->assertEquals($expect, ArrayHelper::addColumn($input, $column, $colName, $keyCol), $message);
	}

	/**
	 * Test removing a column from an array (by index or association).
	 *
	 * @param   array   $input    The source array
	 * @param   string  $colName  The index of the new column or name of the new object property
	 * @param   array   $expect   The expected results
	 * @param   string  $message  The failure message
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestDropColumn
	 * @covers        Joomla\Utilities\ArrayHelper::dropColumn
	 * @since         1.5.0
	 */
	public function testDropColumn($input, $colName, $expect, $message)
	{
		$this->assertEquals($expect, ArrayHelper::dropColumn($input, $colName), $message);
	}

	/**
	 * Test pulling data from a single column (by index or association).
	 *
	 * @param   array   $input     Input array
	 * @param   string  $valueCol  The index of the column or name of object property to be used as value
	 * @param   string  $keyCol    The index of the column or name of object property to be used as key
	 * @param   array   $expect    The expected results
	 * @param   string  $message   The failure message
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestGetColumn
	 * @covers        Joomla\Utilities\ArrayHelper::getColumn
	 * @since         1.0
	 */
	public function testGetColumn($input, $valueCol, $keyCol, $expect, $message)
	{
		$this->assertEquals($expect, ArrayHelper::getColumn($input, $valueCol, $keyCol), $message);
	}

	/**
	 * Test get value from an array.
	 *
	 * @param   array   $input     Input array
	 * @param   mixed   $index     Element to pull, either by association or number
	 * @param   mixed   $default   The defualt value, if element not present
	 * @param   string  $type      The type of value returned
	 * @param   array   $expect    The expected results
	 * @param   string  $message   The failure message
	 * @param   bool    $defaults  Use the defaults (true) or full argument list
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestGetValue
	 * @covers        Joomla\Utilities\ArrayHelper::getValue
	 * @since         1.0
	 */
	public function testGetValue($input, $index, $default, $type, $expect, $message, $defaults)
	{
		if ($defaults)
		{
			$output = ArrayHelper::getValue($input, $index);
		}
		else
		{
			$output = ArrayHelper::getValue($input, $index, $default, $type);
		}

		$this->assertEquals($expect, $output, $message);
	}

	/**
	 * Test get value from an array.
	 *
	 * @return  void
	 *
	 * @covers        Joomla\Utilities\ArrayHelper::getValue
	 * @since         1.3.1
	 */
	public function testGetValueWithObjectImplementingArrayAccess()
	{
		$array = array(
			'name' => 'Joe',
			'surname' => 'Blogs',
			'age' => 20,
			'address' => null,
		);

		$arrayObject = new ArrayObject($array);

		$this->assertEquals('Joe', ArrayHelper::getValue($arrayObject, 'name'), 'An object implementing \ArrayAccess should succesfully retrieve the value of an object');
	}

	/**
	 * @testdox  Verify that getValue() throws an \InvalidArgumentException when an object is given that doesn't implement \ArrayAccess
	 *
	 * @covers             Joomla\Utilities\ArrayHelper::getValue
	 * @expectedException  \InvalidArgumentException
	 * @since              1.3.1
	 */
	public function testInvalidArgumentExceptionWithAnObjectNotImplementingArrayAccess()
	{
		$object = new \stdClass;
		$object->name = "Joe";
		$object->surname = "Blogs";
		$object->age = 20;
		$object->address = null;

		ArrayHelper::getValue($object, 'string');
	}

	/**
	 * Tests the ArrayHelper::invert method.
	 *
	 * @param   array   $input     The array being input.
	 * @param   string  $expected  The expected return value.
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestInvert
	 * @since         1.0
	 */
	public function testInvert($input, $expected)
	{
		$this->assertThat(
			ArrayHelper::invert($input),
			$this->equalTo($expected)
		);
	}

	/**
	 * Test the ArrayHelper::isAssociate method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @sovers  ArrayHelper::isAssociative
	 */
	public function testIsAssociative()
	{
		$this->assertThat(
			ArrayHelper::isAssociative(
				array(
					1, 2, 3
				)
			),
			$this->isFalse(),
			'Line: ' . __LINE__ . ' This array should not be associative.'
		);

		$this->assertThat(
			ArrayHelper::isAssociative(
				array(
					'a' => 1, 'b' => 2, 'c' => 3
				)
			),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' This array should be associative.'
		);

		$this->assertThat(
			ArrayHelper::isAssociative(
				array(
					'a' => 1, 2, 'c' => 3
				)
			),
			$this->isTrue(),
			'Line: ' . __LINE__ . ' This array should be associative.'
		);
	}

	/**
	 * Tests the ArrayHelper::pivot method.
	 *
	 * @param   array   $source    The source array.
	 * @param   string  $key       Where the elements of the source array are objects or arrays, the key to pivot on.
	 * @param   array   $expected  The expected result.
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestPivot
	 * @covers        Joomla\Utilities\ArrayHelper::pivot
	 * @since         1.0
	 */
	public function testPivot($source, $key, $expected)
	{
		$this->assertThat(
			ArrayHelper::pivot($source, $key),
			$this->equalTo($expected)
		);
	}

	/**
	 * Test sorting an array of objects.
	 *
	 * @param   array    $input          Input array of objects
	 * @param   mixed    $key            Key to sort on
	 * @param   mixed    $direction      Ascending (1) or Descending(-1)
	 * @param   string   $casesensitive  @todo
	 * @param   string   $locale         @todo
	 * @param   array    $expect         The expected results
	 * @param   string   $message        The failure message
	 * @param   boolean  $defaults       Use the defaults (true) or full argument list
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestSortObject
	 * @covers        Joomla\Utilities\ArrayHelper::sortObjects
	 * @since         1.0
	 */
	public function testSortObjects($input, $key, $direction, $casesensitive, $locale, $expect, $message, $defaults, $swappable_keys = array())
	{
		// Convert the $locale param to a string if it is an array
		if (\is_array($locale))
		{
			$locale = "'" . implode("', '", $locale) . "'";
		}

		if (empty($input))
		{
			$this->markTestSkipped('Skip for MAC until PHP sort bug is fixed');

			return;
		}
		elseif ($locale != false && !setlocale(LC_COLLATE, $locale))
		{
			// If the locale is not available, we can't have to transcode the string and can't reliably compare it.
			$this->markTestSkipped("Locale {$locale} is not available.");

			return;
		}

		if ($defaults)
		{
			$output = ArrayHelper::sortObjects($input, $key);
		}
		else
		{
			$output = ArrayHelper::sortObjects($input, $key, $direction, $casesensitive, $locale);
		}

		// The ordering of elements that compare equal according to
		// $key is undefined (implementation dependent).
		if ($expect != $output && $swappable_keys) {
			list($k1, $k2) = $swappable_keys;
			$e1 = $output[$k1];
			$e2 = $output[$k2];
			$output[$k1] = $e2;
			$output[$k2] = $e1;
		}

		$this->assertEquals($expect, $output, $message);
	}

	/**
	 * Test convert an array to all integers.
	 *
	 * @param   string  $input    The array being input
	 * @param   string  $default  The default value
	 * @param   string  $expect   The expected return value
	 * @param   string  $message  The failure message
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestToInteger
	 * @covers        Joomla\Utilities\ArrayHelper::toInteger
	 * @since         1.0
	 */
	public function testToInteger($input, $default, $expect, $message)
	{
		$result = ArrayHelper::toInteger($input, $default);
		$this->assertEquals(
			$expect,
			$result,
			$message
		);
	}

	/**
	 * Test convert array to object.
	 *
	 * @param   string  $input      The array being input
	 * @param   string  $className  The class name to build
	 * @param   string  $expect     The expected return value
	 * @param   string  $message    The failure message
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestToObject
	 * @covers        Joomla\Utilities\ArrayHelper::toObject
	 * @since         1.0
	 */
	public function testToObject($input, $className, $expect, $message)
	{
		$this->assertEquals(
			$expect,
			ArrayHelper::toObject($input),
			$message
		);
	}

	/**
	 * Tests converting array to string.
	 *
	 * @param   array    $input     The array being input
	 * @param   string   $inner     The inner glue
	 * @param   string   $outer     The outer glue
	 * @param   boolean  $keepKey   Keep the outer key
	 * @param   string   $expect    The expected return value
	 * @param   string   $message   The failure message
	 * @param   boolean  $defaults  Use function defaults (true) or full argument list
	 *
	 * @return  void
	 *
	 * @dataProvider  seedTestToString
	 * @covers        Joomla\Utilities\ArrayHelper::toString
	 * @since         1.0
	 */
	public function testToString($input, $inner, $outer, $keepKey, $expect, $message, $defaults)
	{
		if ($defaults)
		{
			$output = ArrayHelper::toString($input);
		}
		else
		{
			$output = ArrayHelper::toString($input, $inner, $outer, $keepKey);
		}

		$this->assertEquals($expect, $output, $message);
	}

	/**
	 * Tests the arraySearch method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Utilities\ArrayHelper::arraySearch
	 * @since   1.0
	 */
	public function testArraySearch()
	{
		$array = array(
			'name' => 'Foo',
			'email' => 'foobar@example.com'
		);

		// Search case sensitive.
		$this->assertEquals('name', ArrayHelper::arraySearch('Foo', $array));

		// Search case insenitive.
		$this->assertEquals('email', ArrayHelper::arraySearch('FOOBAR', $array, false));

		// Search non existent value.
		$this->assertEquals(false, ArrayHelper::arraySearch('barfoo', $array));
	}

	/**
	 * testFlatten
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Utilities\ArrayHelper::flatten
	 * @since   1.0
	 */
	public function testFlatten()
	{
		$array = array(
			'flower' => 'sakura',
			'olive' => 'peace',
			'pos1' => array(
				'sunflower' => 'love'
			),
			'pos2' => array(
				'cornflower' => 'elegant'
			)
		);

		$flatted = ArrayHelper::flatten($array);

		$this->assertEquals($flatted['pos1.sunflower'], 'love');

		$flatted = ArrayHelper::flatten($array, '/');

		$this->assertEquals($flatted['pos1/sunflower'], 'love');
	}
}
