<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Php;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Registry\Format\Php.
 */
class PhpTest extends TestCase
{
	/**
	 * @testdox  A data object is converted to a string
	 *
	 * @covers   Joomla\Registry\Format\Php::getArrayString
	 * @covers   Joomla\Registry\Format\Php::objectToString
	 */
	public function testADataObjectIsConvertedToAString()
	{
		$class = new Php;
		$options = array('class' => 'myClass');
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

		$string = "<?php\n" .
			"class myClass {\n" .
			"\tpublic \$foo = 'bar';\n" .
			"\tpublic \$quoted = '\"stringwithquotes\"';\n" .
			"\tpublic \$booleantrue = '1';\n" .
			"\tpublic \$booleanfalse = '';\n" .
			"\tpublic \$numericint = '42';\n" .
			"\tpublic \$numericfloat = '3.1415';\n" .
			"\tpublic \$section = array(\"key\" => \"value\");\n" .
			"\tpublic \$array = array(\"nestedarray\" => array(\"test1\" => \"value1\"));\n" .
			"}\n?>";

		$this->assertSame($string, $class->objectToString($object, $options));
	}

	/**
	 * @testdox  A data object is converted to a string with no specified class
	 *
	 * @covers   Joomla\Registry\Format\Php::getArrayString
	 * @covers   Joomla\Registry\Format\Php::objectToString
	 */
	public function testADataObjectIsConvertedToAStringWithNoSpecifiedClass()
	{
		$class = new Php;
		$object = new \stdClass;
		$object->foo = 'bar';
		$object->quoted = '"stringwithquotes"';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;

		// The PHP registry format does not support nested objects
		$object->section = new \stdClass;
		$object->section->key = 'value';
		$object->array = array('nestedarray' => array('test1' => 'value1'));

		$string = "<?php\n" .
			"class Registry {\n" .
			"\tpublic \$foo = 'bar';\n" .
			"\tpublic \$quoted = '\"stringwithquotes\"';\n" .
			"\tpublic \$booleantrue = '1';\n" .
			"\tpublic \$booleanfalse = '';\n" .
			"\tpublic \$numericint = '42';\n" .
			"\tpublic \$numericfloat = '3.1415';\n" .
			"\tpublic \$section = array(\"key\" => \"value\");\n" .
			"\tpublic \$array = array(\"nestedarray\" => array(\"test1\" => \"value1\"));\n" .
			"}\n?>";

		$this->assertSame($string, $class->objectToString($object));
	}

	/**
	 * @testdox  A data object is converted to a string with a namespace
	 *
	 * @covers   Joomla\Registry\Format\Php::getArrayString
	 * @covers   Joomla\Registry\Format\Php::objectToString
	 */
	public function testADataObjectIsConvertedToAStringWithANamespace()
	{
		$class = new Php;
		$options = array('class' => 'myClass', 'namespace' => 'Joomla\\Registry\\Test');
		$object = new \stdClass;
		$object->foo = 'bar';
		$object->quoted = '"stringwithquotes"';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;

		// The PHP registry format does not support nested objects
		$object->section = new \stdClass;
		$object->section->key = 'value';
		$object->array = array('nestedarray' => array('test1' => 'value1'));

		$string = "<?php\n" .
			"namespace Joomla\\Registry\\Test;\n\n" .
			"class myClass {\n" .
			"\tpublic \$foo = 'bar';\n" .
			"\tpublic \$quoted = '\"stringwithquotes\"';\n" .
			"\tpublic \$booleantrue = '1';\n" .
			"\tpublic \$booleanfalse = '';\n" .
			"\tpublic \$numericint = '42';\n" .
			"\tpublic \$numericfloat = '3.1415';\n" .
			"\tpublic \$section = array(\"key\" => \"value\");\n" .
			"\tpublic \$array = array(\"nestedarray\" => array(\"test1\" => \"value1\"));\n" .
			"}\n?>";

		$this->assertSame($string, $class->objectToString($object, $options));
	}

	/**
	 * @testdox  A string is converted to a data object
	 *
	 * @covers   Joomla\Registry\Format\Php::stringToObject
	 */
	public function testAStringIsConvertedToADataObject()
	{
		$class = new Php;

		// This method is not implemented in the class. The test is to achieve 100% code coverage
		$this->assertInstanceOf('stdClass', $class->stringToObject(''));
	}

	/**
	 * @testdox  Validate data equality in converted objects
	 *
	 * @covers   Joomla\Registry\Format\Php::objectToString
	 * @covers   Joomla\Registry\Format\Php::stringToObject
	 */
	public function testDataEqualityInConvertedObjects()
	{
		$this->markTestIncomplete('Method is not implemented in the class');

		$class = new Php;

		$input = "<?php\n" .
			"class myClass {\n" .
			"\tpublic \$foo = 'bar';\n" .
			"\tpublic \$quoted = '\"stringwithquotes\"';\n" .
			"\tpublic \$booleantrue = '1';\n" .
			"\tpublic \$booleanfalse = '';\n" .
			"\tpublic \$numericint = '42';\n" .
			"\tpublic \$numericfloat = '3.1415';\n" .
			"\tpublic \$section = array(\"key\" => \"value\");\n" .
			"\tpublic \$array = array(\"nestedarray\" => array(\"test1\" => \"value1\"));\n" .
			"}\n?>";

		$object = $class->stringToObject($input);
		$output = $class->objectToString($object);

		$this->assertEquals($input, $output, 'Input and output data must be equal.');
	}
}
