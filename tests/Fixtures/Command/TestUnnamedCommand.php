<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Fixtures\Command;

use Joomla\Console\AbstractCommand;

class TestUnnamedCommand extends AbstractCommand
{
	/**
	 * {@inheritdoc}
	 */
	public function execute(): int
	{
		return 0;
	}
}
