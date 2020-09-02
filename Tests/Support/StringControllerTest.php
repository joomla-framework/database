<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests\Support;

use Joomla\Filesystem\Support\StringController;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class for StringController.
 *
 * @since  1.0
 */
class StringControllerTest extends TestCase
{
	/**
	 * Test _getArray method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
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
	 * Test createRef method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
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
	 * Test getRef method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
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
