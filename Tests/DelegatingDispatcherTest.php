<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Event\Tests;

use Joomla\Event\DelegatingDispatcher;

/**
 * Tests for the DelegatingDispatcher class.
 *
 * @since  1.0
 */
class DelegatingDispatcherTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test the triggerEvent method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testTriggerEvent()
	{
		$event = 'onTest';

		/** @var \PHPUnit_Framework_MockObject_MockObject|\Joomla\Event\DispatcherInterface $mockedDispatcher */
		$mockedDispatcher = $this->getMockBuilder('Joomla\Event\DispatcherInterface')
			->setMethods(array('triggerEvent'))
			->getMock();

		$mockedDispatcher->expects($this->once())
			->method('triggerEvent')
			->with($event);

		$delegating = new DelegatingDispatcher($mockedDispatcher);

		$delegating->triggerEvent($event);
	}
}
