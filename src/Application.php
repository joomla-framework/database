<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console;

use Joomla\Application\ApplicationInterface;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * Base application class for a Joomla! command line application.
 *
 * @since  __DEPLOY_VERSION__
 */
class Application extends BaseApplication implements ApplicationInterface
{
	/**
	 * Method to close the application.
	 *
	 * @param   integer  $code  The exit code (optional; default is 0).
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function close($code = 0)
	{
		exit($code);
	}

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function execute()
	{
		$input  = null;
		$output = null;

		$args = \func_get_args();

		if (\count($args) > 0)
		{
			$input  = $args[0] ?? null;
			$output = $args[1] ?? null;
		}

		$this->run($input, $output);
	}
}
