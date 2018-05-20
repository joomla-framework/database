<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Application\AbstractCliApplication.
 */
class AbstractCliApplicationTest extends TestCase
{
	/**
	 * @testdox  Tests the constructor creates default object instances
	 *
	 * @covers  Joomla\Application\AbstractCliApplication::__construct
	 */
	public function test__constructDefaultBehaviour()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractCliApplication');

		// Validate default objects unique to the CLI application are created
		$this->assertAttributeInstanceOf('Joomla\Input\Cli', 'input', $object);
		$this->assertAttributeInstanceOf('Joomla\Application\Cli\Output\Stdout', 'output', $object);
		$this->assertAttributeInstanceOf('Joomla\Application\Cli\CliInput', 'cliInput', $object);
	}

	/**
	 * @testdox  Tests the correct objects are stored when injected
	 *
	 * @covers  Joomla\Application\AbstractCliApplication::__construct
	 */
	public function test__constructDependencyInjection()
	{
		$mockInput    = $this->getMockBuilder('Joomla\Input\Cli')->getMock();
		$mockConfig   = $this->getMockBuilder('Joomla\Registry\Registry')->getMock();;
		$mockOutput   = $this->getMockBuilder('Joomla\Application\Cli\Output\Stdout')->getMock();;
		$mockCliInput = $this->getMockBuilder('Joomla\Application\Cli\CliInput')->getMock();;

		$object = $this->getMockForAbstractClass(
			'Joomla\Application\AbstractCliApplication', array($mockInput, $mockConfig, $mockOutput, $mockCliInput)
		);

		$this->assertAttributeSame($mockInput, 'input', $object);
		$this->assertAttributeSame($mockConfig, 'config', $object);
		$this->assertAttributeSame($mockOutput, 'output', $object);
		$this->assertAttributeSame($mockCliInput, 'cliInput', $object);
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
	 * @testdox  Tests that a default CliInput object is returned.
	 *
	 * @covers  Joomla\Application\AbstractCliApplication::getCliInput
	 */
	public function testGetCliInput()
	{
		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractCliApplication');

		$this->assertInstanceOf('Joomla\Application\Cli\CliInput', $object->getCliInput());
	}

	/**
	 * @testdox  Tests that the application sends output successfully.
	 *
	 * @covers  Joomla\Application\AbstractCliApplication::out
	 */
	public function testOut()
	{
		$mockOutput = $this->getMockBuilder('Joomla\Application\Cli\Output\Stdout')
			->setMethods(array('out'))
			->getMock();
		$mockOutput->expects($this->once())
			->method('out');

		$object = $this->getMockForAbstractClass('Joomla\Application\AbstractCliApplication', array(null, null, $mockOutput));

		$this->assertSame($object, $object->out('Testing'));
	}
}
