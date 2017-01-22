<?php
/**
 * Part of the Joomla Framework Model Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model;

use Joomla\Database\DatabaseDriver;

/**
 * Joomla Framework Database Model Interface
 *
 * @since  1.3.0
 * @note   As of 2.0 the `Joomla\Database\DatabaseInterface` will be typehinted.
 */
interface DatabaseModelInterface
{
	/**
	 * Get the database driver.
	 *
	 * @return  DatabaseDriver  The database driver.
	 *
	 * @since   1.3.0
	 */
	public function getDb();
}
