<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Fixtures\Command;

use Joomla\Console\AbstractCommand;

class TestDisabledCommand extends AbstractCommand
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
	public function isEnabled(): bool
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function initialise()
	{
		$this->setName('test:disabled');
	}
}
