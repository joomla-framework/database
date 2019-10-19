<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Monitor;

use Joomla\Database\Monitor\ChainedMonitor;
use Joomla\Database\QueryMonitorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Monitor\ChainedMonitor
 */
class ChainedMonitorTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  ChainedMonitor
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

		$this->monitor = new ChainedMonitor;
	}

	/**
	 * @testdox  The chained monitor forwards calls to all attached monitors
	 */
	public function testChaining()
	{
		$monitor1 = $this->createMonitor();
		$monitor2 = $this->createMonitor();
		$monitor3 = $this->createMonitor();

		$this->monitor->addMonitor($monitor1);
		$this->monitor->addMonitor($monitor2);
		$this->monitor->addMonitor($monitor3);

		$this->monitor->startQuery('SELECT 1');

		$this->assertTrue($monitor1->startCalled);
		$this->assertTrue($monitor2->startCalled);
		$this->assertTrue($monitor3->startCalled);

		$this->monitor->stopQuery();

		$this->assertTrue($monitor1->stopCalled);
		$this->assertTrue($monitor2->stopCalled);
		$this->assertTrue($monitor3->stopCalled);
	}

	private function createMonitor(): QueryMonitorInterface
	{
		return new class implements QueryMonitorInterface
		{
			public $startCalled = false;
			public $stopCalled = false;

			public function startQuery(string $sql): void
			{
				$this->startCalled = true;
			}

			public function stopQuery(): void
			{
				$this->stopCalled = true;
			}
		};
	}
}
