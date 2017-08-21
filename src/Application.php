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
	 * The default command for the application.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $defaultCommand = 'list';

	/**
	 * Output handler.
	 *
	 * @var    OutputInterface
	 * @since  __DEPLOY_VERSION__
	 */
	private $output;

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

		$this->output = new ConsoleOutput;

		// Call the constructor as late as possible (it runs `initialise`).
		parent::__construct($input ?: new Cli, $config);

		// Set the current directory.
		$this->set('cwd', getcwd());

		// Register default commands
		$this->addCommand(new Command\ListCommand);
	}

	/**
	 * Add a command to the application.
	 *
	 * @param   CommandInterface $command  The command to add
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

		if ($command instanceof AbstractCommand)
		{
			$command->setApplication($this);
			$command->setInput($this->input);
		}

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

		$this->validateCommand($command);

		$command->execute();
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
	 * Get the output handler.
	 *
	 * @return  OutputInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getOutputHandler(): OutputInterface
	{
		return $this->output;
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
	 * Set the output handler.
	 *
	 * @param   OutputInterface  $output  The new output handler.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setOutputHandler(OutputInterface $output)
	{
		$this->output = $output;

		return $this;
	}

	/**
	 * Validates a command meets its definition.
	 *
	 * @param   CommandInterface  $command  The command to validate.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function validateCommand(CommandInterface $command)
	{
		$definition = $command->getDefinition();

		// First, make sure options with default values are set to the global input
		foreach ($definition->getOptions() as $option)
		{
			if ($option->getDefault())
			{
				$this->input->def($option->getName(), $option->getDefault());

				foreach (explode('|', $option->getShortcut()) as $shortcut)
				{
					$this->input->def($shortcut, $option->getDefault());
				}
			}
		}

		$missingOptions = array_filter(
			$definition->getOptions(),
			function (InputOption $option) use ($definition)
			{
				$optionPresent = $this->input->get($option->getName()) && $option->isValueRequired();

				// If the option isn't present by its full name and is required, check for a shortcut
				if ($option->isValueRequired() && !$optionPresent && !empty($option->getShortcut()))
				{
					foreach (explode('|', $option->getShortcut()) as $shortcut)
					{
						if ($this->input->get($shortcut) !== null)
						{
							return false;
						}
					}
				}

				return !$optionPresent;
			}
		);

		if (count($missingOptions) > 0)
		{
			throw new \RuntimeException(sprintf('Not enough options (missing: %s).', count($missingOptions)));
		}
	}
}
