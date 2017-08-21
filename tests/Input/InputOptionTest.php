<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Input;

use Joomla\Console\Input\InputOption;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Console\Input\InputOption
 */
class InputOptionTest extends TestCase
{
	/**
	 * @covers  Joomla\Console\Input\InputOption::__construct
	 * @uses    Joomla\Console\Input\InputOption::setDefault
	 */
	public function testAnOptionalOptionIsCreated()
	{
		$option = new InputOption('test', 't', InputOption::OPTIONAL, '', null);

		$this->assertAttributeEquals('test', 'name', $option);
	}

	/**
	 * @covers  Joomla\Console\Input\InputOption::__construct
	 * @uses    Joomla\Console\Input\InputOption::setDefault
	 *
	 * @expectedException  LogicException
	 */
	public function testANoValueOptionWithADefaultValueIsNotCreated()
	{
		new InputOption('test', ['t'], InputOption::NONE, '', 'failure');
	}

	/**
	 * @covers  Joomla\Console\Input\InputOption::__construct
	 *
	 * @expectedException  InvalidArgumentException
	 * @expectedExceptionMessage  An option must have a name.
	 */
	public function testAnOptionWithoutANameIsNotCreated()
	{
		new InputOption('');
	}

	/**
	 * @covers  Joomla\Console\Input\InputOption::__construct
	 *
	 * @expectedException  InvalidArgumentException
	 * @expectedExceptionMessage  An option must have a name.
	 */
	public function testAnOptionWithAnInvalidShortcutIsNotCreated()
	{
		new InputOption('');
	}

	/**
	 * @covers  Joomla\Console\Input\InputOption::__construct
	 *
	 * @expectedException  InvalidArgumentException
	 * @expectedExceptionMessage  An option shortcut must be an array or string, "object" given.
	 */
	public function testAnOptionWithAnInvalidModeIsNotCreated()
	{
		new InputOption('test', new \stdClass);
	}

	/**
	 * @covers  Joomla\Console\Input\InputOption::acceptValue
	 * @uses    Joomla\Console\Input\InputOption::__construct
	 * @uses    Joomla\Console\Input\InputOption::setDefault
	 */
	public function testAnOptionalOptionAcceptsAValue()
	{
		$this->assertTrue(
			(new InputOption('test', 't', InputOption::OPTIONAL, '', null))->acceptValue()
		);
	}

	/**
	 * @covers  Joomla\Console\Input\InputOption::acceptValue
	 * @uses    Joomla\Console\Input\InputOption::__construct
	 * @uses    Joomla\Console\Input\InputOption::setDefault
	 */
	public function testANoValueOptionDoesNotAcceptAValue()
	{
		$this->assertFalse(
			(new InputOption('test', 't', InputOption::NONE, '', null))->acceptValue()
		);
	}

	/**
	 * @covers  Joomla\Console\Input\InputOption::setDefault
	 * @uses    Joomla\Console\Input\InputOption::__construct
	 */
	public function testAnOptionalOptionCanHaveADefaultValue()
	{
		$option = new InputOption('test', 't', InputOption::OPTIONAL, '', null);
		$option->setDefault('default');

		$this->assertAttributeEquals('default', 'default', $option);
	}

	/**
	 * @covers  Joomla\Console\Input\InputOption::setDefault
	 * @uses    Joomla\Console\Input\InputOption::__construct
	 *
	 * @expectedException  LogicException
	 */
	public function testANoValueOptionCanNotHaveADefaultValue()
	{
		$option = new InputOption('test', ['t'], InputOption::NONE, '', null);
		$option->setDefault('failure');
	}

	/**
	 * @covers  Joomla\Console\Input\InputOption::sameAs
	 * @uses    Joomla\Console\Input\InputOption::__construct
	 */
	public function testAnOptionIsComparedForUniqueness()
	{
		$option = new InputOption('test', 't', InputOption::OPTIONAL, '', null);
		$secondOption = clone $option;

		$this->assertTrue($option->sameAs($secondOption));

		$requiredOption = new InputOption('test', 't', InputOption::REQUIRED, '', null);

		$this->assertFalse($option->sameAs($requiredOption));
	}
}
