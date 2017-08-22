<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Command;

use Joomla\Console\AbstractCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

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
		$output = $this->getApplication()->getConsoleOutput();

		$formatter = $output->getFormatter();
		$formatter->setStyle('cmd', new OutputFormatterStyle('magenta'));

		$executable = $this->getInput()->executable;

		$symfonyStyle = new SymfonyStyle($this->getApplication()->getConsoleInput(), $output);
		$symfonyStyle->title('Command Listing');
		$symfonyStyle->write(
			sprintf('Usage: <info>%s</info> <cmd><command></cmd>',
				$executable
			),
			true
		);

		$symfonyStyle->write("\nAvailable commands:\n\n");

		foreach ($this->getApplication()->getCommands() as $command)
		{
			$symfonyStyle->write('<cmd>' . $command->getName() . '</cmd>', true);
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
