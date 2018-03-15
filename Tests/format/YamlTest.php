<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Yaml;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Registry\Format\Yaml.
 */
class YamlTest extends TestCase
{
	/**
	 * Object being tested
	 *
	 * @var  Yaml
	 */
	private $fixture;

	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	public function setUp()
	{
		$this->fixture = new Yaml;
	}

	/**
	 * @testdox  The formatter is instantiated correctly
	 *
	 * @covers   Joomla\Registry\Format\Yaml::__construct
	 */
	public function testConstruct()
	{
		$this->assertAttributeInstanceOf('Symfony\Component\Yaml\Parser', 'parser', $this->fixture);
		$this->assertAttributeInstanceOf('Symfony\Component\Yaml\Dumper', 'dumper', $this->fixture);
	}

	/**
	 * @testdox  A data object is converted to a string
	 *
	 * @covers   Joomla\Registry\Format\Yaml::objectToString
	 */
	public function testADataObjectIsConvertedToAString()
	{
		$object = (object) array(
			'foo' => 'bar',
			'quoted' => '"stringwithquotes"',
			'booleantrue' => true,
			'booleanfalse' => false,
			'numericint' => 42,
			'numericfloat' => 3.1415,
			'section' => (object) array('key' => 'value'),
			'array' => (object) array('nestedarray' => (object) array('test1' => 'value1'))
		);

		$yaml = 'foo: bar
quoted: \'"stringwithquotes"\'
booleantrue: true
booleanfalse: false
numericint: 42
numericfloat: 3.1415
section:
    key: value
array:
    nestedarray: { test1: value1 }
';

		$this->assertEquals(
			str_replace(array("\n", "\r"), '', trim($this->fixture->objectToString($object))),
			str_replace(array("\n", "\r"), '', trim($yaml))
		);
	}

	/**
	 * @testdox  An array is converted to a string
	 *
	 * @covers   Joomla\Registry\Format\Yaml::objectToString
	 */
	public function testAnArrayIsConvertedToAString()
	{
		$object = array(
			'foo' => 'bar',
			'quoted' => '"stringwithquotes"',
			'booleantrue' => true,
			'booleanfalse' => false,
			'numericint' => 42,
			'numericfloat' => 3.1415,
			'section' => array('key' => 'value'),
			'array' => array('nestedarray' => array('test1' => 'value1'))
		);

		$yaml = 'foo: bar
quoted: \'"stringwithquotes"\'
booleantrue: true
booleanfalse: false
numericint: 42
numericfloat: 3.1415
section:
    key: value
array:
    nestedarray: { test1: value1 }
';

		$this->assertEquals(
			str_replace(array("\n", "\r"), '', trim($this->fixture->objectToString($object))),
			str_replace(array("\n", "\r"), '', trim($yaml))
		);
	}

	/**
	 * @testdox  A string is converted to a data object
	 *
	 * @covers   Joomla\Registry\Format\Yaml::stringToObject
	 */
	public function testAStringIsConvertedToADataObject()
	{
		$object = (object) array(
			'foo' => 'bar',
			'quoted' => '"stringwithquotes"',
			'booleantrue' => true,
			'booleanfalse' => false,
			'numericint' => 42,
			'numericfloat' => 3.1415,
			'section' => (object) array('key' => 'value'),
			'array' => (object) array('nestedarray' => (object) array('test1' => 'value1'))
		);

		$yaml = 'foo: bar
quoted: \'"stringwithquotes"\'
booleantrue: true
booleanfalse: false
numericint: 42
numericfloat: 3.1415
section:
    key: value
array:
    nestedarray: { test1: value1 }
';
		$this->assertEquals($object, $this->fixture->stringToObject($yaml));
	}

	/**
	 * @testdox  Validate data equality in converted objects
	 *
	 * @covers   Joomla\Registry\Format\Yaml::objectToString
	 * @covers   Joomla\Registry\Format\Yaml::stringToObject
	 */
	public function testDataEqualityInConvertedObjects()
	{
		$input = "foo: bar\nquoted: '\"stringwithquotes\"'\nbooleantrue: true\nbooleanfalse: false\nnumericint: 42\nnumericfloat: 3.1415\n" .
				"section:\n    key: value\narray:\n    nestedarray: { test1: value1 }\n";

		$object = $this->fixture->stringToObject($input);
		$output = $this->fixture->objectToString($object);

		$this->assertEquals($input, $output, 'Input and output data must be equal.');
	}
}
