<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Registry\Tests\Format;

use Joomla\Registry\Format\Ini;

/**
 * Test class for \Joomla\Registry\Format\Ini.
 */
class IniTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox  A data object is converted to a string
	 *
	 * @covers   Joomla\Registry\Format\Ini::getValueAsINI
	 * @covers   Joomla\Registry\Format\Ini::objectToString
	 */
	public function testADataObjectIsConvertedToAString()
	{
		$class = new Ini;

		$object = new \stdClass;
		$object->foo = 'bar';
		$object->booleantrue = true;
		$object->booleanfalse = false;
		$object->numericint = 42;
		$object->numericfloat = 3.1415;
		$object->section = new \stdClass;
		$object->section->key = 'value';

		// Test basic object to string.
		$string = $class->objectToString($object, array('processSections' => true));

		$this->assertSame(
			"foo=\"bar\"\nbooleantrue=true\nbooleanfalse=false\nnumericint=42\nnumericfloat=3.1415\n\n[section]\nkey=\"value\"",
			trim($string)
		);
	}

	/**
	 * @testdox  A string is converted to a data object
	 *
	 * @covers   Joomla\Registry\Format\Ini::stringToObject
	 */
	public function testAStringIsConvertedToADataObject()
	{
		$class = new Ini;

		$string2 = "[section]\nfoo=bar";

		$object1 = new \stdClass;
		$object1->foo = 'bar';

		$object2 = new \stdClass;
		$object2->section = $object1;

		// Test INI format string without sections.
		$this->assertEquals($class->stringToObject($string2, array('processSections' => false)), $object1);

		// Test INI format string with sections.
		$this->assertEquals($class->stringToObject($string2, array('processSections' => true)), $object2);

		// Test empty string
		$this->assertEquals(new \stdClass, $class->stringToObject(null));

		$string3 = "[section]\nfoo=bar\n;Testcomment\nkey=value\n\n/brokenkey=)brokenvalue";
		$object2->section->key = 'value';

		$this->assertEquals($class->stringToObject($string3, array('processSections' => true)), $object2);

		$string4 = "boolfalse=false\nbooltrue=true\nkeywithoutvalue\nnumericfloat=3.1415\nnumericint=42\nkey=\"value\"";
		$object3 = new \stdClass;
		$object3->boolfalse = false;
		$object3->booltrue = true;
		$object3->numericfloat = 3.1415;
		$object3->numericint = 42;
		$object3->key = 'value';

		$this->assertEquals($class->stringToObject($string4), $object3);

		// Trigger the cache - Doing this only to achieve 100% code coverage. ;-)
		$this->assertEquals($class->stringToObject($string4), $object3);
	}

	/**
	 * @testdox  Validate data equality in converted objects
	 *
	 * @covers   Joomla\Registry\Format\Ini::objectToString
	 * @covers   Joomla\Registry\Format\Ini::stringToObject
	 */
	public function testDataEqualityInConvertedObjects()
	{
		$class = new Ini;

		$input = "[section1]\nboolfalse=false\nbooltrue=true\nnumericfloat=3.1415\nnumericint=42\nkey=\"value\"\n" .
			"arrayitem[]=\"item1\"\narrayitem[]=\"item2\"\n\n" .
			"[section2]\nboolfalse=false\nbooltrue=true\nnumericfloat=3.1415\nnumericint=42\nkey=\"value\"";

		$object = $class->stringToObject($input, array('processSections' => true, 'supportArrayValues' => true));
		$output = $class->objectToString($object, array('processSections' => true, 'supportArrayValues' => true));

		$this->assertEquals($input, $output, 'Input and output data must be equal.');
	}
}
