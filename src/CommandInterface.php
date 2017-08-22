<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console;

use Joomla\Controller\ControllerInterface;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Interface defining console commands.
 *
 * @since  __DEPLOY_VERSION__
 */
interface CommandInterface extends ControllerInterface
{
	/**
	 * Get the command's aliases.
	 *
	 * @return  string[]
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getAliases(): array;

	/**
	 * Get the command's input definition.
	 *
	 * @return  InputDefinition
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getDefinition(): InputDefinition;

	/**
	 * Get the command's name.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getName(): string;

	/**
	 * Check if the command is enabled in this environment.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function isEnabled(): bool;

	/**
	 * Merges the definition from the application to this command.
	 *
	 * @param   boolean  $mergeArgs  Flag indicating whether the application's definition arguments should be merged
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @internal  This method should not be relied on as part of the public API
	 */
	public function mergeApplicationDefinition(InputDefinition $definition, $mergeArgs = true);

	/**
	 * Set the command's aliases.
	 *
	 * @param   string[]  $aliases  The command aliases
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setAliases(array $aliases);

	/**
	 * Set the command's name.
	 *
	 * @param   string  $name  The command name
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setName(string $name);
}
