<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI\Tests;

use Joomla\DI\Container;

/**
 * Tests for ContainerAwareTrait class.
 *
 * @since   1.2
 * @covers  \Joomla\DI\ContainerAwareTrait
 */
class ContainerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Holds the Container instance for testing.
	 *
	 * @var    \Joomla\DI\ContainerAwareTrait
	 * @since  1.2
	 */
	protected $object;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public static function setUpBeforeClass()
	{
		// Only run tests on PHP 5.4+
		if (version_compare(PHP_VERSION, '5.4', '<'))
		{
			static::markTestSkipped('Tests are not present in PHP 5.4');
		}
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function setUp()
	{
		$this->object = $this->getObjectForTrait('\\Joomla\\DI\\ContainerAwareTrait');
	}

	/**
	 * Tear down the tests.
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function tearDown()
	{
		$this->object = null;
	}

	/**
	 * Tests calling getContainer() without a Container object set
	 *
	 * @return  void
	 *
	 * @since   1.2
	 * @coversDefaultClass  getContainer
	 * @expectedException   \UnexpectedValueException
	 */
	public function testGetContainerException()
	{
		$this->object->getContainer();
	}

	/**
	 * Tests calling getContainer() with a Container object set
	 *
	 * @return  void
	 *
	 * @since   1.2
	 * @coversDefaultClass  getContainer
	 */
	public function testGetContainer()
	{
		$reflection = new \ReflectionClass($this->object);
		$refProp = $reflection->getProperty('container');
		$refProp->setAccessible(true);
		$refProp->setValue($this->object, new Container);

		$this->assertInstanceOf(
			'\\Joomla\\DI\\Container',
			$this->object->getContainer(),
			'Validates the Container object was set.'
		);
	}

	/**
	 * Tests setting a Container object
	 *
	 * @return  void
	 *
	 * @since   1.2
	 * @coversDefaultClass  setContainer
	 */
	public function testSetContainer()
	{
		$this->object->setContainer(new Container);

		$reflection = new \ReflectionClass($this->object);
		$refProp = $reflection->getProperty('container');
		$refProp->setAccessible(true);
		$container = $refProp->getValue($this->object);

		$this->assertInstanceOf(
			'\\Joomla\\DI\\Container',
			$container,
			'Validates a Container object was retrieved.'
		);
	}
}
