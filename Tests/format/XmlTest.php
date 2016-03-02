<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Xml;

/**
 * Test class for \Joomla\Registry\Format\Xml.
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox  A data object is converted to a string
	 *
	 * @covers   Joomla\Registry\Format\Xml::getXmlChildren
	 * @covers   Joomla\Registry\Format\Xml::objectToString
	 */
	public function testADataObjectIsConvertedToAString()
	{
		$class = new Xml;

		$object = new \stdClass;
		$object->foo = 'bar';
		$object->quoted = '"stringwithquotes"';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;
		$object->section = new \stdClass;
		$object->section->key = 'value';
		$object->array = array('nestedarray' => array('test1' => 'value1'));

		// Check for different PHP behavior of displaying boolean false in XML.
		$checkFalse = '<check/>' == simplexml_load_string('<test/>')->addChild('check', false)->asXML()
			? '/>'
			: '></node>';

		$string = "<?xml version=\"1.0\"?>\n<registry>" .
			"<node name=\"foo\" type=\"string\">bar</node>" .
			"<node name=\"quoted\" type=\"string\">\"stringwithquotes\"</node>" .
			"<node name=\"booleantrue\" type=\"boolean\">1</node>" .
			"<node name=\"booleanfalse\" type=\"boolean\"" . $checkFalse .
			"<node name=\"numericint\" type=\"integer\">42</node>" .
			"<node name=\"numericfloat\" type=\"double\">3.1415</node>" .
			"<node name=\"section\" type=\"object\">" .
			"<node name=\"key\" type=\"string\">value</node>" .
			"</node>" .
			"<node name=\"array\" type=\"array\">" .
			"<node name=\"nestedarray\" type=\"array\">" .
			"<node name=\"test1\" type=\"string\">value1</node>" .
			"</node>" .
			"</node>" .
			"</registry>\n";

		// Test basic object to string.
		$this->assertSame($string, $class->objectToString($object));
	}

	/**
	 * @testdox  A string is converted to a data object
	 *
	 * @covers   Joomla\Registry\Format\Xml::getValueFromNode
	 * @covers   Joomla\Registry\Format\Xml::stringToObject
	 */
	public function testAStringIsConvertedToADataObject()
	{
		$class = new Xml;

		$object = new \stdClass;
		$object->foo = 'bar';
		$object->booleantrue = true;
		$object->booleanfalse1 = false;
		$object->booleanfalse2 = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;
		$object->section = new \stdClass;
		$object->section->key = 'value';
		$object->array = array('test1' => 'value1');

		$string = "<?xml version=\"1.0\"?>\n<registry>" .
			"<node name=\"foo\" type=\"string\">bar</node>" .
			"<node name=\"booleantrue\" type=\"boolean\">1</node>" .
			"<node name=\"booleanfalse1\" type=\"boolean\"></node>" .
			"<node name=\"booleanfalse2\" type=\"boolean\"/>" .
			"<node name=\"numericint\" type=\"integer\">42</node>" .
			"<node name=\"numericfloat\" type=\"double\">3.1415</node>" .
			"<node name=\"section\" type=\"object\">" .
			"<node name=\"key\" type=\"string\">value</node>" .
			"</node>" .
			"<node name=\"array\" type=\"array\">" .
			"<node name=\"test1\" type=\"string\">value1</node>" .
			"</node>" .
			"</registry>\n";

		// Test basic object to string.
		$this->assertEquals($object, $class->stringToObject($string));
	}

	/**
	 * @testdox  Validate data equality in converted objects
	 *
	 * @covers   Joomla\Registry\Format\Xml::objectToString
	 * @covers   Joomla\Registry\Format\Xml::stringToObject
	 */
	public function testDataEqualityInConvertedObjects()
	{
		$class = new Xml;

		// Check for different PHP behavior of displaying boolean false in XML.
		$checkFalse = '<check/>' == simplexml_load_string('<test/>')->addChild('check', false)->asXML()
			? '/>'
			: '></node>';

		$input = "<?xml version=\"1.0\"?>\n<registry>" .
			"<node name=\"foo\" type=\"string\">bar</node>" .
			"<node name=\"booleantrue\" type=\"boolean\">1</node>" .
			"<node name=\"booleanfalse\" type=\"boolean\"" . $checkFalse .
			"<node name=\"numericint\" type=\"integer\">42</node>" .
			"<node name=\"numericfloat\" type=\"double\">3.1415</node>" .
			"<node name=\"section\" type=\"object\">" .
			"<node name=\"key\" type=\"string\">value</node>" .
			"</node>" .
			"<node name=\"array\" type=\"array\">" .
			"<node name=\"test1\" type=\"string\">value1</node>" .
			"</node>" .
			"</registry>\n";

		$object = $class->stringToObject($input);
		$output = $class->objectToString($object);

		$this->assertEquals($input, $output, 'Input and output data must be equal.');
	}
}
