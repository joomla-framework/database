<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Fixtures\Command;

use Joomla\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Internal test command to test handling of a command not configuring itself correctly.
 */
final class SkipConfigurationCommand extends AbstractCommand
{
	/**
	 * The default command name
	 *
	 * @var  string
	 */
	protected static $defaultName = 'test:skip-configure';

	/**
	 * Command constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		return 0;
	}
}
