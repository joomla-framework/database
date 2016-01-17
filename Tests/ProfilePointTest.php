<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Profiler\Tests;

use Joomla\Profiler\ProfilePoint;

/**
 * Tests for the \Joomla\Profiler\ProfilePoint class.
 */
class ProfilePointTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var  ProfilePoint
	 */
	private $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->instance = new ProfilePoint('test');
	}

	/**
	 * @covers  \Joomla\Profiler\ProfilePoint::__construct
	 * @uses    \Joomla\Profiler\ProfilePoint::getMemoryBytes
	 * @uses    \Joomla\Profiler\ProfilePoint::getName
	 * @uses    \Joomla\Profiler\ProfilePoint::getTime
	 */
	public function testThePointIsInstantiatedCorrectly()
	{
		$this->assertSame('test', $this->instance->getName());
		$this->assertSame(0.0, $this->instance->getTime());
		$this->assertSame(0, $this->instance->getMemoryBytes());
	}

	/**
	 * @covers  \Joomla\Profiler\ProfilePoint::__construct
	 * @uses    \Joomla\Profiler\ProfilePoint::getMemoryBytes
	 * @uses    \Joomla\Profiler\ProfilePoint::getName
	 * @uses    \Joomla\Profiler\ProfilePoint::getTime
	 */
	public function testThePointIsInstantiatedCorrectlyWithInjectedDependencies()
	{
		$point = new ProfilePoint('foo', '1', '1048576');
		$this->assertSame('foo', $point->getName());
		$this->assertSame(1.0, $point->getTime());
		$this->assertSame(1048576, $point->getMemoryBytes());
	}

	/**
	 * @covers  \Joomla\Profiler\ProfilePoint::getName
	 */
	public function testThePointNameIsReturned()
	{
		$this->assertEquals($this->instance->getName(), 'test');
	}

	/**
	 * @covers  \Joomla\Profiler\ProfilePoint::getTime
	 */
	public function testThePointTimeIsReturned()
	{
		$this->assertEquals($this->instance->getTime(), 0.0);
	}

	/**
	 * @covers  \Joomla\Profiler\ProfilePoint::getMemoryBytes
	 */
	public function testThePointMemoryIsReturnedInBytes()
	{
		$this->assertEquals($this->instance->getMemoryBytes(), 0);
	}

	/**
	 * @covers  \Joomla\Profiler\ProfilePoint::getMemoryMegaBytes
	 */
	public function testThePointMemoryIsReturnedInMegabytes()
	{
		$profilePoint = new ProfilePoint('test', 0, 1048576);
		$this->assertEquals($profilePoint->getMemoryMegaBytes(), 1);
	}
}
