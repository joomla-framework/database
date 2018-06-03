<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Stream;
use Joomla\Filesystem\Support\StringController;
use Joomla\Test\TestHelper;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Stream.
 *
 * @since  1.0
 */
class StreamTest extends TestCase
{
	const WRITE_PREFIX = 'WRITE_PREFIX/';
	const READ_PREFIX = 'READ_PREFIX/';

	/**
	 * @var    Stream
	 * @since  __DEPLOY_VERSION__
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Stream(self::WRITE_PREFIX, self::READ_PREFIX);
		vfsStream::setup('root');
	}

	/**
	 * Test counstructor method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function test__construct()
	{
		$object = new Stream('foo', 'bar');

		$this->assertEquals(
			'foo',
			TestHelper::getValue($object, 'writeprefix')
		);

		$this->assertEquals(
			'bar',
			TestHelper::getValue($object, 'readprefix')
		);

		$this->assertEquals(
			0,
			count(TestHelper::getValue($object, 'contextOptions'))
		);

		$this->assertEquals(
			null,
			TestHelper::getValue($object, 'context')
		);
	}

	/**
	 * Tests getStream method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGetStream()
	{
		$object = Stream::getStream();

		$this->assertInstanceOf(
			'Joomla\\Filesystem\\Stream',
			$object,
			'getStream must return an instance of Joomla\\Filesystem\\Stream'
		);

		$this->assertEquals(
			dirname(__DIR__) . '/',
			TestHelper::getValue($object, 'writeprefix')
		);

		$this->assertEquals(
			dirname(__DIR__),
			TestHelper::getValue($object, 'readprefix')
		);

		$object = Stream::getStream(false);

		$this->assertInstanceOf(
			'Joomla\\Filesystem\\Stream',
			$object,
			'getStream must return an instance of Joomla\\Filesystem\\Stream'
		);

		$this->assertEquals(
			'',
			TestHelper::getValue($object, 'writeprefix')
		);

		$this->assertEquals(
			'',
			TestHelper::getValue($object, 'readprefix')
		);
	}

	/**
	 * Test open method with no filename.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testOpenNoFilenameException()
	{
		$this->object->open('');
	}

	/**
	 * Test open method with invalid filename.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testOpenInvlaidFilenameException()
	{
		$this->object->open('foobar');
	}

	/**
	 * Test open method with invalid string name.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testOpenInvlaidStringnameException()
	{
		$this->object->open('string://bbarfoo');
	}

	/**
	 * Test open method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testOpen()
	{
		// Test simple file open
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);

		$this->assertEquals(
			$filename,
			TestHelper::getValue($this->object, 'filename')
		);

		$this->assertEquals(
			'r',
			TestHelper::getValue($this->object, 'openmode')
		);

		$this->assertEquals(
			'f',
			TestHelper::getValue($this->object, 'processingmethod')
		);

		$this->assertEquals(
			'resource',
			gettype(TestHelper::getValue($this->object, 'fh'))
		);

		$this->object->close();
		unlink($filename);

		// Test custom stream open
		$string = "Lorem ipsum dolor sit amet";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertEquals(
			$filename,
			TestHelper::getValue($this->object, 'filename')
		);

		$this->object->close();
	}

	/**
	 * Test closing of a stream before opening it.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testCloseBeforeOpeningException()
	{
		$object = new Stream;

		$object->close();
	}

	/**
	 * Test eof not found exception.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testEofNotOpenException()
	{
		$this->object->eof();
	}

	/**
	 * Test eof method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testEof()
	{
		$string = "Lorem ipsum dolor sit amet";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertFalse(
			$this->object->eof()
		);

		$this->object->read(strlen($string));

		$this->assertTrue(
			$this->object->eof()
		);

		$this->object->close();
	}

	/**
	 * Test file size method exception - File not open.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testFilesizeNotOpen()
	{
		$this->object->filesize();
	}

	/**
	 * Test filesize method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testFilesize()
	{
		$string = "Lorem ipsum dolor sit amet";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertEquals(
			strlen($string),
			$this->object->filesize()
		);

		$this->object->close();

		// Skip remote tests
		if (false)
		{
			$this->object->open('https://www.joomla.org');

			$this->assertTrue(
				is_numeric($this->object->filesize())
			);

			$this->object->close();
		}
	}

	/**
	 * Test gets method's stream not open exception.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGetsNotOpen()
	{
		$this->object->gets();
	}

	/**
	 * Test gets method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGets()
	{
		$string = "Lorem ipsum dolor sit amet.\nFoo bar";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertEquals(
			"Lorem ipsum dolor sit amet.\n",
			$this->object->gets()
		);

		$this->assertEquals(
			"Foo",
			$this->object->gets(4)
		);

		$this->object->close();
	}

	/**
	 * Test gets invalid length exception.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGetsInvalidLength()
	{
		$string = "Lorem ipsum dolor sit amet.\nFoo bar";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->object->gets(1);

		$this->object->close();
	}

	/**
	 * Test read method's stream not open exception.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testReadNotOpen()
	{
		$this->object->read();
	}

	/**
	 * Test read method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testRead()
	{
		$string = "Lorem ipsum dolor sit amet.\nFoo bar";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);

		$this->assertEquals(
			"L",
			$this->object->read(1)
		);

		$this->assertEquals(
			"orem ipsum dolor sit amet.\nFoo bar",
			$this->object->read()
		);

		$this->object->close();
	}

	/**
	 * Test seek method stream not open exception.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testSeekNotOpen()
	{
		$this->object->seek(0);
	}

	/**
	 * Test data for seek test.
	 *
	 * @return array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function dataSeek()
	{
		return array(
			array(0, 0, SEEK_SET, 0),
			array(0, 0, SEEK_CUR, 0),
			array(0, 0, SEEK_END, 35),
			array(0, 5, SEEK_SET, 5),
			array(0, 5, SEEK_CUR, 5),
			array(0, 5, SEEK_END, 30),
			array(5, 5, SEEK_SET, 5),
			array(5, 5, SEEK_CUR, 10),
			array(5, 5, SEEK_END, 30),
		);
	}

	/**
	 * Test seek method.
	 *
	 * @param   int  $initial  Intial position of the pointer
	 * @param   int  $offset   Offset to seek
	 * @param   int  $whence   Seek type
	 * @param   int  $expPos   Expected pointer position
	 *
	 * @return  void
	 *
	 * @dataProvider dataSeek
	 * @since   __DEPLOY_VERSION__
	 */
	public function testSeek($initial, $offset, $whence, $expPos)
	{
		$string = "Lorem ipsum dolor sit amet.\nFoo bar";
		StringController::createRef('lorem', $string);
		$filename = 'string://lorem';

		$this->object->open($filename);
		$this->object->seek($initial, SEEK_SET);

		$this->assertTrue(
			$this->object->seek($offset, $whence)
		);

		$this->assertEquals(
			$expPos,
			$this->object->tell()
		);

		$this->object->close();
	}

	/**
	 * Test tell method stream not open exception.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testTellNotOpen()
	{
		$this->object->tell();
	}

	/**
	 * Test write method stream not open exception.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testWriteNotOpen()
	{
		$data = 'foobar';
		$this->object->write($data);
	}

	/**
	 * Test write method with readonly mode excepton.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testWriteReadonly()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$object = Stream::getStream();

		// Open stream in reading mode.
		$object->open($filename);

		$data = 'foobar';
		$this->assertTrue($object->write($data));

		$object->close();

		unlink($filename);
	}

	/**
	 * Test write method.
	 *
	 * @return  void
	 *
	 * @requires PHP 5.4
	 * @since   __DEPLOY_VERSION__
	 */
	public function testWrite()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$filename = $path . '/' . $name;

		$object = Stream::getStream();
		$object->open($filename, 'w');

		$data = 'foobar';
		$this->assertTrue($object->write($data));

		$object->close();

		$this->assertStringEqualsFile(
			$filename,
			$data
		);

		unlink($filename);
	}

	/**
	 * Test chmod with no filename exception.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testChmodNoFilename()
	{
		$this->object->chmod();
	}

	/**
	 * Test chmod method.
	 *
	 * @return  void
	 */
	public function testChmod()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp/';
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->assertTrue($this->object->chmod($filename, 0777));

		$this->assertEquals(
			'0777',
			substr(sprintf('%o', fileperms($filename)), -4)
		);

		$this->object = Stream::getStream();
		$this->object->open($filename, 'w');

		$this->assertTrue($this->object->chmod('', 0644));

		$this->object->close();

		clearstatcache();
		$this->assertEquals(
			'0644',
			substr(sprintf('%o', fileperms($filename)), -4)
		);

		unlink($filename);
	}

	/**
	 * Test get_meta_data stream not open exception.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGet_meta_dataNotOpen()
	{
		$this->object->get_meta_data();
	}

	/**
	 * Test get_meta_data method.
	 *
	 * @return  void
	 */
	public function testGet_meta_data()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);
		$metaData = $this->object->get_meta_data();

		$this->assertTrue(
			is_array($metaData)
		);

		$this->assertEquals(
			$filename,
			$metaData['uri']
		);

		unlink($filename);
	}

	/**
	 * Test buildContext method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function test_buildContext()
	{
		$contextOptions = array();

		TestHelper::setValue($this->object, 'contextOptions', $contextOptions);
		$this->object->_buildContext();

		$this->assertEquals(
			null,
			TestHelper::getValue($this->object, 'context')
		);

		$contextOptions = array(
			'http' => array(
				'method' => "GET",
				'header' => "Accept-language: en\r\n" .
					"Cookie: foo=bar\r\n"
			)
		);

		TestHelper::setValue($this->object, 'contextOptions', $contextOptions);
		$this->object->_buildContext();

		$this->assertEquals(
			'resource',
			gettype(TestHelper::getValue($this->object, 'context'))
		);
	}

	/**
	 * Test setContextOptions method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testSetContextOptions()
	{
		$contextOptions = array(
			'http' => array(
				'method' => "GET",
				'header' => "Accept-language: en\r\n" .
					"Cookie: foo=bar\r\n"
			)
		);

		$this->object->setContextOptions($contextOptions);

		$this->assertEquals(
			$contextOptions,
			TestHelper::getValue($this->object, 'contextOptions')
		);
	}

	/**
	 * Test addContextEntry method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testAddContextEntry()
	{
		$this->object->addContextEntry('foo', 'bar', 'barfoo');
		$contextOptions = TestHelper::getValue($this->object, 'contextOptions');

		$this->assertEquals(
			'barfoo',
			$contextOptions['foo']['bar']
		);
	}

	/**
	 * Test deleteContextEntry method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testDeleteContextEntry()
	{
		$contextOptions = array(
			'foo' => array(
				'bar' => 'Bar',
				'rab' => 'Rab'
			)
		);

		TestHelper::setValue($this->object, 'contextOptions', $contextOptions);

		$this->object->deleteContextEntry('foo', 'bar');
		$actual = TestHelper::getValue($this->object, 'contextOptions');

		$this->assertArrayHasKey(
			'foo',
			$actual
		);

		$this->assertArrayHasKey(
			'rab',
			$actual['foo']
		);

		$this->assertArrayNotHasKey(
			'bar',
			$actual['foo']
		);

		$this->object->deleteContextEntry('foo', 'rab');
		$actual = TestHelper::getValue($this->object, 'contextOptions');

		$this->assertArrayNotHasKey(
			'foo',
			$actual
		);
	}

	/**
	 * Test applyContextToStream method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testApplyContextToStream()
	{
		$this->assertFalse($this->object->applyContextToStream());

		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);
		$this->assertTrue($this->object->applyContextToStream());

		unlink($filename);
	}

	/**
	 * Test appendFilter method.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testAppendFilter()
	{
		$this->assertFalse($this->object->appendFilter("string.rot13"));

		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);

		$filters = TestHelper::getValue($this->object, 'filters');

		$this->assertEquals(
			'resource',
			gettype($this->object->appendFilter("string.rot13"))
		);

		$this->assertEquals(
			count($filters) + 1,
			count(TestHelper::getValue($this->object, 'filters'))
		);

		unlink($filename);

		// Tests for invalid filters
		$this->object->appendFilter("foobar");
	}

	/**
	 * Test prependFilter method.
	 *
	 * @return  void
	 *
	 * @expectedException RuntimeException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testPrependFilter()
	{
		$this->assertFalse($this->object->prependFilter("string.rot13"));

		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . '/' . $name;

		// Create a temp file to test copy operation
		if (!@file_put_contents($filename, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->object->open($filename);

		$filters = TestHelper::getValue($this->object, 'filters');

		$this->assertEquals(
			'resource',
			gettype($this->object->prependFilter("string.rot13"))
		);

		$this->assertEquals(
			count($filters) + 1,
			count(TestHelper::getValue($this->object, 'filters'))
		);

		// Tests for invalid filters
		$this->object->prependFilter("foobar");

		unlink($filename);

		$this->object->close();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testRemoveFilter().
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testRemoveFilter()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test copy method.
	 *
	 * @return  void
	 *
	 * @requires PHP 5.4
	 * @since   __DEPLOY_VERSION__
	 */
	public function testCopy()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$copiedFileName = 'copiedTempFile';
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		if (!@file_put_contents($path . '/' . $name, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->assertTrue(
			$this->object->copy($path . '/' . $name, $path . '/' . $copiedFileName, null, false),
			'Line:' . __LINE__ . ' File should copy successfully.'
		);
		$this->assertFileEquals(
			$path . '/' . $name,
			$path . '/' . $copiedFileName
		);
		unlink($path . '/' . $copiedFileName);

		unlink($path . '/' . $name);
	}

	/**
	 * Test move mthod.
	 *
	 * @return  void
	 *
	 * @requires PHP 5.4
	 * @since   __DEPLOY_VERSION__
	 */
	public function testMove()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$movedFileName = 'copiedTempFile';
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		if (!@file_put_contents($path . '/' . $name, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->assertThat(
			$this->object->move($path . '/' . $name, $path . '/' . $movedFileName, null, false),
			$this->isTrue(),
			'Line:' . __LINE__ . ' File should moved successfully.'
		);
		unlink($path . '/' . $movedFileName);

		@unlink($path . '/' . $name);
	}

	/**
	 * Test delete method.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testDelete()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		if (!@file_put_contents($path . '/' . $name, $data))
		{
			$this->markTestSkipped('Temp file could not be written');
		}

		$this->assertFileExists($path . '/' . $name);
		$this->assertThat(
			$this->object->delete($path . '/' . $name, null, false),
			$this->isTrue(),
			'Line:' . __LINE__ . ' File should deleted successfully.'
		);
		$this->assertFileNotExists($path . '/' . $name);

		@unlink($path . '/' . $name);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testUpload().
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUpload()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test writeFile method.
	 *
	 * @return  void
	 *
	 * @requires PHP 5.4
	 * @since   __DEPLOY_VERSION__
	 */
	public function testWriteFile()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			$this->object->writeFile($path . '/' . $name, $data)
		);

		$this->assertFileExists($path . '/' . $name);
		$this->assertStringEqualsFile(
			$path . '/' . $name,
			$data
		);

		unlink($path . '/' . $name);
	}

	/**
	 * Test data for _getFilename test
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function data_getFilename()
	{
		return array(
			array('', '', 'foobar', 'r', false, false, 'foobar'),
			array('', '', 'foobar', 'r', false, true, 'foobar'),
			array('', '', 'foobar', 'w', false, false, 'foobar'),
			array('', '', 'foobar', 'w', false, true, 'foobar'),
			array('one', 'two', 'foobar', 'r', true, false, 'twofoobar'),
			array('one', 'two', 'foobar', 'w', true, false, 'onefoobar'),
			array('one', 'two', 'foobar', 'r', true, true, 'twofoobar'),
			array('one', 'two', 'foobar', 'w', true, true, 'onefoobar'),
			array('one', 'two', __DIR__ . '/foobar', 'r', true, false, 'two/Tests/foobar'),
			array('one', 'two', __DIR__ . '/foobar', 'w', true, false, 'one/Tests/foobar'),
		);
	}

	/**
	 * Test _getFilename method.
	 *
	 * @param   string   $wPrefix     Write prefix
	 * @param   string   $rPrefix     Read prefix
	 * @param   string   $filename    Filename
	 * @param   string   $mode        File open mode
	 * @param   boolean  $use_prefix  Whether to use prefix or not
	 * @param   boolean  $relative    filename is relative or not
	 * @param   string   $expected    Expected path
	 *
	 * @return  void
	 *
	 * @dataProvider data_getFilename
	 * @since   __DEPLOY_VERSION__
	 */
	public function test_getFilename($wPrefix, $rPrefix, $filename, $mode, $use_prefix, $relative, $expected)
	{
		TestHelper::setValue($this->object, 'writeprefix', $wPrefix);
		TestHelper::setValue($this->object, 'readprefix', $rPrefix);

		$this->assertEquals(
			$expected,
			$this->object->_getFilename($filename, $mode, $use_prefix, $relative)
		);
	}

	/**
	 * Test...
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testGetFileHandle()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
