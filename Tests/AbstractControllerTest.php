<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Controller\Tests;

use Joomla\Input\Input;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Controller\AbstractController class.
 */
class AbstractControllerTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  \Joomla\Controller\AbstractController
	 */
	private $instance;

	/**
	 * {@inheritdoc}
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->instance = $this->getMockForAbstractClass('Joomla\Controller\AbstractController');
	}

	/**
	 * @testdox  Tests the controller is instantiated correctly
	 *
	 * @covers  Joomla\Controller\AbstractController::__construct
	 */
	public function test__constructDefaultBehaviour()
	{
		$object = $this->getMockForAbstractClass('Joomla\Controller\AbstractController');

		// Both class attributes should be null
		$this->assertAttributeNotInstanceOf('Joomla\Application\AbstractApplication', 'app', $object);
		$this->assertAttributeNotInstanceOf('Joomla\Input\Input', 'input', $object);
	}

	/**
	 * @testdox  Tests the controller is instantiated correctly
	 *
	 * @covers  Joomla\Controller\AbstractController::__construct
	 */
	public function test__constructDependencyInjection()
	{
		$mockInput = $this->getMockBuilder('Joomla\Input\Input')
			->getMock();

		$mockApp = $this->getMockForAbstractClass('Joomla\Application\AbstractApplication');
		$object  = $this->getMockForAbstractClass('Joomla\Controller\AbstractController', array($mockInput, $mockApp));

		$this->assertAttributeSame($mockInput, 'input', $object);
		$this->assertAttributeSame($mockApp, 'app', $object);
	}

	/**
	 * @testdox  Tests the controller throws an UnexpectedValueException if the application is not registered
	 *
	 * @covers  Joomla\Controller\AbstractController::getApplication
	 * @expectedException  \UnexpectedValueException
	 */
	public function testGetApplicationThrowsAnException()
	{
		$this->instance->getApplication();
	}

	/**
	 * @testdox  Tests the controller throws an UnexpectedValueException if the input object is not registered
	 *
	 * @covers  Joomla\Controller\AbstractController::getInput
	 * @expectedException  \UnexpectedValueException
	 */
	public function testGetInputThrowsAnException()
	{
		$this->instance->getInput();
	}

	/**
	 * @testdox  Tests the controller is serialized correctly
	 *
	 * @covers  Joomla\Controller\AbstractController::serialize
	 * @uses    Joomla\Controller\AbstractController::setInput
	 */
	public function testSerialize()
	{
		$mockInput = $this->getMockBuilder('Joomla\Input\Input')
			->enableOriginalConstructor()
			->enableProxyingToOriginalMethods()
			->getMock();

		$this->instance->setInput($mockInput);

		$this->assertContains('C:19:"Mock_Input_', $this->instance->serialize());
	}

	/**
	 * @testdox  Tests the controller is unserialized correctly
	 *
	 * @covers  Joomla\Controller\AbstractController::unserialize
	 * @uses    Joomla\Controller\AbstractController::getInput
	 */
	public function testUnserialize()
	{
		// Use a real Input object for this test
		$input = new Input;

		$serialized = serialize($input);

		$this->assertSame($this->instance, $this->instance->unserialize($serialized), 'Checks chaining and target method.');

		$this->assertInstanceOf('Joomla\Input\Input', $this->instance->getInput());
	}

	/**
	 * @testdox  Tests the controller throws an UnexpectedValueException when the serialized string is not an Input object
	 *
	 * @covers  Joomla\Controller\AbstractController::unserialize
	 * @expectedException  UnexpectedValueException
	 */
	public function testUnserializeThrowsAnException()
	{
		$this->instance->unserialize('s:7:"default";');
	}

	/**
	 * @testdox  Tests an application object is injected into the controller and retrieved correctly
	 *
	 * @covers  Joomla\Controller\AbstractController::getApplication
	 * @covers  Joomla\Controller\AbstractController::setApplication
	 */
	public function testSetAndGetApplication()
	{
		$mockApp = $this->getMockForAbstractClass('Joomla\Application\AbstractApplication');

		$this->instance->setApplication($mockApp);
		$this->assertSame($mockApp, $this->instance->getApplication());
	}

	/**
	 * @testdox  Tests an input object is injected into the controller and retrieved correctly
	 *
	 * @covers  Joomla\Controller\AbstractController::getInput
	 * @covers  Joomla\Controller\AbstractController::setInput
	 */
	public function testSetAndGetInput()
	{
		$mockInput = $this->getMockBuilder('Joomla\Input\Input')
			->getMock();

		$this->instance->setInput($mockInput);
		$this->assertSame($mockInput, $this->instance->getInput());
	}
}
