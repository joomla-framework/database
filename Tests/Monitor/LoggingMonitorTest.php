<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Monitor;

use Joomla\Database\Monitor\LoggingMonitor;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

/**
 * Test class for Joomla\Database\Monitor\LoggingMonitor
 */
class LoggingMonitorTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  LoggingMonitor
	 */
	private $monitor;

	/**
	 * Logger for testing
	 *
	 * @var TestLogger
	 */
	private $logger;

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

		$this->logger = new TestLogger;

		$this->monitor = new LoggingMonitor;
	}

	/**
	 * @testdox  The monitor does not log messages if no logger is injected
	 */
	public function testStartQueryNoLogger()
	{
		$this->monitor->startQuery('SELECT 1');

		$this->assertFalse(
			$this->logger->hasInfoThatContains('Query Executed:')
		);
	}

	/**
	 * @testdox  The monitor does log messages if a logger is injected
	 */
	public function testStartQueryWithLogger()
	{
		$this->monitor->setLogger($this->logger);

		$this->monitor->startQuery('SELECT 1');

		$this->assertTrue(
			$this->logger->hasInfoThatContains('Query Executed:')
		);
	}
}
