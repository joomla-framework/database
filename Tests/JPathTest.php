<?php
/**
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Filesystem\Path;

/**
 * Tests for the Path class.
 *
 * @since  1.0
 */
class PathTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test...
	 *
	 * @todo Implement testCanChmod().
	 *
	 * @return void
	 */
	public function testCanChmod()
	{
		$path = __DIR__ . '/tmp/canChangePermission';
		file_put_contents($path, "Joomla");

		$this->assertFalse(
			Path::canChmod('/')
		);

		$this->assertFalse(
			Path::canChmod('/foobar')
		);

		$this->assertTrue(
			Path::canChmod($path)
		);

		unlink($path);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testSetPermissions().
	 *
	 * @return void
	 */
	public function testSetPermissions()
	{
		$path = __DIR__ . '/tmp/setPermission';
		file_put_contents($path, "Joomla");
		chmod($path, 0644);

		$this->assertFalse(
			Path::setPermissions('/var/www')
		);

		$this->assertFalse(
			Path::setPermissions('/foobar')
		);

		$this->assertEquals(
			0777,
			Path::setPermissions($path, 0777)
		);

		unlink($path);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGetPermissions().
	 *
	 * @return void
	 */
	public function testGetPermissions()
	{
		$this->assertEquals(
			'---------',
			Path::getPermissions('/foobar')
		);

		$path = __DIR__ . '/tmp/setPermission';

		file_put_contents($path, "Joomla");
		chmod($path, 0777);
		$this->assertEquals(
			'rwxrwxrwx',
			Path::getPermissions($path)
		);
		unlink($path);

		file_put_contents($path, "Joomla");
		chmod($path, 0644);
		$this->assertEquals(
			'rw-r--r--',
			Path::getPermissions($path)
		);
		unlink($path);
	}

	public function dataCheckValidPaths()
	{
		return array(
			array('/var/foo/bar'),
			array('/var/fo.o/bar'),
			array('/var/./bar'),
			array('/var/foo..bar'),
			array('/var/foo/..bar'),
			array('/var/foo../bar'),
			array('/var/foo/bar..'),
			array('/var/..foo./bar'),
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testCheck().
	 * @dataProvider dataCheckValidPaths
	 * @return void
	 */
	public function testCheckValidPaths($data)
	{
		$this->assertEquals(
			__DIR__ . $data,
			Path::check(__DIR__ . $data)
		);
	}

	public function dataCheckExceptionPaths()
	{
		return array(
			array('../var/foo/bar'),
			array('/var/../foo/bar'),
			array('/var/foo/..'),
			array('/var/foo'),
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testCheck().
	 *
	 * @dataProvider dataCheckExceptionPaths
	 * @return void
	 * @expectedException Exception
	 */
	public function testCheckExceptionPaths($data)
	{
		Path::check($data);
	}

	/**
	 * Data provider for testClean() method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getCleanData()
	{
		return array(
			// Input Path, Directory Separator, Expected Output
			'Nothing to do.' => array('/var/www/foo/bar/baz', '/', '/var/www/foo/bar/baz'),
			'Return JPATH_ROOT.' => array(' ', '/', JPATH_ROOT),
			'One backslash.' => array('/var/www/foo\\bar/baz', '/', '/var/www/foo/bar/baz'),
			'Two and one backslashes.' => array('/var/www\\\\foo\\bar/baz', '/', '/var/www/foo/bar/baz'),
			'Mixed backslashes and double forward slashes.' => array('/var\\/www//foo\\bar/baz', '/', '/var/www/foo/bar/baz'),
			'UNC path.' => array('\\\\www\\docroot', '\\', '\\\\www\\docroot'),
			'UNC path with forward slash.' => array('\\\\www/docroot', '\\', '\\\\www\\docroot'),
			'UNC path with UNIX directory separator.' => array('\\\\www/docroot', '/', '/www/docroot'),
		);
	}

	/**
	 * Tests the clean method.
	 *
	 * @param   string  $input     @todo
	 * @param   string  $ds        @todo
	 * @param   string  $expected  @todo
	 *
	 * @return  void
	 *
	 * @covers        Joomla\Filesystem\Path::clean
	 * @dataProvider  getCleanData
	 * @since      1.0
	 */
	public function testClean($input, $ds, $expected)
	{
		$this->assertEquals(
			$expected,
			Path::clean($input, $ds)
		);
	}

	/**
	 * Tests the JPath::clean method with an array as an input.
	 *
	 * @return  void
	 *
	 * @expectedException  UnexpectedValueException
	 */
	public function testCleanArrayPath()
	{
		Path::clean(array('/path/to/folder'));
	}

	/**
	 * Test...
	 *
	 * @todo Implement testIsOwner().
	 *
	 * @return void
	 */
	public function testIsOwner()
	{
		$path = __DIR__ . '/tmp/setPermission';
		file_put_contents($path, "Joomla");

		$this->assertFalse(
			Path::isOwner('/')
		);

		$this->assertTrue(
			Path::isOwner($path)
		);

		unlink($path);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testFind().
	 *
	 * @return void
	 */
	public function testFind()
	{
		$this->assertNotEquals(
			__FILE__,
			Path::find(dirname(__DIR__), 'JPathTest.php')
		);

		$this->assertEquals(
			__FILE__,
			Path::find(__DIR__, 'JPathTest.php')
		);
	}
}
