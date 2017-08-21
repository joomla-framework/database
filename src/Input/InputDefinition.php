<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Input;

/**
 * Class defining the definition for a command.
 *
 * @since  __DEPLOY_VERSION__
 */
class InputDefinition
{
	/**
	 * Container holding the known options.
	 *
	 * @var    InputOption[]
	 * @since  __DEPLOY_VERSION__
	 */
	private $options;

	/**
	 * Mapping array of shortcut values to an option.
	 *
	 * @var    string[]
	 * @since  __DEPLOY_VERSION__
	 */
	private $shortcuts = [];

	/**
	 * Constructor.
	 *
	 * @param   array  $definition  Array of InputOption objects defining a command.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(array $definition = [])
	{
		$this->setDefinition($definition);
	}

	/**
	 * Adds an option to the definition.
	 *
	 * @param   InputOption  $option  The object defining the option.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \LogicException
	 */
	public function addOption(InputOption $option)
	{
		if (isset($this->options[$option->getName()]) && !$option->sameAs($this->options[$option->getName()]))
		{
			throw new \LogicException(sprintf('An option named "%s" already exists.', $option->getName()));
		}

		foreach ($option->getShortcuts() as $shortcut)
		{
			if (isset($this->shortcuts[$shortcut]) && !$option->sameAs($this->options[$this->shortcuts[$shortcut]]))
			{
				throw new \LogicException(sprintf('An option with shortcut "%s" already exists.', $shortcut));
			}
		}

		$this->options[$option->getName()] = $option;

		foreach ($option->getShortcuts() as $shortcut)
		{
			$this->shortcuts[$shortcut] = $option->getName();
		}
	}

	/**
	 * Adds options to the definition.
	 *
	 * @param   InputOption[]  $options  Array of InputOption objects defining a command.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addOptions(array $options = [])
	{
		foreach ($options as $option)
		{
			$this->addOption($option);
		}
	}

	/**
	 * Get the option for a given name.
	 *
	 * @param   string  $name  The option name.
	 *
	 * @return  InputOption
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \InvalidArgumentException
	 */
	public function getOption(string $name): InputOption
	{
		if (!$this->hasOption($name))
		{
			throw new \InvalidArgumentException(sprintf('The "--%s" option does not exist.', $name));
		}

		return $this->options[$name];
	}

	/**
	 * Get the option for a given shortcut.
	 *
	 * @param   string  $name  The option shortcut.
	 *
	 * @return  InputOption
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getOptionForShortcut(string $name): InputOption
	{
		return $this->getOption($this->findOptionNameForShortcut($name));
	}

	/**
	 * Get the configured options.
	 *
	 * @return  InputOption[]
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * Checks if an option exists.
	 *
	 * @param   string  $name  The option name.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function hasOption(string $name): bool
	{
		return isset($this->options[$name]);
	}

	/**
	 * Checks if a shortcut exists.
	 *
	 * @param   string  $name  The shortcut name.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function hasShortcut(string $name): bool
	{
		return isset($this->shortcuts[$name]);
	}

	/**
	 * Sets the definition of the command.
	 *
	 * @param   array  $definition  Array of InputOption objects defining a command.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setDefinition(array $definition)
	{
		$this->setOptions($definition);
	}

	/**
	 * Sets the options for the definition.
	 *
	 * @param   InputOption[]  $options  Array of InputOption objects defining a command.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setOptions($options = [])
	{
		$this->options   = [];
		$this->shortcuts = [];

		$this->addOptions($options);
	}

	/**
	 * Returns the InputOption name given a shortcut.
	 *
	 * @param   string  $shortcut  The shortcut name.
	 *
	 * @return  string
	 *
	 * @throws  \InvalidArgumentException
	 */
	private function findOptionNameForShortcut(string $shortcut): string
	{
		if (!$this->hasShortcut($shortcut))
		{
			throw new \InvalidArgumentException(sprintf('The "-%s" option does not exist.', $shortcut));
		}

		return $this->shortcuts[$shortcut];
	}
}
