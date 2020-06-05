<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Monitor;

use Joomla\Database\QueryMonitorInterface;

/**
 * Chained query monitor allowing multiple monitors to be executed.
 *
 * @since  2.0.0-beta
 */
class ChainedMonitor implements QueryMonitorInterface
{
	/**
	 * The query monitors stored to this chain
	 *
	 * @var    QueryMonitorInterface[]
	 * @since  2.0.0-beta
	 */
	private $monitors = [];

	/**
	 * Register a monitor to the chain.
	 *
	 * @param   QueryMonitorInterface  $monitor  The monitor to add.
	 *
	 * @return  void
	 *
	 * @since   2.0.0-beta
	 */
	public function addMonitor(QueryMonitorInterface $monitor): void
	{
		$this->monitors[] = $monitor;
	}

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
		foreach ($this->monitors as $monitor)
		{
			$monitor->startQuery($sql);
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
		foreach ($this->monitors as $monitor)
		{
			$monitor->stopQuery();
		}
	}
}
