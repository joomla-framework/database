<?php
/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\IO;

/**
 * Interface defining an output processor
 *
 * @since  __DEPLOY_VERSION__
 */
interface OutputProcessorInterface
{
	/**
	 * Process the provided output into a string.
	 *
	 * @param   string  $output  The string to process.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function process(string $output): string;
}
