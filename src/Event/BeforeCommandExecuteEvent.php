<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Event;

use Joomla\Console\Application;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Console\ConsoleEvents;

/**
 * Event triggered before a command is executed.
 *
 * @since  2.0.0-beta
 */
class BeforeCommandExecuteEvent extends ConsoleEvent
{
	/**
	 * The return code for a command disabled by this event.
	 *
	 * @var    integer
	 * @since  2.0.0-beta
	 */
	public const RETURN_CODE_DISABLED = 113;

	/**
	 * Flag indicating the command is enabled
	 *
	 * @var    boolean
	 * @since  2.0.0-beta
	 */
	private $commandEnabled = true;

	/**
	 * Event constructor.
	 *
	 * @param   Application           $application  The active application.
	 * @param   AbstractCommand|null  $command      The command being executed.
	 *
	 * @since   2.0.0-beta
	 */
	public function __construct(Application $application, ?AbstractCommand $command = null)
	{
		parent::__construct(ConsoleEvents::BEFORE_COMMAND_EXECUTE, $application, $command);

		if ($command)
		{
			$this->commandEnabled = $command->isEnabled();
		}
	}

	/**
	 * Disable the command.
	 *
	 * @return  void
	 *
	 * @since   2.0.0-beta
	 */
	public function disableCommand(): void
	{
		$this->commandEnabled = false;
	}

	/**
	 * Enable the command.
	 *
	 * @return  void
	 *
	 * @since   2.0.0-beta
	 */
	public function enableCommand(): void
	{
		$this->commandEnabled = false;
	}

	/**
	 * Check if the command is enabled.
	 *
	 * @return    boolean
	 *
	 * @since   2.0.0-beta
	 */
	public function isCommandEnabled(): bool
	{
		return $this->commandEnabled;
	}
}
