<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console;

use Joomla\Application\AbstractApplication;
use Joomla\Console\Exception\CommandNotFoundException;
use Joomla\Input\Cli;
use Joomla\Registry\Registry;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base application class for a Joomla! command line application.
 *
 * @since  __DEPLOY_VERSION__
 */
class Application extends AbstractApplication
{
	/**
	 * The available commands.
	 *
	 * @var    CommandInterface[]
	 * @since  __DEPLOY_VERSION__
	 */
	private $commands = [];

	/**
	 * The command loader.
	 *
	 * @var    Loader\LoaderInterface
	 * @since  __DEPLOY_VERSION__
	 */
	private $commandLoader;

	/**
	 * Console input handler.
	 *
	 * @var    InputInterface
	 * @since  __DEPLOY_VERSION__
	 */
	private $consoleInput;

	/**
	 * Console output handler.
	 *
	 * @var    OutputInterface
	 * @since  __DEPLOY_VERSION__
	 */
	private $consoleOutput;

	/**
	 * The default command for the application.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $defaultCommand = 'list';

	/**
	 * The base application input definition.
	 *
	 * @var    InputDefinition
	 * @since  __DEPLOY_VERSION__
	 */
	private $definition;

	/**
	 * The exit code from the command.
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	private $exitCode = 0;

	/**
	 * Class constructor.
	 *
	 * @param   Cli       $input   An optional argument to provide dependency injection for the application's input object.  If the argument is an
	 *                             Input object that object will become the application's input object, otherwise a default input object is created.
	 * @param   Registry  $config  An optional argument to provide dependency injection for the application's config object.  If the argument
	 *                             is a Registry object that object will become the application's config object, otherwise a default config
	 *                             object is created.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Cli $input = null, Registry $config = null)
	{
		// Close the application if we are not executed from the command line.
		if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			$this->close();
		}

		// Call the constructor as late as possible (it runs `initialise`).
		parent::__construct($input ?: new Cli, $config);
	}

	/**
	 * Add a command to the application.
	 *
	 * @param   CommandInterface  $command  The command to add
	 *
	 * @return  CommandInterface|void  The registered command or null if the command is not enabled
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addCommand(CommandInterface $command)
	{
		if (!$command->isEnabled())
		{
			return;
		}

		$command->setApplication($this);

		if (!$command->getName())
		{
			throw new \LogicException(sprintf('The command class %s does not have a name.', get_class($command)));
		}

		$this->commands[$command->getName()] = $command;

		foreach ($command->getAliases() as $alias)
		{
			$this->commands[$alias] = $command;
		}

		return $command;
	}

	/**
	 * Configures the console input and output instances for the process.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function configureIO()
	{
		$input  = $this->getConsoleInput();
		$output = $this->getConsoleOutput();

		if ($input->hasParameterOption(['--ansi'], true))
		{
			$output->setDecorated(true);
		}
		elseif ($input->hasParameterOption(['--no-ansi'], true))
		{
			$output->setDecorated(false);
		}

		if ($input->hasParameterOption(['--no-interaction', '-n'], true))
		{
			$input->setInteractive(false);
		}

		if ($input->hasParameterOption(['--quiet', '-q'], true))
		{
			$output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
			$input->setInteractive(false);
		}
		else
		{
			if ($input->hasParameterOption('-vvv', true)
				|| $input->hasParameterOption('--verbose=3', true)
				|| $input->getParameterOption('--verbose', false, true) === 3
			)
			{
				$output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
			}
			elseif ($input->hasParameterOption('-vv', true)
				|| $input->hasParameterOption('--verbose=2', true)
				|| $input->getParameterOption('--verbose', false, true) === 2
			)
			{
				$output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
			}
			elseif ($input->hasParameterOption('-v', true)
				|| $input->hasParameterOption('--verbose=1', true)
				|| $input->hasParameterOption('--verbose', true)
				|| $input->getParameterOption('--verbose', false, true)
			)
			{
				$output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
			}
		}
	}

	/**
	 * Method to run the application routines.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function doExecute()
	{
		$commandName = $this->getCommandName();

		if (!$commandName)
		{
			$this->out('<comment>Command name not given.</comment>');

			$this->close(1);
		}

		$command = $this->getCommand($commandName);
		$command->mergeApplicationDefinition($this->definition);

		$this->getConsoleInput()->bind($command->getDefinition());

		$exitCode = $command->execute();

		$this->exitCode = is_numeric($exitCode) ? (int) $exitCode : 0;
	}

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function execute()
	{
		$this->configureIO();

		$this->doExecute();
	}

	/**
	 * Gets the base input definition.
	 *
	 * @return  InputDefinition
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getBaseInputDefinition(): InputDefinition
	{
		return new InputDefinition(
			[
				new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
				new InputOption('--quiet', '-q', InputOption::VALUE_NONE, 'Flag indicating that all output should be silenced'),
				new InputOption(
					'--verbose',
					'-v|vv|vvv',
					InputOption::VALUE_NONE,
					'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'
				),
				new InputOption('--ansi', '', InputOption::VALUE_NONE, 'Force ANSI output'),
				new InputOption('--no-ansi', '', InputOption::VALUE_NONE, 'Disable ANSI output'),
				new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, 'Flag to disable interacting with the user'),
			]
		);
	}

	/**
	 * Get a command by name.
	 *
	 * @param   string  $name  The name of the command to retrieve.
	 *
	 * @return  CommandInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  CommandNotFoundException
	 */
	public function getCommand(string $name): CommandInterface
	{
		if (isset($this->commands[$name]))
		{
			return $this->commands[$name];
		}

		if ($this->commandLoader && $this->commandLoader->has($name))
		{
			$command = $this->commandLoader->get($name);

			$this->addCommand($command);

			return $command;
		}

		throw new CommandNotFoundException("There is not a command with the name '$name'.");
	}

	/**
	 * Get the name of the command to run.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getCommandName(): string
	{
		$args = $this->input->args;

		return !empty($args[0]) ? $args[0] : $this->defaultCommand;
	}

	/**
	 * Get the registered commands.
	 *
	 * @return  CommandInterface[]
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getCommands(): array
	{
		return $this->commands;
	}

	/**
	 * Get the console input handler.
	 *
	 * @return  InputInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getConsoleInput(): InputInterface
	{
		return $this->consoleInput;
	}

	/**
	 * Get the console output handler.
	 *
	 * @return  OutputInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getConsoleOutput(): OutputInterface
	{
		return $this->consoleOutput;
	}

	/**
	 * Get the commands which should be registered by default to the application.
	 *
	 * @return  CommandInterface[]
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getDefaultCommands(): array
	{
		return [
			new Command\ListCommand,
		];
	}

	/**
	 * Get the application definition.
	 *
	 * @return  InputDefinition
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getDefinition(): InputDefinition
	{
		return $this->definition;
	}

	/**
	 * Check if the application has a command with the given name.
	 *
	 * @param   string  $name  The name of the command to check for existence.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function hasCommand(string $name): bool
	{
		return isset($this->commands[$name]) || ($this->commandLoader && $this->commandLoader->has($name));
	}

	/**
	 * Custom initialisation method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function initialise()
	{
		// Set the current directory.
		$this->set('cwd', getcwd());

		$this->consoleInput  = new ArgvInput;
		$this->consoleOutput = new ConsoleOutput;

		$this->definition = $this->getBaseInputDefinition();

		// Register default commands
		foreach ($this->getDefaultCommands() as $command)
		{
			$this->addCommand($command);
		}
	}

	/**
	 * Set the command loader.
	 *
	 * @param   Loader\LoaderInterface  $loader  The new command loader.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setCommandLoader(Loader\LoaderInterface $loader)
	{
		$this->commandLoader = $loader;

		return $this;
	}

	/**
	 * Set the console input handler.
	 *
	 * @param   InputInterface  $input  The new console input handler.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setConsoleInput(InputInterface $input)
	{
		$this->consoleInput = $input;

		return $this;
	}

	/**
	 * Set the console output handler.
	 *
	 * @param   OutputInterface  $output  The new console output handler.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setConsoleOutput(OutputInterface $output)
	{
		$this->consoleOutput = $output;

		return $this;
	}
}
