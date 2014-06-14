<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Filesystem\Stream;
use Joomla\Test\TestHelper;
use Joomla\Filesystem\Support\StringController;

/**
 * Test class for Stream.
 *
 * @since  1.0
 */
class StreamTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Stream
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Stream;
	}

	/**
	 * Test...
	 *
	 * @todo Implement test__construct().
	 *
	 * @return void
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
	 * Tests getStream()
	 *
	 * @return  void
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
	 * Test...
	 *
	 * @todo Implement testOpen().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testOpenNoFilenameException()
	{
		$this->object->open('');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testOpen().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testOpenInvlaidFilenameException()
	{
		$this->object->open('foobar');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testOpen().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testOpenInvlaidStringnameException()
	{
		$this->object->open('string://bbarfoo');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testOpen().
	 *
	 * @return void
	 */
	public function testOpen()
	{
        // Test simple file open
        $name = 'tempFile';
		$path = __DIR__ . '/tmp/';
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . $name;		
		// Create a temp file to test open operation
		file_put_contents($filename, $data);

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
	 * Test...
	 *
	 * @todo Implement testClose().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testCloseBeforeOpeningException()
	{
		$object = new Stream;

		$object->close();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testEof().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testEofNotOpenException()
	{
		$this->object->eof();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testEof().
	 *
	 * @return void
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
	 * Test...
	 *
	 * @todo Implement testFilesize().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testFilesizeNotOpen()
	{
		$this->object->filesize();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testFilesize().
	 *
	 * @return void
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

		$this->object->open('http://www.joomla.org');

		$this->assertTrue(
			is_numeric($this->object->filesize())
		);

		$this->object->close();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGets().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testGetsNotOpen()
	{
		$this->object->gets();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGets().
	 *
	 * @return void
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
	 * Test...
	 *
	 * @todo Implement testGets().
	 *
	 * @return void
	 * @expectedException RuntimeException
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
	 * Test...
	 *
	 * @todo Implement testRead().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testReadNotOpen()
	{
		$this->object->read();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testRead().
	 *
	 * @return void
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
	 * Test...
	 *
	 * @todo Implement testSeek().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testSeekNotOpen()
	{
		$this->object->seek(0);
	}

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
	 * Test...
	 *
	 * @todo Implement testSeek().
	 *
	 * @dataProvider dataSeek
	 * @return void
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
	 * Test...
	 *
	 * @todo Implement testTell().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testTellNotOpen()
	{
		$this->object->tell();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testWrite().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testWriteNotOpen()
	{
		$data = 'foobar';
		$this->object->write($data);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testWrite().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testWriteReadonly()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp/';
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . $name;
		// Create a temp file to test copy operation
		file_put_contents($filename, $data);

		$object = Stream::getStream();
		$object->open($filename);

		$data = 'foobar';
		$this->assertTrue($object->write($data));

		$object->close();

		unlink($filename);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testWrite().
	 *
	 * @return void
	 */
	public function testWrite()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp/';
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . $name;
		// Create a temp file to test copy operation
		file_put_contents($filename, $data);

		$object = Stream::getStream();
		$object->open($filename, 'w');

		$data = 'foobar';
		$this->assertTrue($object->write($data));

		$object->close();

		$this->assertEquals(
			$data,
			file_get_contents($filename)
		);

		unlink($filename);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testChmod().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testChmodNoFilename()
	{
		$this->object->chmod();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testChmod().
	 *
	 * @return void
	 */
	public function testChmod()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp/';
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . $name;
		// Create a temp file to test copy operation
		file_put_contents($filename, $data);

		$this->assertTrue($this->object->chmod($filename,0777));

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
	 * Test...
	 *
	 * @todo Implement testGet_meta_data().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testGet_meta_dataNotOpen()
	{
		$this->object->get_meta_data();
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGet_meta_data().
	 *
	 * @return void
	 */
	public function testGet_meta_data()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp/';
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . $name;
		// Create a temp file to test copy operation
		file_put_contents($filename, $data);

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
	 * Test...
	 *
	 * @todo Implement test_buildContext().
	 *
	 * @return void
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
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
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
	 * Test...
	 *
	 * @todo Implement testSetContextOptions().
	 *
	 * @return void
	 */
	public function testSetContextOptions()
	{
		$contextOptions = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: en\r\n" .
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
	 * Test...
	 *
	 * @todo Implement testAddContextEntry().
	 *
	 * @return void
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
	 * Test...
	 *
	 * @todo Implement testDeleteContextEntry().
	 *
	 * @return void
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
	 * Test...
	 *
	 * @todo Implement testApplyContextToStream().
	 *
	 * @return void
	 */
	public function testApplyContextToStream()
	{
		$this->assertFalse($this->object->applyContextToStream());

		$name = 'tempFile';
		$path = __DIR__ . '/tmp/';
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . $name;
		// Create a temp file to test copy operation
		file_put_contents($filename, $data);

		$this->object->open($filename);
		$this->assertTrue($this->object->applyContextToStream());

		unlink($filename);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testAppendFilter().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testAppendFilter()
	{
		$this->assertFalse($this->object->appendFilter("string.rot13"));

		$name = 'tempFile';
		$path = __DIR__ . '/tmp/';
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . $name;
		// Create a temp file to test copy operation
		file_put_contents($filename, $data);

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
	 * Test...
	 *
	 * @todo Implement testPrependFilter().
	 *
	 * @return void
	 * @expectedException RuntimeException
	 */
	public function testPrependFilter()
	{
		$this->assertFalse($this->object->prependFilter("string.rot13"));

		$name = 'tempFile';
		$path = __DIR__ . '/tmp/';
		$data = 'Lorem ipsum dolor sit amet';
		$filename = $path . $name;
		// Create a temp file to test copy operation
		file_put_contents($filename, $data);

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
	 * @return void
	 */
	public function testRemoveFilter()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testCopy().
	 *
	 * @return void
	 */
	public function testCopy()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp';
		$copiedFileName = 'copiedTempFile';
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		$this->assertThat(
			$this->object->copy($path . '/' . $name, $path . '/' . $copiedFileName),
			$this->isTrue(),
			'Line:' . __LINE__ . ' File should copy successfully.'
		);
		unlink($path . '/' . $copiedFileName);

		unlink($path . '/' . $name);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testMove().
	 *
	 * @return void
	 */
	public function testMove()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp';
		$movedFileName = 'copiedTempFile';
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		$this->assertThat(
			$this->object->move($path . '/' . $name, $path . '/' . $movedFileName),
			$this->isTrue(),
			'Line:' . __LINE__ . ' File should moved successfully.'
		);
		unlink($path . '/' . $movedFileName);

		@unlink($path . '/' . $name);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testDelete().
	 *
	 * @return void
	 */
	public function testDelete()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp';
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		$this->assertThat(
			$this->object->delete($path . '/' . $name),
			$this->isTrue(),
			'Line:' . __LINE__ . ' File should deleted successfully.'
		);

		@unlink($path . '/' . $name);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testUpload().
	 *
	 * @return void
	 */
	public function testUpload()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testWriteFile().
	 *
	 * @return void
	 */
	public function testWriteFile()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp';
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			$this->object->writeFile($path . '/' . $name, $data)
		);

		$this->assertEquals(
			$data,
			file_get_contents($path . '/' . $name)
		);

		unlink($path . '/' . $name);
	}

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
	 * Test...
	 *
	 * @todo Implement test_getFilename().
	 * @dataProvider data_getFilename
	 * @return void
	 */
	public function test_getFilename($wPrefix, $rPrefix, $filename, $mode, $use_prefix,
		$relative, $expected)
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
	 * @todo Implement testGetFileHandle().
	 *
	 * @return void
	 */
	public function testGetFileHandle()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
