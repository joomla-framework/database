<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Tests;

use Joomla\Application\Cli\ColorProcessor;
use Joomla\Application\Cli\ColorStyle;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 */
class ColorProcessorTest extends TestCase
{
	/**
	 * Object under test
	 *
	 * @var  ColorProcessor
	 */
	protected $object;

	/**
	 * Windows OS flag
	 *
	 * @var  boolean
	 */
	protected $winOs = false;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$this->object = new ColorProcessor;
		$this->winOs = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
	}

	/**
	 * Tests the process method for adding a style
	 */
	public function testAddStyle()
	{
		$style = new ColorStyle('red');
		$this->object->addStyle('foo', $style);

		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertThat(
			$this->object->process('<foo>foo</foo>'),
			$this->equalTo($check)
		);
	}

	/**
	 * Tests the stripColors method
	 */
	public function testStripColors()
	{
		$colorProcessor = $this->object;

		$this->assertThat(
			$colorProcessor::stripColors('<foo>foo</foo>'),
			$this->equalTo('foo')
		);
	}

	/**
	 * Tests the process method for replacing colors
	 */
	public function testProcess()
	{
		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertThat(
			$this->object->process('<fg=red>foo</fg=red>'),
			$this->equalTo($check)
		);
	}

	/**
	 * Tests the process method for replacing colors
	 */
	public function testProcessNamed()
	{
		$style = new ColorStyle('red');
		$this->object->addStyle('foo', $style);

		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertThat(
			$this->object->process('<foo>foo</foo>'),
			$this->equalTo($check)
		);
	}

	/**
	 * Tests the process method for replacing colors
	 */
	public function testProcessReplace()
	{
		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertThat(
			$this->object->process('<fg=red>foo</fg=red>'),
			$this->equalTo($check)
		);
	}
}
