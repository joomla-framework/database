<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Input;

/**
 * Value object representing a command line option.
 *
 * @since  __DEPLOY_VERSION__
 */
class InputOption
{
	/**
	 * Constant defining an option with no value.
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	const NONE = 1;

	/**
	 * Constant defining a required option.
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	const REQUIRED = 2;

	/**
	 * Constant defining an optional option.
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	const OPTIONAL = 4;

	/**
	 * The option default value.
	 *
	 * @var    mixed
	 * @since  __DEPLOY_VERSION__
	 */
	private $default;

	/**
	 * The option description.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $description = '';

	/**
	 * The option mode (optional or required).
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	private $mode = self::OPTIONAL;

	/**
	 * The option name.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	private $name = '';

	/**
	 * The option shortcuts.
	 *
	 * @var    string[]
	 * @since  __DEPLOY_VERSION__
	 */
	private $shortcuts = [];

	/**
	 * Constructor.
	 *
	 * @param   string   $name         The argument name.
	 * @param   mixed    $shortcut     An optional shortcut for the option, either a string or an array of strings.
	 * @param   integer  $mode         The option mode.
	 * @param   string   $description  A description text.
	 * @param   mixed    $default      The default value when the argument is optional.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \InvalidArgumentException
	 */
	public function __construct(string $name, $shortcut = null, int $mode = self::OPTIONAL, string $description = '', $default = null)
	{
		if (empty($name))
		{
			throw new \InvalidArgumentException('An option must have a name.');
		}

		if ($mode > 4 || $mode < 1 || $mode === 3)
		{
			throw new \InvalidArgumentException(sprintf('Argument mode "%s" is not valid.', $mode));
		}

		if ($shortcut !== null)
		{
			if (is_array($shortcut))
			{
				$this->shortcuts = $shortcut;
			}
			elseif (is_string($shortcut))
			{
				$this->shortcuts = [$shortcut];
			}
			else
			{
				throw new \InvalidArgumentException(sprintf('An option shortcut must be an array or string, "%s" given.', gettype($shortcut)));
			}
		}

		$this->name        = $name;
		$this->mode        = $mode;
		$this->description = $description;

		$this->setDefault($default);
	}

	/**
	 * Check if the option accepts a value.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function acceptValue(): bool
	{
		return $this->isRequired() || $this->isOptional();
	}

	/**
	 * Get the option default value.
	 *
	 * @return  mixed
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getDefault()
	{
		return $this->default;
	}

	/**
	 * Get the option description.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * Get the option name.
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
	 * Get the option shortcuts.
	 *
	 * @return  string[]
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getShortcuts(): array
	{
		return $this->shortcuts;
	}

	/**
	 * Check if the option is optional.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function isOptional(): bool
	{
		return $this->mode === self::OPTIONAL;
	}

	/**
	 * Check if the option is required.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function isRequired(): bool
	{
		return $this->mode === self::REQUIRED;
	}

	/**
	 * Checks whether the given option is the same as this one.
	 *
	 * @param   InputOption  $option  Option to be compared.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function sameAs(InputOption $option): bool
	{
		return $option->getName() === $this->getName()
			&& $option->getShortcuts() === $this->getShortcuts()
			&& $option->getDefault() === $this->getDefault()
			&& $option->isRequired() === $this->isRequired()
			&& $option->isOptional() === $this->isOptional();
	}

	/**
	 * Sets the default value.
	 *
	 * @param   mixed  $default  The default value.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \LogicException
	 */
	public function setDefault($default = null)
	{
		if ($this->mode === self::NONE && $default !== null)
		{
			throw new \LogicException(sprintf('Cannot set a default value when using %s::NONE mode.', get_class($this)));
		}

		$this->default = $this->acceptValue() ? $default : false;
	}
}
