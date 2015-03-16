<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Tests;

use Joomla\Application\Cli\ColorProcessor;
use Joomla\Application\Cli\ColorStyle;

/**
 * Test class.
 *
 * @since  1.0
 */
class ColorProcessorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Object under test
	 *
	 * @var    ColorProcessor
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Windows OS flag
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $winOs = false;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		$this->object = new ColorProcessor;
		$this->winOs = ((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));
	}

	/**
	 * Tests the process method for adding a style
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Application\Cli\ColorProcessor::addStyle
	 * @since   1.0
	 */
	public function testAddStyle()
	{
		$style = new ColorStyle('red');
		$this->object->addStyle('foo', $style);

		$check = ($this->winOs) ? 'foo' : '[31mfoo[0m';

		$this->assertThat(
			$this->object->process('<foo>foo</foo>'),
			$this->equalTo($check)
		);
	}

	/**
	 * Tests the stripColors method
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Application\Cli\ColorProcessor::stripColors
	 * @since   1.0
	 */
	public function testStripColors()
	{
		$this->assertThat(
			$this->object->stripColors('<foo>foo</foo>'),
			$this->equalTo('foo')
		);
	}

	/**
	 * Tests the process method for replacing colors
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Application\Cli\ColorProcessor::process
	 * @since   1.0
	 */
	public function testProcess()
	{
		$check = ($this->winOs) ? 'foo' : '[31mfoo[0m';

		$this->assertThat(
			$this->object->process('<fg=red>foo</fg=red>'),
			$this->equalTo($check)
		);
	}

	/**
	 * Tests the process method for replacing colors
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Application\Cli\ColorProcessor::process
	 * @since   1.0
	 */
	public function testProcessNamed()
	{
		$style = new ColorStyle('red');
		$this->object->addStyle('foo', $style);

		$check = ($this->winOs) ? 'foo' : '[31mfoo[0m';

		$this->assertThat(
			$this->object->process('<foo>foo</foo>'),
			$this->equalTo($check)
		);
	}

	/**
	 * Tests the process method for replacing colors
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Application\Cli\ColorProcessor::replaceColors
	 * @since   1.0
	 */
	public function testProcessReplace()
	{
		$check = ($this->winOs) ? 'foo' : '[31mfoo[0m';

		$this->assertThat(
			$this->object->process('<fg=red>foo</fg=red>'),
			$this->equalTo($check)
		);
	}
}
