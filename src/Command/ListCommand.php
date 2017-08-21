<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Command;

use Joomla\Console\AbstractCommand;
use Joomla\Console\IO\ColorProcessor;
use Joomla\Console\IO\ColorStyle;

/**
 * Command listing all available commands.
 *
 * @method         \Joomla\Console\Application  getApplication()  Get the application object.
 * @property-read  \Joomla\Console\Application  $app              Application object
 *
 * @since  __DEPLOY_VERSION__
 */
class ListCommand extends AbstractCommand
{
	/**
	 * Execute the command.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function execute()
	{
		/** @var ColorProcessor $processor */
		$processor = $this->getApplication()->getOutputHandler()->getProcessor();

		if ($processor instanceof ColorProcessor)
		{
			$processor->addStyle('title', new ColorStyle('yellow', '', ['bold']));
			$processor->addStyle('cmd', new ColorStyle('magenta'));
		}

		$executable = $this->getInput()->executable;

		$this->getApplication()->outputTitle('Command Listing')
			->out(
				sprintf('Usage: <info>%s</info> <cmd><command></cmd>',
					$executable
				)
			);

		$this->getApplication()->out()
			->out('Available commands:')
			->out();

		foreach ($this->getApplication()->getCommands() as $command)
		{
			$this->getApplication()->out('<cmd>' . $command->getName() . '</cmd>')
				->out();
		}

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
		$this->setName('list');
	}
}
