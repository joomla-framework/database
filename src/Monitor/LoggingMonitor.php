<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Monitor;

use Joomla\Database\QueryMonitorInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Query monitor handling logging of queries.
 *
 * @since  2.0.0-beta
 */
class LoggingMonitor implements QueryMonitorInterface, LoggerAwareInterface
{
	use LoggerAwareTrait;

	/**
	 * Act on a query being started.
	 *
	 * @param   string  $sql  The SQL to be executed.
	 *
	 * @return  void
	 *
	 * @since   2.0.0-beta
	 */
	public function startQuery(string $sql): void
	{
		if ($this->logger)
		{
			// Add the query to the object queue.
			$this->logger->info(
				'Query Executed: {sql}',
				['sql' => $sql, 'trace' => debug_backtrace()]
			);
		}
	}

	/**
	 * Act on a query being stopped.
	 *
	 * @return  void
	 *
	 * @since   2.0.0-beta
	 */
	public function stopQuery(): void
	{
		// Nothing to do
	}
}
