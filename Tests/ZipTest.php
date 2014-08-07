<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Zip as ArchiveZip;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Archive\Zip.
 *
 * @since  1.0
 */
class ZipTest extends \PHPUnit_Framework_TestCase
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
	 * @var Joomla\Archive\Zip
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

		$this->object = new ArchiveZip;
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
	 * @covers  Joomla\Archive\Zip::__construct
	 */
	public function test__construct()
	{
		$object = new ArchiveZip;

		$this->assertEmpty(
			TestHelper::getValue($object, 'options')
		);

		$options = array('use_streams' => false);
		$object = new ArchiveZip($options);

		$this->assertEquals(
			$options,
			TestHelper::getValue($object, 'options')
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testCreate().
	 *
	 * @covers  Joomla\Archive\Zip::create
	 * @covers  Joomla\Archive\Zip::addToZIPFile
	 * @covers  Joomla\Archive\Zip::unix2DOSTime
	 * @covers  Joomla\Archive\Zip::createZIPFile
	 * @return void
	 */
	public function testCreate()
	{
		$result = $this->object->create(
			self::$outputPath . '/logo.zip',
			array(array(
				'name' => 'logo.png',
				'data' => file_get_contents(self::$inputPath . '/logo.png'),
			))
		);

		$this->assertTrue($result);

		$dataZip = file_get_contents(self::$outputPath . '/logo.zip');
		$this->assertTrue(
			$this->object->checkZipData($dataZip)
		);

		@unlink(self::$outputPath . '/logo.zip');
	}

	/**
	 * Tests the extractNative Method.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Zip::extractNative
	 */
	public function testExtractNative()
	{
		if (!ArchiveZip::hasNativeSupport())
		{
			$this->markTestSkipped(
				'ZIP files can not be extracted nativly.'
			);

			return;
		}

		TestHelper::invoke(
			$this->object,
			'extractNative',
			self::$inputPath . '/logo.zip', self::$outputPath
		);

		$this->assertFileExists(self::$outputPath . '/logo-zip.png');
		$this->assertFileEquals(
			self::$outputPath . '/logo-zip.png',
			self::$inputPath . '/logo.png'
		);

		@unlink(self::$outputPath . '/logo-zip.png');
	}

	/**
	 * Tests the extractCustom Method.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Zip::extractCustom
	 * @covers  Joomla\Archive\Zip::readZipInfo
	 * @covers  Joomla\Archive\Zip::getFileData
	 */
	public function testExtractCustom()
	{
		if (!ArchiveZip::isSupported())
		{
			$this->markTestSkipped(
				'ZIP files can not be extracted.'
			);

			return;
		}

		TestHelper::invoke(
			$this->object,
			'extractCustom',
			self::$inputPath . '/logo.zip', self::$outputPath
		);

		$this->assertFileExists(self::$outputPath . '/logo-zip.png');
		$this->assertFileEquals(
			self::$outputPath . '/logo-zip.png',
			self::$inputPath . '/logo.png'
		);

		@unlink(self::$outputPath . '/logo-zip.png');
	}

	/**
	 * Tests the extract Method.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Zip::extract
	 */
	public function testExtract()
	{
		if (!ArchiveZip::isSupported())
		{
			$this->markTestSkipped(
				'ZIP files can not be extracted.'
			);

			return;
		}

		$this->object->extract(
			self::$inputPath . '/logo.zip',
			self::$outputPath
		);

		$this->assertFileExists(self::$outputPath . '/logo-zip.png');
		$this->assertFileEquals(
			self::$outputPath . '/logo-zip.png',
			self::$inputPath . '/logo.png'
		);

		@unlink(self::$outputPath . '/logo-zip.png');
	}

	/**
	 * Tests the extract Method exception on non-existent archive file.
	 *
	 * @group   JArchive
	 *
	 * @covers             Joomla\Archive\Zip::extract
	 * @expectedException  RuntimeException
	 * @return  void
	 */
	public function testExtractException()
	{
		if (!ArchiveZip::isSupported())
		{
			$this->markTestSkipped(
				'ZIP files can not be extracted.'
			);

			return;
		}

		$this->object->extract(
			self::$inputPath . '/foobar.zip',
			self::$outputPath
		);
	}

	/**
	 * Tests the hasNativeSupport Method.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Zip::hasNativeSupport
	 */
	public function testHasNativeSupport()
	{
		$this->assertEquals(
			(function_exists('zip_open') && function_exists('zip_read')),
			ArchiveZip::hasNativeSupport()
		);
	}

	/**
	 * Tests the isSupported Method.
	 *
	 * @group    JArchive
	 * @return   void
	 *
	 * @covers   Joomla\Archive\Zip::isSupported
	 * @depends  testHasNativeSupport
	 */
	public function testIsSupported()
	{
		$this->assertEquals(
			(ArchiveZip::hasNativeSupport() || extension_loaded('zlib')),
			ArchiveZip::isSupported()
		);
	}

	/**
	 * Test...
	 *
	 * @covers  Joomla\Archive\Zip::checkZipData
	 *
	 * @return void
	 */
	public function testCheckZipData()
	{
		$dataZip = file_get_contents(self::$inputPath . '/logo.zip');
		$this->assertTrue(
			$this->object->checkZipData($dataZip)
		);

		$dataTar = file_get_contents(self::$inputPath . '/logo.tar');
		$this->assertFalse(
			$this->object->checkZipData($dataTar)
		);
	}
}
