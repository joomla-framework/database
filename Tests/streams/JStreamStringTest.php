<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Filesystem\Stream\String as StreamString;
use Joomla\Filesystem\Support\StringController;
use Joomla\Test\TestHelper;

/**
 * Test class for StreamString.
 *
 * @since  1.0
 */
class StreamStringTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var StreamString
	 *
	 * @since __VERSION_NO__
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	protected function setUp()
	{
		parent::setUp();

		$ref = 'lorem';
		$string = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit';

		StringController::createRef($ref, $string);
		$this->object = new StreamString;
	}

	/**
	 * Test stream_open method.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	public function testStream_open()
	{
		$null = '';

		$this->assertFalse(
			$this->object->stream_open('string://foo', null, null, $null)
		);

		$this->assertTrue(
			$this->object->stream_open('string://lorem', null, null, $null)
		);

		$this->assertEquals(
			StringController::getRef('lorem'),
			TestHelper::getValue($this->object, 'currentString')
		);

		$this->assertEquals(
			0,
			TestHelper::getValue($this->object, 'pos')
		);
	}

	/**
	 * Test stream_stat method.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	public function testStream_stat()
	{
		$string = 'foo bar';
		$now = time();
		$stat = array(
			'dev' => 0,
			'ino' => 0,
			'mode' => 0,
			'nlink' => 1,
			'uid' => 0,
			'gid' => 0,
			'rdev' => 0,
			'size' => strlen($string),
			'atime' => $now,
			'mtime' => $now,
			'ctime' => $now,
			'blksize' => '512',
			'blocks' => ceil(strlen($string) / 512));

		TestHelper::setValue($this->object, 'stat', $stat);

		$this->assertEquals(
			$stat,
			$this->object->stream_stat()
		);
	}

	/**
	 * Test url_stat method.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	public function testUrl_stat()
	{
		$url_stat = $this->object->url_stat('string://lorem');

		$string = StringController::getRef('lorem');
		$stat = array(
			'dev' => 0,
			'ino' => 0,
			'mode' => 0,
			'nlink' => 1,
			'uid' => 0,
			'gid' => 0,
			'rdev' => 0,
			'size' => strlen($string),
			'blksize' => '512',
			'blocks' => ceil(strlen($string) / 512));

		foreach ($stat as $key => $value)
		{
			$this->assertEquals(
				$value,
				$url_stat[$key]
			);
		}

		$url_stat = $this->object->url_stat('string://foo');

		$string = StringController::getRef('foo');
		$stat = array(
			'dev' => 0,
			'ino' => 0,
			'mode' => 0,
			'nlink' => 1,
			'uid' => 0,
			'gid' => 0,
			'rdev' => 0,
			'size' => strlen($string),
			'blksize' => '512',
			'blocks' => ceil(strlen($string) / 512));

		foreach ($stat as $key => $value)
		{
			$this->assertEquals(
				$value,
				$url_stat[$key]
			);
		}
	}

	/**
	 * Test stream_read method.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	public function testStream_read()
	{
		TestHelper::setValue($this->object, 'currentString', StringController::getRef('lorem'));

		$this->assertEquals(
			0,
			TestHelper::getValue($this->object, 'pos')
		);

		$this->assertEquals(
			'Lorem',
			$this->object->stream_read(5)
		);

		$this->assertEquals(
			5,
			TestHelper::getValue($this->object, 'pos')
		);
	}

	/**
	 * Test stream_write method.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	public function testStream_write()
	{
		$this->assertFalse(
			$this->object->stream_write('lorem')
		);
	}

	/**
	 * Test stream_tell method.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	public function testStream_tell()
	{
		TestHelper::setValue($this->object, 'pos', 11);

		$this->assertEquals(
			11,
			$this->object->stream_tell()
		);
	}

	/**
	 * Test stream_eof method.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	public function testStream_eof()
	{
		TestHelper::setValue($this->object, 'pos', 5);
		TestHelper::setValue($this->object, 'len', 6);

		$this->assertFalse(
			$this->object->stream_eof()
		);

		TestHelper::setValue($this->object, 'pos', 6);
		TestHelper::setValue($this->object, 'len', 6);

		$this->assertTrue(
			$this->object->stream_eof()
		);

		TestHelper::setValue($this->object, 'pos', 7);
		TestHelper::setValue($this->object, 'len', 6);

		$this->assertTrue(
			$this->object->stream_eof()
		);
	}

	/**
	 * Test data for test of stream_seek method.
	 *
	 * @return array
	 *
	 * @since __VERSION_NO__
	 */
	public function dataStream_seek()
	{
		return array(
			array(0, 0, 0, SEEK_SET, 0, true),
			array(0, 0, 0, SEEK_CUR, 0, true),
			array(0, 0, 0, SEEK_END, 0, true),
			array(0, 0, 7, SEEK_SET, 0, false),
			array(0, 0, 7, SEEK_CUR, 0, false),
			array(0, 0, 7, SEEK_END, 0, false),
			array(0, 5, 0, SEEK_SET, 0, true),
			array(0, 5, 0, SEEK_CUR, 0, true),
			array(0, 5, 0, SEEK_END, 5, true),
			array(0, 5, 2, SEEK_SET, 2, true),
			array(0, 5, 2, SEEK_CUR, 2, true),
			array(0, 5, 2, SEEK_END, 3, true),
			array(2, 5, 2, SEEK_SET, 2, true),
			array(2, 5, 2, SEEK_CUR, 4, true),
			array(2, 5, 2, SEEK_END, 3, true),
			array(2, 5, 5, SEEK_CUR, 2, false),
		);
	}

	/**
	 * Test stream_seek method.
	 *
	 * @param   int  $currPos    Current Position
	 * @param   int  $currLen    Current Length
	 * @param   int  $offset     Offset to seek
	 * @param   int  $whence     Seek type
	 * @param   int  $expPos     Expected pointer position
	 * @param   int  $expReturn  Expected return value
	 *
	 * @return void
	 *
	 * @dataProvider dataStream_seek
	 * @since __VERSION_NO__
	 */
	public function testStream_seek($currPos, $currLen, $offset, $whence, $expPos, $expReturn)
	{
		TestHelper::setValue($this->object, 'pos', $currPos);
		TestHelper::setValue($this->object, 'len', $currLen);

		$this->assertEquals(
			$expReturn,
			$this->object->stream_seek($offset, $whence)
		);

		$this->assertEquals(
			$expPos,
			TestHelper::getValue($this->object, 'pos')
		);
	}

	/**
	 * Test stream_flush method.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	public function testStream_flush()
	{
		$this->assertTrue(
			$this->object->stream_flush()
		);
	}
}
