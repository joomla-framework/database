<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Tests;

use Joomla\Application\Cli\ColorProcessor;
use Joomla\Application\Cli\ColorStyle;

/**
 * Test class for Joomla\Application\Cli\ColorProcessor.
 */
class ColorProcessorTest extends CompatTestCase
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
	protected function doSetUp()
	{
		$this->object = new ColorProcessor;
		$this->winOs = PHP_OS_FAMILY === 'Windows';

		parent::doSetUp();
	}

	/**
	 * Tests the process method for adding a style
	 */
	public function testAddStyle()
	{
		$this->object->addStyle('foo', new ColorStyle('red'));

		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertSame(
			$check,
			$this->object->process('<foo>foo</foo>')
		);
	}

	/**
	 * Tests the stripColors method
	 */
	public function testStripColors()
	{
		$this->assertSame(
			'foo',
			ColorProcessor::stripColors('<foo>foo</foo>')
		);
	}

	/**
	 * Tests the process method for replacing colors
	 */
	public function testProcess()
	{
		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertSame(
			$check,
			$this->object->process('<fg=red>foo</fg=red>')
		);
	}

	/**
	 * Tests the process method for replacing colors
	 */
	public function testProcessNamed()
	{
		$this->object->addStyle('foo', new ColorStyle('red'));

		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertSame(
			$check,
			$this->object->process('<foo>foo</foo>')
		);
	}

	/**
	 * Tests the process method for replacing colors
	 */
	public function testProcessReplace()
	{
		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertSame(
			$check,
			$this->object->process('<fg=red>foo</fg=red>')
		);
	}
}
