<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Fixtures\Command;

use Joomla\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputOption;

class TestNoAliasWithOptionsCommand extends AbstractCommand
{
	/**
	 * {@inheritdoc}
	 */
	public function execute(): int
	{
		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function initialise()
	{
		$this->setName('test:noalias:options');
		$this->addOption('foo', 'f', InputOption::VALUE_REQUIRED);
		$this->addOption('bar', 'b', InputOption::VALUE_REQUIRED, '', 'defined');
	}
}
