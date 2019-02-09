<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Command;

use Joomla\Console\Application;
use Joomla\Console\Command\AbstractCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test class for \Joomla\Console\Command\AbstractCommand
 */
class AbstractCommandTest extends TestCase
{
	public function testTheCommandIsExecutedWithoutAnApplication()
	{
		$command = new class extends AbstractCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$input = $this->createMock(InputInterface::class);
		$output = $this->createMock(OutputInterface::class);

		$this->assertSame(0, $command->execute($input, $output));
	}

	public function testTheCommandIsExecutedWithAnApplication()
	{
		$command = new class extends AbstractCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$input = $this->createMock(InputInterface::class);
		$output = $this->createMock(OutputInterface::class);

		$application = new Application($input, $output);

		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));
	}

	public function testArgumentsAreAddedToTheDefinition()
	{
		$command = new class extends AbstractCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$this->assertSame($command, $command->addArgument('test'), 'addArgument has a fluent interface');
		$this->assertTrue($command->getDefinition()->hasArgument('test'));
	}

	public function testOptionsAreAddedToTheDefinition()
	{
		$command = new class extends AbstractCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$this->assertSame($command, $command->addOption('test'), 'addOption has a fluent interface');
		$this->assertTrue($command->getDefinition()->hasOption('test'));
	}

	public function testTheDefaultCommandNameIsRetrieved()
	{
		$command = new class extends AbstractCommand
		{
			protected static $defaultName = 'test:command';

			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$this->assertSame('test:command', $command::getDefaultName());
	}

	public function testTheCommandHelpIsProcessed()
	{
		$command = new class extends AbstractCommand
		{
			protected static $defaultName = 'test:command';

			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}

			public function getHelp(): string
			{
				return 'The %command.name% command is used... Example: php %command.full_name%';
			}
		};

		$this->assertContains('The test:command command is used...', $command->getProcessedHelp(), 'getProcessedHelp() replaces %command.name%');
		$this->assertNotContains('%command.full_name%', $command->getProcessedHelp(), 'getProcessedHelp() replaces %command.full_name%');
	}

	public function testTheCommandSynopsisIsProcessed()
	{
		$command = new class extends AbstractCommand
		{
			protected static $defaultName = 'test:command';

			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$command->addOption('foo');
		$command->addArgument('bar');

		$this->assertEquals('test:command [--foo] [--] [<bar>]', $command->getSynopsis());
	}

	public function testTheApplicationInputDefinitionIsMergedWithTheCommand()
	{
		$command = new class extends AbstractCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$input = $this->createMock(InputInterface::class);
		$output = $this->createMock(OutputInterface::class);

		$application = new Application($input, $output);

		$application->getDefinition()->addArguments([new InputArgument('foo')]);
		$application->getDefinition()->addOptions([new InputOption('bar')]);

		$command->setApplication($application);
		$command->setDefinition($definition = new InputDefinition([new InputArgument('bar'), new InputOption('foo')]));

		$command->mergeApplicationDefinition();

		$this->assertTrue($command->getDefinition()->hasArgument('foo'));
		$this->assertTrue($command->getDefinition()->hasArgument('bar'));
		$this->assertTrue($command->getDefinition()->hasOption('foo'));
		$this->assertTrue($command->getDefinition()->hasOption('bar'));

		$command->mergeApplicationDefinition();

		$this->assertEquals(
			3,
			$command->getDefinition()->getArgumentCount(),
			'mergeApplicationDefinition() does not try to merge the application arguments and options multiple times'
		);
	}

	public function testTheArgumentsOfTheApplicationInputDefinitionAreNotMergedWithTheCommandUntilInstructed()
	{
		$command = new class extends AbstractCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$input = $this->createMock(InputInterface::class);
		$output = $this->createMock(OutputInterface::class);

		$application = new Application($input, $output);

		$application->getDefinition()->addArguments([new InputArgument('foo')]);
		$application->getDefinition()->addOptions([new InputOption('bar')]);

		$command->setApplication($application);
		$command->setDefinition($definition = new InputDefinition([new InputArgument('bar'), new InputOption('foo')]));

		$command->mergeApplicationDefinition(false);

		$this->assertFalse($command->getDefinition()->hasArgument('foo'));
		$this->assertTrue($command->getDefinition()->hasArgument('bar'));
		$this->assertTrue($command->getDefinition()->hasOption('foo'));
		$this->assertTrue($command->getDefinition()->hasOption('bar'));

		$command->mergeApplicationDefinition(true);

		$this->assertTrue($command->getDefinition()->hasArgument('foo'));
	}

	public function testTheApplicationHelperSetIsMergedToTheCommand()
	{
		$command = new class extends AbstractCommand
		{
			protected function doExecute(InputInterface $input, OutputInterface $output): int
			{
				return 0;
			}
		};

		$input = $this->createMock(InputInterface::class);
		$output = $this->createMock(OutputInterface::class);

		$application = new Application($input, $output);

		$command->setApplication($application);

		$this->assertSame($command->getHelperSet(), $application->getHelperSet());
	}
}
