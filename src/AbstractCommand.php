<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console;

use Joomla\Console\Input\InputDefinition;
use Joomla\Console\Input\InputOption;
use Joomla\Controller\AbstractController;

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
	 * Adds an option to the input definition.
	 *
	 * @param   string   $name         The argument name.
	 * @param   mixed    $shortcut     An optional shortcut for the option, either a string or an array of strings.
	 * @param   integer  $mode         The option mode.
	 * @param   string   $description  A description text.
	 * @param   mixed    $default      The default value when the argument is optional.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addOption(string $name, $shortcut = null, int $mode = InputOption::OPTIONAL, string $description = '', $default = null)
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
