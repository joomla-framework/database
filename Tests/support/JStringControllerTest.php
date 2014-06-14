<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Filesystem\Support\StringController;
use Joomla\Test\TestHelper;

/**
 * Test class for StringController.
 *
 * @since  1.0
 */
class StringControllerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test...
	 *
	 * @todo Implement test_getArray().
	 *
	 * @return void
	 */
	public function test_getArray()
	{
		$strings = array('foo' => 'bar');

		TestHelper::setValue(new StringController, 'strings', $strings);

		$this->assertEquals(
			$strings,
			StringController::_getArray()
		);

		// Clean up static variable
		TestHelper::setValue(new StringController, 'strings', array());
	}

	/**
	 * Test...
	 *
	 * @todo Implement testCreateRef().
	 *
	 * @return void
	 */
	public function testCreateRef()
	{
		$string = "foo";

		StringController::createRef('bar', $string);

		$strings = StringController::_getArray();

		$this->assertEquals(
			$string,
			$strings['bar']
		);

		// Clean up static variable
		TestHelper::setValue(new StringController, 'strings', array());
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGetRef().
	 *
	 * @return void
	 */
	public function testGetRef()
	{
		$string = "foo";
		StringController::createRef('bar', $string);

		$this->assertEquals(
			$string,
			StringController::getRef('bar')
		);

		$this->assertEquals(
			false,
			StringController::getRef('foo')
		);
	}
}
