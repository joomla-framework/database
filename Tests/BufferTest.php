<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Buffer;
use PHPUnit\Framework\TestCase;

/**
 * Test class for JBuffer.
 *
 * @since  1.0
 */
class BufferTest extends TestCase
{
	/**
	 * @var    Buffer
	 * @since   1.4.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Buffer;
	}

	/**
	 * Test cases for the stream_open test
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 */
	public function casesOpen()
	{
		return array(
			'basic' => array(
				'http://www.example.com/fred',
				null,
				null,
				null,
				'www.example.com',
			),
		);
	}

	/**
	 * Test stream_open method.
	 *
	 * @param   string  $path         The path to buffer
	 * @param   string  $mode         The mode of the buffer
	 * @param   string  $options      The options
	 * @param   string  $opened_path  The path
	 * @param   string  $expected     The expected test return
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 * @dataProvider casesOpen
	 */
	public function testStreamOpen($path, $mode, $options, $opened_path, $expected)
	{
		$this->object->stream_open($path, $mode, $options, $opened_path);
		$this->assertThat(
			$expected,
			$this->equalTo($this->object->name)
		);
	}

	/**
	 * Test cases for the stream_read test
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 */
	public function casesRead()
	{
		return array(
			'basic' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				30,
				10,
				'EFGHIJKLMN',
			),
		);
	}

	/**
	 * Test stream_read method.
	 *
	 * @param   string   $buffer    The buffer to perform the operation upon
	 * @param   string   $name      The name of the buffer
	 * @param   integer  $position  The position in the buffer of the current pointer
	 * @param   integer  $count     The movement of the pointer
	 * @param   boolean  $expected  The expected test return
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 * @dataProvider casesRead
	 */
	public function testStreamRead($buffer, $name, $position, $count, $expected)
	{
		$this->object->name = $name;
		$this->object->position = $position;
		$this->object->buffers[$name] = $buffer;

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->stream_read($count))
		);
	}

	/**
	 * Test cases for the stream_write test
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 */
	public function casesWrite()
	{
		return array(
			'basic' => array(
				'abcdefghijklmnop',
				'www.example.com',
				5,
				'ABCDE',
				'abcdeABCDEklmnop',
			),
		);
	}

	/**
	 * Test stream_write method.
	 *
	 * @param   string   $buffer    The buffer to perform the operation upon
	 * @param   string   $name      The name of the buffer
	 * @param   integer  $position  The position in the buffer of the current pointer
	 * @param   string   $write     The data to write
	 * @param   boolean  $expected  The expected test return
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 * @dataProvider casesWrite
	 */
	public function testStreamWrite($buffer, $name, $position, $write, $expected)
	{
		$this->object->name = $name;
		$this->object->position = $position;
		$this->object->buffers[$name] = $buffer;
		$output = $this->object->stream_write($write);

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->buffers[$name])
		);
	}

	/**
	 * Test stream_tell method.
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 */
	public function testStreamTell()
	{
		$pos = 10;
		$this->object->position = $pos;

		$this->assertThat(
			$pos,
			$this->equalTo($this->object->stream_tell())
		);
	}

	/**
	 * Test cases for the stream_eof test
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 */
	public function casesEof()
	{
		return array(
			'~EOF' => array(
				'abcdefghijklmnop',
				'www.example.com',
				5,
				false,
			),
			'EOF' => array(
				'abcdefghijklmnop',
				'www.example.com',
				17,
				true,
			),
		);
	}

	/**
	 * Test stream_eof method.
	 *
	 * @param   string   $buffer    The buffer to perform the operation upon
	 * @param   string   $name      The name of the buffer
	 * @param   integer  $position  The position in the buffer of the current pointer
	 * @param   boolean  $expected  The expected test return
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 * @dataProvider casesEof
	 */
	public function testStreamEof($buffer, $name, $position, $expected)
	{
		$this->object->name = $name;
		$this->object->position = $position;
		$this->object->buffers[$name] = $buffer;

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->stream_eof())
		);
	}

	/**
	 * Test cases for the stream_seek test
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 */
	public function casesSeek()
	{
		return array(
			'basic' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				10,
				SEEK_SET,
				true,
				10,
			),
			'too_early' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-10,
				SEEK_SET,
				false,
				5,
			),
			'off_end' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				100,
				SEEK_SET,
				false,
				5,
			),
			'is_pos' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				10,
				SEEK_CUR,
				true,
				15,
			),
			'is_neg' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-100,
				SEEK_CUR,
				false,
				5,
			),
			'from_end' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-10,
				SEEK_END,
				true,
				42,
			),
			'before_beg' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-100,
				SEEK_END,
				false,
				5,
			),
			'bad_seek_code' => array(
				'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
				'www.example.com',
				5,
				-100,
				100,
				false,
				5,
			),
		);
	}

	/**
	 * Test stream_seek method.
	 *
	 * @param   string   $buffer       The buffer to perform the operation upon
	 * @param   string   $name         The name of the buffer
	 * @param   integer  $position     The position in the buffer of the current pointer
	 * @param   integer  $offset       The movement of the pointer
	 * @param   integer  $whence       The buffer seek op code
	 * @param   boolean  $expected     The expected test return
	 * @param   integer  $expectedPos  The new buffer position pointer
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 * @dataProvider casesSeek
	 */
	public function testStreamSeek($buffer, $name, $position, $offset, $whence, $expected, $expectedPos)
	{
		$this->object->name = $name;
		$this->object->position = $position;
		$this->object->buffers[$name] = $buffer;

		$this->assertThat(
			$expected,
			$this->equalTo($this->object->stream_seek($offset, $whence))
		);
		$this->assertThat(
			$expectedPos,
			$this->equalTo($this->object->position)
		);
	}
}
