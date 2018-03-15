<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests;

use Joomla\Registry\AbstractRegistryFormat;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Registry\AbstractRegistryFormat.
 */
class AbstractRegistryFormatTest extends TestCase
{
	/**
	 * Data provider for testGetInstance
	 *
	 * @return  array
	 */
	public function seedTestGetInstance()
	{
		return array(
			array('Xml'),
			array('Ini'),
			array('Json'),
			array('Php'),
			array('Yaml')
		);
	}

	/**
	 * @testdox  An instance of the format object is returned in the specified type.
	 *
	 * @param    string  $format  The format to load
	 *
	 * @covers   Joomla\Registry\AbstractRegistryFormat::getInstance
	 * @dataProvider  seedTestGetInstance
	 */
	public function testGetInstance($format)
	{
		$class = '\\Joomla\\Registry\\Format\\' . $format;

		$object = AbstractRegistryFormat::getInstance($format);
		$this->assertInstanceOf('Joomla\Registry\Format\\' . $format, AbstractRegistryFormat::getInstance($format));
	}

	/**
	 * @testdox  An Exception is thrown when retrieving a non-existing format.
	 *
	 * @covers   Joomla\Registry\AbstractRegistryFormat::getInstance
	 * @expectedException  \InvalidArgumentException
	 */
	public function testGetInstanceNonExistent()
	{
		AbstractRegistryFormat::getInstance('SQL');
	}
}
