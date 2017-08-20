<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests;

use Joomla\Console\Application;
use Joomla\Console\Tests\Fixtures\Command\TestAliasedCommand;
use Joomla\Console\Tests\Fixtures\Command\TestDisabledCommand;
use Joomla\Console\Tests\Fixtures\Command\TestNoAliasCommand;
use Joomla\Console\Tests\Fixtures\Command\TestUnnamedCommand;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Console\Application
 */
class ApplicationTest extends TestCase
{
	/**
	 * @var  Application
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$this->object = new Application;
	}

	/**
	 * @covers  Joomla\Console\Application::addCommand
	 * @uses    Joomla\Console\Application::hasCommand
	 */
	public function testACommandCanBeAddedWithoutAliases()
	{
		$command = new TestNoAliasCommand;

		$this->assertSame($command, $this->object->addCommand($command));
		$this->assertTrue($this->object->hasCommand($command->getName()));
	}

	/**
	 * @covers  Joomla\Console\Application::addCommand
	 * @uses    Joomla\Console\Application::hasCommand
	 */
	public function testACommandCanBeAddedWithAliases()
	{
		$command = new TestAliasedCommand;

		$this->assertSame($command, $this->object->addCommand($command));
		$this->assertTrue($this->object->hasCommand($command->getName()));

		foreach ($command->getAliases() as $alias)
		{
			$this->assertTrue($this->object->hasCommand($alias));
		}
	}

	/**
	 * @covers  Joomla\Console\Application::addCommand
	 * @uses    Joomla\Console\Application::hasCommand
	 */
	public function testADisabledCommandIsNotAdded()
	{
		$command = new TestDisabledCommand;

		$this->assertNull($this->object->addCommand($command));
		$this->assertFalse($this->object->hasCommand($command->getName()));
	}

	/**
	 * @covers  Joomla\Console\Application::addCommand
	 *
	 * @expectedException  LogicException
	 */
	public function testAnUnnamedCommandIsNotAdded()
	{
		$this->object->addCommand(new TestUnnamedCommand);
	}

	/**
	 * @covers  Joomla\Console\Application::hasCommand
	 * @uses    Joomla\Console\Application::addCommand
	 */
	public function testACommandIsReportedAsAvailable()
	{
		$availableCommand    = new TestNoAliasCommand;
		$notAvailableCommand = new TestDisabledCommand;

		$this->object->addCommand($availableCommand);
		$this->object->addCommand($notAvailableCommand);

		$this->assertTrue($this->object->hasCommand($availableCommand->getName()));
		$this->assertFalse($this->object->hasCommand($notAvailableCommand->getName()));
	}

	/**
	 * @covers  Joomla\Console\Application::getCommand
	 * @uses    Joomla\Console\Application::addCommand
	 */
	public function testACommandIsRetrieved()
	{
		$command = new TestNoAliasCommand;

		$this->object->addCommand($command);
		$this->assertSame($command, $this->object->getCommand($command->getName()));
	}
}
