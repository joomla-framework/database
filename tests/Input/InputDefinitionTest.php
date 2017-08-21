<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Input;

use Joomla\Console\Input\InputDefinition;
use Joomla\Console\Input\InputOption;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Console\Input\InputDefinition
 */
class InputDefinitionTest extends TestCase
{
	/**
	 * @covers  Joomla\Console\Input\InputDefinition::__construct
	 * @uses    Joomla\Console\Input\InputDefinition::hasOption
	 * @uses    Joomla\Console\Input\InputDefinition::setDefinition
	 */
	public function testTheDefinitionIsCreatedWithoutAnOption()
	{
		$this->assertEmpty((new InputDefinition)->getOptions());
	}

	/**
	 * @covers  Joomla\Console\Input\InputDefinition::__construct
	 * @uses    Joomla\Console\Input\InputDefinition::hasOption
	 * @uses    Joomla\Console\Input\InputDefinition::setDefinition
	 */
	public function testTheDefinitionIsCreatedWithAnOption()
	{
		$option = new InputOption('test', 't', InputOption::OPTIONAL, '', null);

		$definition = new InputDefinition([$option]);

		$this->assertTrue($definition->hasOption($option->getName()));
	}

	/**
	 * @covers  Joomla\Console\Input\InputDefinition::addOption
	 * @uses    Joomla\Console\Input\InputDefinition::hasOption
	 */
	public function testAnOptionIsAdded()
	{
		$option = new InputOption('test', 't', InputOption::OPTIONAL, '', null);

		$definition = new InputDefinition;
		$definition->addOption($option);

		$this->assertTrue($definition->hasOption($option->getName()));
	}

	/**
	 * @covers  Joomla\Console\Input\InputDefinition::addOption
	 * @uses    Joomla\Console\Input\InputDefinition::hasOption
	 *
	 * @expectedException  LogicException
	 * @expectedExceptionMessage  An option named "test" already exists.
	 */
	public function testAnOptionIsNotAddedWhenASimilarOptionExists()
	{
		$firstOption  = new InputOption('test', 't', InputOption::OPTIONAL, '', null);
		$secondOption = new InputOption('test', 't', InputOption::REQUIRED, '', null);

		$definition = new InputDefinition;
		$definition->addOption($firstOption);
		$definition->addOption($secondOption);
	}

	/**
	 * @covers  Joomla\Console\Input\InputDefinition::hasShortcut
	 * @uses    Joomla\Console\Input\InputDefinition::addOption
	 */
	public function testAnOptionWithShortcutsIsAdded()
	{
		$option = new InputOption('test', 't', InputOption::OPTIONAL, '', null);

		$definition = new InputDefinition;
		$definition->addOption($option);

		$this->assertTrue($definition->hasShortcut('t'));
	}

	/**
	 * @covers  Joomla\Console\Input\InputDefinition::getOptionForShortcut
	 * @uses    Joomla\Console\Input\InputDefinition::addOption
	 */
	public function testAnOptionIsRetrievedByShortcut()
	{
		$option = new InputOption('test', 't', InputOption::OPTIONAL, '', null);

		$definition = new InputDefinition;
		$definition->addOption($option);

		$this->assertSame($option, $definition->getOptionForShortcut('t'));
	}
}
