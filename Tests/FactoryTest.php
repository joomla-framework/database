<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Registry\Factory;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Registry\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * {@inheritdoc}
	 */
	protected function tearDown()
	{
		// Reset the internal cache
		TestHelper::setValue('Joomla\\Registry\\Factory', 'formatInstances', array());

		parent::tearDown();
	}

	/**
	 * @testdox  A format object is returned from the local Joomla namespace
	 *
	 * @covers   Joomla\Registry\Factory::getFormat
	 */
	public function testGetFormatFromLocalNamespace()
	{
		$this->assertInstanceOf(
			'Joomla\\Registry\\Format\\Ini',
			Factory::getFormat('ini')
		);
	}

	/**
	 * @testdox  A format object is returned from the requested namespace
	 *
	 * @covers   Joomla\Registry\Factory::getFormat
	 */
	public function testGetFormatFromRequestedNamespace()
	{
		$this->assertInstanceOf(
			'Joomla\\Registry\\Tests\\Stubs\\Ini',
			Factory::getFormat('ini', array('format_namespace' => __NAMESPACE__ . '\\Stubs'))
		);
	}

	/**
	 * @testdox  A format object is returned from the local namespace when not found in the requested namespace
	 *
	 * @covers   Joomla\Registry\Factory::getFormat
	 */
	public function testGetFormatFromLocalNamespaceWhenRequestedNamespaceDoesNotExist()
	{
		$this->assertInstanceOf(
			'Joomla\\Registry\\Format\\Json',
			Factory::getFormat('json', array('format_namespace' => __NAMESPACE__ . '\\Stubs'))
		);
	}

	/**
	 * @testdox  An exception is thrown if the requested format does not exist
	 *
	 * @covers             Joomla\Registry\Factory::getFormat
	 * @expectedException  \InvalidArgumentException
	 */
	public function testGetInstanceNonExistent()
	{
		Factory::getFormat('sql');
	}
}
