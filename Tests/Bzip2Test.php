<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Bzip2 as ArchiveBzip2;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Archive\Bzip2.
 *
 * @since  1.0
 */
class Bzip2Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * Output directory
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected static $outputPath;

	/**
	 * Input directory
	 *
	 * @var    string
	 * @since  1.1.3
	 */
	protected static $inputPath;

	/**
	 * Object under test
	 *
	 * @var    ArchiveBzip2
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
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

		$this->object = new ArchiveBzip2;
	}

	/**
	 * Tear down the fixture.
	 *
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.1.3
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
	 * @covers  Joomla\Archive\Bzip2::__construct
	 *
	 * @return  void
	 *
	 * @since   1.1.3
	 */
	public function test__construct()
	{
		$object = new ArchiveBzip2;

		$this->assertEmpty(
			TestHelper::getValue($object, 'options')
		);

		$options = array('use_streams' => false);
		$object = new ArchiveBzip2($options);

		$this->assertEquals(
			$options,
			TestHelper::getValue($object, 'options')
		);
	}

	/**
	 * Tests the extract Method.
	 *
	 * @covers  Joomla\Archive\Bzip2::extract
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testExtract()
	{
		if (!ArchiveBzip2::isSupported())
		{
			$this->markTestSkipped('Bzip2 files can not be extracted.');

			return;
		}

		$this->object->extract(
			self::$inputPath . '/logo.bz2',
			self::$outputPath . '/logo-bz2.png'
		);

		$this->assertFileExists(self::$outputPath . '/logo-bz2.png');
		$this->assertFileEquals(
			self::$outputPath . '/logo-bz2.png',
			self::$inputPath . '/logo.png'
		);

		@unlink(self::$outputPath . '/logo-bz2.png');
	}

	/**
	 * Tests the extract Method.
	 *
	 * @covers  Joomla\Archive\Bzip2::extract
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testExtractWithStreams()
	{
		$this->markTestSkipped('There is a bug, see https://bugs.php.net/bug.php?id=63195&edit=1');

		if (!ArchiveBzip2::isSupported())
		{
			$this->markTestSkipped('Bzip2 files can not be extracted.');

			return;
		}

		$object = new ArchiveBzip2(array('use_streams' => true));
		$object->extract(
			self::$inputPath . '/logo.bz2',
			self::$outputPath . '/logo-bz2.png'
		);

		$this->assertFileExists(self::$outputPath . '/logo-bz2.png');
		$this->assertFileEquals(
			self::$outputPath . '/logo-bz2.png',
			self::$inputPath . '/logo.png'
		);

		@unlink(self::$outputPath . '/logo-bz2.png');
	}

	/**
	 * Tests the isSupported Method.
	 *
	 * @covers  Joomla\Archive\Bzip2::isSupported
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testIsSupported()
	{
		$this->assertEquals(
			extension_loaded('bz2'),
			ArchiveBzip2::isSupported()
		);
	}
}
