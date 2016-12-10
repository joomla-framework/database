<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Filesystem\Stream;

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

	private $fileName = "FILE_NAME";
	private $writePrefix = "WRITE_PREFIX";
	private $readPrefix = "READ_PREFIX";

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Stream($this->writePrefix, $this->readPrefix);
	}

	/**
	 * Test...
	 *
	 * @todo Implement test__destruct().
	 *
	 * @return void
	 */
	public function test__destruct()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Tests getStream()
	 *
	 * @return  void
	 */
	public function testGetStream()
	{
		$this->object = Stream::getStream();

		$this->assertInstanceOf(
			'Joomla\\Filesystem\\Stream',
			$this->object,
			'getStream must return an instance of Joomla\\Filesystem\\Stream'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testClose().
	 *
	 * @return void
	 */
	public function testClose()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testSeek().
	 *
	 * @return void
	 */
	public function testSeek()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testTell().
	 *
	 * @return void
	 */
	public function testTell()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testAppendFilter().
	 *
	 * @return void
	 */
	public function testAppendFilter()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testPrependFilter().
	 *
	 * @return void
	 */
	public function testPrependFilter()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Test _getFilename method.
	 *
	 * @return void
	 */
	public function test_getFilenameBaseCase()
	{
		$this->assertEquals(
			$this->fileName,
			$this->object->_getFilename($this->fileName, "w", false, true),
			'Line:' . __LINE__ . ' _getFilename should return unmodified filename when use_prefix is false.'
		);
	}

	/**
	 * Test _getFilename method.
	 *
	 * @return void
	 */
	public function test_getFilenameUseOfReadAndWritePrefix()
	{
		$this->assertEquals(
			$this->writePrefix . $this->fileName,
			$this->object->_getFilename($this->fileName, "w", true, true),
			'Line:' . __LINE__ . ' _getFilename should add write prefix if present.'
		);

		$this->assertEquals(
			$this->readPrefix . $this->fileName,
			$this->object->_getFilename($this->fileName, "r", true, true),
			'Line:' . __LINE__ . ' _getFilename should add read prefix if present.'
		);
	}

	/**
	 * Test _getFilename method.
	 *
	 * @return void
	 */
	public function test_getFilenameReplaceJPATH_ROOTWithAbsoluteFilename()
	{
		$this->assertEquals(
			$this->writePrefix . $this->fileName,
			$this->object->_getFilename($this->fileName, "w", true, false),
			'Line:' . __LINE__ . ' _getFilename should replace JPATH_ROOT and add write prefix if present.'
		);

		$this->assertEquals(
			$this->readPrefix . '/Tests/' . $this->fileName,
			$this->object->_getFilename(__DIR__ . '/' . $this->fileName, "r", true, false),
			'Line:' . __LINE__ . ' _getFilename should replace JPATH_ROOT and add read prefix if present.'
		);

		$this->assertEquals(
			$this->writePrefix . '/Tests/' . __DIR__ . '/' . $this->fileName,
			$this->object->_getFilename(__DIR__ . '/' . __DIR__ . '/' . $this->fileName, "w", true, false),
			'Line:' . __LINE__ . ' _getFilename should replace JPATH_ROOT from start only.'
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
