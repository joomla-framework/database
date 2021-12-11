<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for \Joomla\Model\StatefulModelTrait.
 */
class StatefulModelTraitTest extends TestCase
{
	/**
	 * @testdox  Calling getState() without a state set will throw an Exception
	 *
	 * @requires            PHP 5.4
	 * @covers              Joomla\Model\StatefulModelTrait::getState
	 * @expectedException   \UnexpectedValueException
	 */
	public function testgetStateException()
	{
		/** @var \Joomla\Model\StatefulModelTrait $object */
		$object = $this->getObjectForTrait('\\Joomla\\Model\\StatefulModelTrait');
		$object->getState();
	}

	/**
	 * @testdox  A Registry representing the state is set and retrieved
	 *
	 * @requires  PHP 5.4
	 * @covers    Joomla\Model\StatefulModelTrait::getState
	 * @covers    Joomla\Model\StatefulModelTrait::setState
	 */
	public function testSetAndgetState()
	{
		/** @var \Joomla\Model\StatefulModelTrait $object */
		$object = $this->getObjectForTrait('\\Joomla\\Model\\StatefulModelTrait');

		/** @var \Joomla\Registry\Registry $state */
		$state = $this->getMockBuilder('\\Joomla\\Registry\\Registry')
			->getMock();

		$object->setState($state);

		$this->assertSame($state, $object->getState());
	}
}
