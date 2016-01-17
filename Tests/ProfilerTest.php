<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Profiler\Tests;

use Joomla\Profiler\Renderer\DefaultRenderer;
use Joomla\Profiler\ProfilePoint;
use Joomla\Profiler\Profiler;

/**
 * Test class for Joomla\Profiler\Profiler.
 */
class ProfilerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var  Profiler
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

		$this->instance = new Profiler('test');
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::__construct
	 */
	public function testTheProfilerIsInstantiatedCorrectly()
	{
		$this->assertAttributeSame('test', 'name', $this->instance);
		$this->assertAttributeInstanceOf('\Joomla\Profiler\Renderer\DefaultRenderer', 'renderer', $this->instance);
		$this->assertAttributeEmpty('points', $this->instance);
		$this->assertAttributeSame(false, 'memoryRealUsage', $this->instance);
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::__construct
	 */
	public function testTheProfilerIsInstantiatedCorrectlyWithInjectedDependencies()
	{
		$renderer = new DefaultRenderer;
		$pointOne = new ProfilePoint('start');
		$pointTwo = new ProfilePoint('two', 1, 1);
		$points   = array($pointOne, $pointTwo);

		$profiler = new Profiler('bar', $renderer, $points, true);

		$this->assertAttributeSame('bar', 'name', $profiler);
		$this->assertAttributeSame($renderer, 'renderer', $profiler);
		$this->assertAttributeSame($points, 'points', $profiler);
		$this->assertAttributeSame(true, 'memoryRealUsage', $profiler);
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::setPoints
	 * @uses    \Joomla\Profiler\Profiler::__construct
	 * @uses    \Joomla\Profiler\Profiler::hasPoint
	 */
	public function testTheProfilerRegistersInjectedPointsCorrectly()
	{
		$point    = new ProfilePoint('start');
		$profiler = new Profiler('bar', null, array($point));

		$this->assertTrue($profiler->hasPoint('start'));
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::setPoints
	 * @uses    \Joomla\Profiler\Profiler::__construct
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testTheProfilerCannotRegisterMultipleInjectedPointsWithTheSameName()
	{
		$point1   = new ProfilePoint('start');
		$point2   = new ProfilePoint('start');
		$profiler = new Profiler('bar', null, array($point1, $point2));
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::setPoints
	 * @uses    \Joomla\Profiler\Profiler::__construct
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testTheProfilerCannotRegisterInjectedPointsNotImplementingThePointInterface()
	{
		$point1   = new ProfilePoint('start');
		$point2   = new \stdClass;
		$profiler = new Profiler('bar', null, array($point1, $point2));
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getName
	 */
	public function testTheProfilerNameIsReturned()
	{
		$this->assertEquals('test', $this->instance->getName());
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::mark
	 * @uses    \Joomla\Profiler\Profiler::hasPoint
	 */
	public function testTheProfilerMarksASinglePoint()
	{
		$this->instance->mark('one');

		$this->assertTrue($this->instance->hasPoint('one'));
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::mark
	 * @uses    \Joomla\Profiler\Profiler::hasPoint
	 */
	public function testTheProfilerMarksMultiplePoints()
	{
		$this->instance->mark('one');
		$this->instance->mark('two');

		$this->assertTrue($this->instance->hasPoint('one'));
		$this->assertTrue($this->instance->hasPoint('two'));

		// Assert the first point has a time and memory = 0
		$firstPoint = $this->instance->getPoint('one');

		$this->assertSame(0.0, $firstPoint->getTime());
		$this->assertSame(0, $firstPoint->getMemoryBytes());

		// Assert the other point has a time and memory > 0
		$secondPoint = $this->instance->getPoint('two');

		$this->assertGreaterThan(0, $secondPoint->getTime());
		$this->assertGreaterThan(0, $secondPoint->getMemoryBytes());
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::mark
	 *
	 * @expectedException  \LogicException
	 */
	public function testTheProfilerCannotMarkMultiplePointsWithTheSameName()
	{
		$this->instance->mark('test');
		$this->instance->mark('test');
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::hasPoint
	 * @uses    \Joomla\Profiler\Profiler::mark
	 */
	public function testTheProfilerChecksIfAPointHasBeenAdded()
	{
		$this->assertFalse($this->instance->hasPoint('test'));

		$this->instance->mark('test');

		$this->assertTrue($this->instance->hasPoint('test'));
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getPoint
	 * @uses    \Joomla\Profiler\Profiler::mark
	 */
	public function testTheProfilerRetrievesTheRequestedPoint()
	{
		$this->assertNull($this->instance->getPoint('foo'));

		$this->instance->mark('start');

		$point = $this->instance->getPoint('start');

		$this->assertInstanceOf('\Joomla\Profiler\ProfilePoint', $point);
		$this->assertEquals('start', $point->getName());
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getTimeBetween
	 * @uses    \Joomla\Profiler\Profiler::__construct
	 */
	public function testTheProfilerMeasuresTheTimeBetweenTwoPoints()
	{
		$first  = new ProfilePoint('start');
		$second = new ProfilePoint('stop', 1.5);

		$profiler = new Profiler('test', null, array($first, $second));

		$this->assertSame(1.5, $profiler->getTimeBetween('start', 'stop'));
		$this->assertSame(1.5, $profiler->getTimeBetween('stop', 'start'));
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getTimeBetween
	 * @uses    \Joomla\Profiler\Profiler::__construct
	 *
	 * @expectedException \LogicException
	 */
	public function testTheProfilerCannotMeasureTimeBetweenTwoPointsIfTheSecondPointDoesNotExist()
	{
		$first    = new ProfilePoint('start');
		$profiler = new Profiler('test', null, array($first));

		$profiler->getTimeBetween('start', 'bar');
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getTimeBetween
	 * @uses    \Joomla\Profiler\Profiler::__construct
	 *
	 * @expectedException \LogicException
	 */
	public function testTheProfilerCannotMeasureTimeBetweenTwoPointsIfTheFirstPointDoesNotExist()
	{
		$first    = new ProfilePoint('start');
		$profiler = new Profiler('test', null, array($first));

		$profiler->getTimeBetween('foo', 'start');
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getMemoryBytesBetween
	 * @uses    \Joomla\Profiler\Profiler::__construct
	 */
	public function testTheProfilerMeasuresTheMemoryUsedBetweenTwoPoints()
	{
		$first  = new ProfilePoint('start');
		$second = new ProfilePoint('stop', 0, 1000);

		$profiler = new Profiler('test', null, array($first, $second));

		$this->assertSame(1000, $profiler->getMemoryBytesBetween('start', 'stop'));
		$this->assertSame(1000, $profiler->getMemoryBytesBetween('stop', 'start'));
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getMemoryBytesBetween
	 * @uses    \Joomla\Profiler\Profiler::__construct
	 *
	 * @expectedException \LogicException
	 */
	public function testTheProfilerCannotMeasureMemoryBetweenTwoPointsIfTheSecondPointDoesNotExist()
	{
		$first    = new ProfilePoint('start');
		$profiler = new Profiler('test', null, array($first));

		$profiler->getMemoryBytesBetween('start', 'bar');
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getMemoryBytesBetween
	 * @uses    \Joomla\Profiler\Profiler::__construct
	 *
	 * @expectedException \LogicException
	 */
	public function testTheProfilerCannotMeasureMemoryBetweenTwoPointsIfTheFirstPointDoesNotExist()
	{
		$first    = new ProfilePoint('start');
		$profiler = new Profiler('test', null, array($first));

		$profiler->getMemoryBytesBetween('foo', 'start');
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getMemoryPeakBytes
	 */
	public function testTheProfilerReturnsThePeakMemoryUse()
	{
		$this->assertNull($this->instance->getMemoryPeakBytes());
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getPoints
	 */
	public function testTheProfilerReturnsTheMarkedPoints()
	{
		$this->assertEmpty($this->instance->getPoints());
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::setRenderer
	 */
	public function testTheProfilerCanHaveARendererInjected()
	{
		$renderer = new DefaultRenderer;

		$this->instance->setRenderer($renderer);

		$this->assertAttributeSame($renderer, 'renderer', $this->instance);
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getRenderer
	 */
	public function testTheProfilerReturnsTheRenderer()
	{
		$this->assertInstanceOf('\Joomla\Profiler\Renderer\DefaultRenderer', $this->instance->getRenderer());
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::render
	 * @uses    \Joomla\Profiler\Profiler::setRenderer
	 */
	public function testTheProfilerRendersItsData()
	{
		$mockedRenderer = $this->getMock('\Joomla\Profiler\ProfilerRendererInterface');
		$mockedRenderer->expects($this->once())
			->method('render')
			->with($this->instance);

		$this->instance->setRenderer($mockedRenderer);

		$this->instance->render();
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::__toString
	 * @uses    \Joomla\Profiler\Profiler::setRenderer
	 */
	public function testTheProfilerCanBeCastToAString()
	{
		$mockedRenderer = $this->getMock('\Joomla\Profiler\ProfilerRendererInterface');
		$mockedRenderer->expects($this->once())
			->method('render')
			->with($this->instance)
			->willReturn('Rendered profile');

		$this->instance->setRenderer($mockedRenderer);

		$this->assertSame('Rendered profile', (string) $this->instance);
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::getIterator
	 */
	public function testTheProfilerReturnsAnIterator()
	{
		// Create 3 points.
		$first  = new ProfilePoint('test');
		$second = new ProfilePoint('second', 1.5, 1000);
		$third  = new ProfilePoint('third', 2.5, 2000);
		$points = array($first, $second, $third);

		// Create a profiler and inject the points.
		$profiler = new Profiler('test', null, $points);

		$this->assertInstanceOf('\ArrayIterator', $profiler->getIterator());
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::count
	 */
	public function testTheProfilerCanBeCounted()
	{
		$this->assertCount(0, $this->instance);

		$this->instance->mark('start');
		$this->instance->mark('foo');
		$this->instance->mark('end');

		$this->assertCount(3, $this->instance);
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::setStart
	 */
	public function testTheProfilerStartTimeAndMemoryCanBeSet()
	{
		$time   = microtime(true);
		$memory = memory_get_usage(false);

		$this->instance->setStart($time, $memory);

		$this->assertAttributeSame($time, 'startTimeStamp', $this->instance);
		$this->assertAttributeSame($memory, 'startMemoryBytes', $this->instance);
		$this->assertCount(1, $this->instance);
	}

	/**
	 * @covers  \Joomla\Profiler\Profiler::setStart
	 *
	 * @expectedException  \RuntimeException
	 */
	public function testTheProfilerStartTimeAndMemoryCannotBeChangedIfAPointHasBeenMarked()
	{
		$time   = microtime(true);
		$memory = memory_get_usage(false);

		$this->instance->mark('test');
		$this->instance->setStart($time, $memory);
	}
}
