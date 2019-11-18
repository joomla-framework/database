<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Monitor;

use Joomla\Database\Monitor\DebugMonitor;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Monitor\DebugMonitor
 */
class DebugMonitorTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  DebugMonitor
	 */
	private $monitor;

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->monitor = new DebugMonitor;
	}

	/**
	 * @testdox  The monitor collects debug metrics about a query
	 */
	public function testMonitor()
	{
		// "Execute" 3 queries, we'll use the password_hash function to force time/memory usage to increase along the way
		for ($i = 1; $i <= 3; $i++)
		{
			$this->monitor->startQuery("SELECT $i");

			password_hash("password_hash_$i", PASSWORD_DEFAULT);

			$this->monitor->stopQuery();
		}

		$this->assertCount(3, $this->monitor->getCallStacks(), 'There should be a call stack for each query.');
		$this->assertCount(3, $this->monitor->getLogs(), 'There should be a logged query for each query.');
		$this->assertCount(6, $this->monitor->getMemoryLogs(), 'There should be memory stamp for the start and stop of each query.');
		$this->assertCount(6, $this->monitor->getTimings(), 'There should be timestamp for the start and stop of each query.');
	}
}
