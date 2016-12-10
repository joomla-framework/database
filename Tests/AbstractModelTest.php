<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model\Tests;

use Joomla\Registry\Registry;

/**
 * Tests for the Joomla\Model\AbstractModel class.
 *
 * @since  1.0
 */
class AbstractModelTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    \Joomla\Model\AbstractModel
	 * @since  1.0
	 */
	private $instance;

	/**
	 * Tests the __construct method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Model\AbstractModel::__construct
	 * @since   1.0
	 */
	public function test__construct()
	{
		$this->assertEquals(new Registry, $this->instance->getState(), 'Checks default state.');

		$state = new Registry(array('foo' => 'bar'));

		$class = $this->getMockBuilder('Joomla\\Model\\AbstractModel')
			->setConstructorArgs(array($state))
			->getMockForAbstractClass();

		$this->assertEquals($state, $class->getState(), 'Checks state injection.');
	}

	/**
	 * Tests the setState method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Model\AbstractModel::getState
	 * @covers  Joomla\Model\AbstractModel::setState
	 * @since   1.0
	 */
	public function testSetState()
	{
		$state = new Registry(array('foo' => 'bar'));
		$this->instance->setState($state);
		$this->assertSame($state, $this->instance->getState());
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		$this->instance = $this->getMockBuilder('Joomla\\Model\\AbstractModel')
			->getMockForAbstractClass();
	}
}
