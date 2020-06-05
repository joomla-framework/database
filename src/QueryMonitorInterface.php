<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Interface defining a query monitor.
 *
 * @since  2.0.0-beta
 */
interface QueryMonitorInterface
{
	/**
	 * Act on a query being started.
	 *
	 * @param   string  $sql  The SQL to be executed.
	 *
	 * @return  void
	 *
	 * @since   2.0.0-beta
	 */
	public function startQuery(string $sql): void;

	/**
	 * Act on a query being stopped.
	 *
	 * @return  void
	 *
	 * @since   2.0.0-beta
	 */
	public function stopQuery(): void;
}
