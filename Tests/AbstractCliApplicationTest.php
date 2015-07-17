<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Tests;

/**
 * Test class for Joomla\Application\AbstractCliApplication.
 */
class AbstractCliApplicationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox  Tests the constructor creates default object instances
	 *
	 * @covers  Joomla\Application\AbstractCliApplication::__construct
	 */
	public function test__constructDefaultBehaviour()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractCliApplication');

		// Validate default objects are created
		$this->assertAttributeInstanceOf('Joomla\Input\Cli', 'input', $object);
		$this->assertAttributeInstanceOf('Joomla\Registry\Registry', 'config', $object);
		$this->assertAttributeInstanceOf('Joomla\Application\Cli\Output\Stdout', 'output', $object);

		// Validate default configuration data is written
		$executionDateTime = new \DateTime($object->get('execution.datetime'));

		$this->assertSame(date('Y'), $executionDateTime->format('Y'));
	}

	/**
	 * @testdox  Tests the correct objects are stored when injected
	 *
	 * @covers  Joomla\Application\AbstractCliApplication::__construct
	 */
	public function test__constructDependencyInjection()
	{
		$mockInput  = $this->getMock('Joomla\Input\Cli');
		$mockConfig = $this->getMock('Joomla\Registry\Registry');
		$mockOutput = $this->getMock('Joomla\Application\Cli\Output\Stdout');
		$object     = $this->getMockForAbstractClass('Joomla\Application\AbstractCliApplication', array($mockInput, $mockConfig, $mockOutput));

		$this->assertAttributeSame($mockInput, 'input', $object);
		$this->assertAttributeSame($mockConfig, 'config', $object);
		$this->assertAttributeSame($mockOutput, 'output', $object);
	}

	/**
	 * @testdox  Tests that a default CliOutput object is returned.
	 *
	 * @covers  Joomla\Application\AbstractCliApplication::getOutput
	 */
	public function testGetOutput()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractCliApplication');

		$this->assertInstanceOf('Joomla\Application\Cli\Output\Stdout', $object->getOutput());
	}

	/**
	 * @testdox  Tests that the application sends output successfully.
	 *
	 * @covers  Joomla\Application\AbstractCliApplication::out
	 */
	public function testOut()
	{
		$mockOutput = $this->getMock('Joomla\Application\Cli\Output\Stdout', array('out'));
		$mockOutput->expects($this->once())
			->method('out');

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractCliApplication', array(null, null, $mockOutput));

		$this->assertSame($object, $object->out('Testing'));
	}
}
