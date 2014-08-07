<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Gzip as ArchiveGzip;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Archive\Gzip.
 *
 * @since  1.0
 */
class GzipTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Output directory
	 *
	 * @var string
	 */
	protected static $outputPath;

	/**
	 * Input directory
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $inputPath;

	/**
	 * @var Joomla\Archive\Gzip
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

		self::$inputPath = __DIR__ . '/testdata';
		self::$outputPath = __DIR__ . '/output';

		if (!is_dir(self::$outputPath))
		{
			mkdir(self::$outputPath, 0777);
		}

		$this->object = new ArchiveGzip;
	}

	/**
	 * Tear down the fixture.
	 *
	 * This method is called after a test is executed.
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	protected function tearDown()
	{
		if (is_dir(self::$outputPath))
		{
			rmdir(self::$outputPath);
		}

		parent::tearDown();
	}

	/**
	 * Tests the constructor.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Gzip::__construct
	 */
	public function test__construct()
	{
		$object = new ArchiveGzip;

		$this->assertEmpty(
			TestHelper::getValue($object, 'options')
		);

		$options = array('use_streams' => false);
		$object = new ArchiveGzip($options);

		$this->assertEquals(
			$options,
			TestHelper::getValue($object, 'options')
		);
	}

	/**
	 * Tests the extract Method.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Gzip::extract
	 */
	public function testExtract()
	{
		if (!ArchiveGzip::isSupported())
		{
			$this->markTestSkipped('Gzip files can not be extracted.');

			return;
		}

		$this->object->extract(
			self::$inputPath . '/logo.gz',
			self::$outputPath . '/logo-gz.png'
		);

		$this->assertFileExists(self::$outputPath . '/logo-gz.png');
		$this->assertFileEquals(
			self::$outputPath . '/logo-gz.png',
			self::$inputPath . '/logo.png'
		);

		@unlink(self::$outputPath . '/logo-gz.png');
	}

	/**
	 * Tests the extract Method.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Gzip::extract
	 * @covers  Joomla\Archive\Gzip::getFilePosition
	 */
	public function testExtractWithStreams()
	{
		$this->markTestSkipped('There is a bug, see https://bugs.php.net/bug.php?id=63195&edit=1');

		if (!ArchiveGzip::isSupported())
		{
			$this->markTestSkipped('Gzip files can not be extracted.');

			return;
		}

		$object = new ArchiveGzip(array('use_streams' => true));
		$object->extract(
			self::$inputPath . '/logo.gz',
			self::$outputPath . '/logo-gz.png'
		);

		$this->assertFileExists(self::$outputPath . '/logo-gz.png');
		$this->assertFileEquals(
			self::$outputPath . '/logo-gz.png',
			self::$inputPath . '/logo.png'
		);

		@unlink(self::$outputPath . '/logo-gz.png');
	}

	/**
	 * Tests the isSupported Method.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Gzip::isSupported
	 */
	public function testIsSupported()
	{
		$this->assertEquals(
			extension_loaded('zlib'),
			$this->object->isSupported()
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement test_getFilePosition().
	 *
	 * @covers  Joomla\Archive\Gzip::getFilePosition
	 * @return void
	 */
	public function testGetFilePosition()
	{
		// @todo use an all flags enabled file
		TestHelper::setValue(
			$this->object,
			'data',
			file_get_contents(self::$inputPath . '/logo.gz')
		);

		$this->assertEquals(
			22,
			$this->object->getFilePosition()
		);
	}
}
