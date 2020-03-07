<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Command;

use Joomla\Console\Application;
use Joomla\Console\Command\HelpCommand;
use Joomla\Console\Command\ListCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Console\Command\HelpCommand
 */
class HelpCommandTest extends TestCase
{
	/**
	 * @covers  Joomla\Console\Command\HelpCommand
	 * @uses    Joomla\Console\Application
	 * @uses    Joomla\Console\Command\AbstractCommand
	 * @uses    Joomla\Console\Command\ListCommand
	 * @uses    Joomla\Console\Descriptor\TextDescriptor
	 * @uses    Joomla\Console\Helper\DescriptorHelper
	 */
	public function testTheCommandIsExecutedWithACommandName()
	{
		$input  = new ArrayInput(
			[
				'command'      => 'help',
				'command_name' => 'list',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new HelpCommand;
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('list [<namespace>]', $screenOutput);
	}

	/**
	 * @covers  Joomla\Console\Command\HelpCommand
	 * @uses    Joomla\Console\Application
	 * @uses    Joomla\Console\Command\AbstractCommand
	 * @uses    Joomla\Console\Command\ListCommand
	 * @uses    Joomla\Console\Descriptor\TextDescriptor
	 * @uses    Joomla\Console\Helper\DescriptorHelper
	 */
	public function testTheCommandIsExecutedWithACommandClass()
	{
		$input  = new ArrayInput(
			[
				'command' => 'help',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new HelpCommand;
		$command->setApplication($application);
		$command->setCommand(new ListCommand);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('list [<namespace>]', $screenOutput);
	}
}
