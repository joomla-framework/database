<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Command;

use Joomla\Console\Application;
use Joomla\Console\Command\ListCommand;
use Joomla\Console\Tests\Fixtures\Command\NamespacedCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Console\Command\ListCommand
 */
class ListCommandTest extends TestCase
{
	public function testTheCommandIsExecuted()
	{
		$input  = new ArrayInput(
			[
				'command' => 'list',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ListCommand;
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();

		$this->assertRegExp('/help\s{2,}Show the help for a command/', $screenOutput);
	}

	public function testTheCommandIsExecutedForANamespace()
	{
		$input  = new ArrayInput(
			[
				'command'   => 'list',
				'namespace' => 'test',
			]
		);
		$output = new BufferedOutput;

		$namespacedCommand = new NamespacedCommand;
		$namespacedCommand->setDescription('A testing command');

		$application = new Application($input, $output);
		$application->addCommand($namespacedCommand);

		$command = new ListCommand;
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();

		$this->assertRegExp('/test:namespaced\s{2,}A testing command/', $screenOutput);
	}
}
