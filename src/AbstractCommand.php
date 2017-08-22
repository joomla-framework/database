<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console;

use Joomla\Controller\AbstractController;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * Base class for a console command.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractCommand extends AbstractController implements CommandInterface
{
	/**
	 * The command's aliases.
	 *
	 * @var    string[]
	 * @since  __DEPLOY_VERSION__
	 */
	private $aliases = [];

	/**
	 * The command's input definition.
	 *
	 * @var    InputDefinition
	 * @since  __DEPLOY_VERSION__
	 */
	private $definition;

	/**
	 * The command's name.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $name = '';

	/**
	 * Constructor.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct()
	{
		$this->definition = new InputDefinition;

		$this->initialise();
	}

	/**
	 * Adds an argument to the input definition.
	 *
	 * @param   string   $name         The argument name
	 * @param   integer  $mode         The argument mode: InputArgument::REQUIRED or InputArgument::OPTIONAL
	 * @param   string   $description  A description text
	 * @param   mixed    $default      The default value (for InputArgument::OPTIONAL mode only)
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addArgument($name, $mode = null, $description = '', $default = null)
	{
		$this->definition->addArgument(new InputArgument($name, $mode, $description, $default));

		return $this;
	}

	/**
	 * Adds an option to the input definition.
	 *
	 * @param   string        $name         The option name
	 * @param   string|array  $shortcut     The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
	 * @param   integer       $mode         The option mode: One of the VALUE_* constants
	 * @param   string        $description  A description text
	 * @param   mixed         $default      The default value (must be null for InputOption::VALUE_NONE)
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null)
	{
		$this->definition->addOption(new InputOption($name, $shortcut, $mode, $description, $default));

		return $this;
	}

	/**
	 * Get the command's aliases.
	 *
	 * @return  string[]
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getAliases(): array
	{
		return $this->aliases;
	}

	/**
	 * Get the command's input definition.
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
	 * Get the command's name.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Check if the command is enabled in this environment.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function isEnabled(): bool
	{
		return true;
	}

	/**
	 * Initialise the command.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function initialise()
	{
	}

	/**
	 * Set the command's aliases.
	 *
	 * @param   string[]  $aliases  The command aliases
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setAliases(array $aliases)
	{
		$this->aliases = $aliases;
	}

	/**
	 * Sets the input definition for the command.
	 *
	 * @param   array|InputDefinition  $definition  Either an InputDefinition object or an array of objects to write to the definition.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setDefinition($definition)
	{
		if ($definition instanceof InputDefinition)
		{
			$this->definition = $definition;
		}
		else
		{
			$this->definition->setDefinition($definition);
		}

		return $this;
	}

	/**
	 * Set the command's name.
	 *
	 * @param   string  $name  The command name
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}
}
