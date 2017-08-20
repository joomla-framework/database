<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\IO;

use Joomla\Console\IO\ColorProcessor;
use Joomla\Console\IO\ColorStyle;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Console\IO\ColorProcessor
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
		$this->winOs  = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
		$this->object = new ColorProcessor($this->winOs);
	}

	/**
	 * @covers  Joomla\Console\IO\ColorProcessor::addStyle
	 */
	public function testAStyleCanBeAdded()
	{
		$style = new ColorStyle('red');
		$this->object->addStyle('foo', $style);

		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertEquals(
			$check,
			$this->object->process('<foo>foo</foo>')
		);
	}

	/**
	 * @covers  Joomla\Console\IO\ColorProcessor::stripColors
	 */
	public function testColorStylesAreStripped()
	{
		$this->assertEquals(
			'foo',
			ColorProcessor::stripColors('<foo>foo</foo>')
		);
	}

	/**
	 * @covers  Joomla\Console\IO\ColorProcessor::process
	 * @covers  Joomla\Console\IO\ColorProcessor::replaceColors
	 */
	public function testAStringIsCorrectlyProcessed()
	{
		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertEquals(
			$check,
			$this->object->process('<fg=red>foo</fg=red>')
		);
	}

	/**
	 * @covers  Joomla\Console\IO\ColorProcessor::process
	 * @covers  Joomla\Console\IO\ColorProcessor::replaceColors
	 */
	public function testAStringReferencingNamedStylesIsCorrectlyProcessed()
	{
		$style = new ColorStyle('red');
		$this->object->addStyle('foo', $style);

		$check = $this->winOs ? 'foo' : '[31mfoo[0m';

		$this->assertEquals(
			$check,
			$this->object->process('<foo>foo</foo>')
		);
	}
}
