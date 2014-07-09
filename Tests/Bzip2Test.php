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
	 * @var string
	 */
	protected static $outputPath;

	/**
	 * @var ArchiveBzip2
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

		self::$outputPath = __DIR__ . '/output';

		if (!is_dir(self::$outputPath))
		{
			mkdir(self::$outputPath, 0777);
		}

		$this->object = new ArchiveBzip2;
	}

	/**
	 * Tests the constructor.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Bzip2::__construct
	 */
	public function test__construct()
	{
		$object = new ArchiveBzip2;

		$this->assertEquals(
			array(),
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
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Bzip2::extract
	 */
	public function testExtract()
	{
		if (!\Joomla\Archive\Bzip2::isSupported())
		{
			$this->markTestSkipped('Bzip2 files can not be extracted.');

			return;
		}

		$this->object->extract(__DIR__ . '/logo.bz2', self::$outputPath . '/logo-bz2.png');

		$this->assertFileExists(self::$outputPath . '/logo-bz2.png');
		$this->assertFileEquals(
			self::$outputPath . '/logo-bz2.png',
			__DIR__ . '/logo.png'
		);

		@unlink(self::$outputPath . '/logo-bz2.png');
	}

	/**
	 * Tests the extract Method.
	 *
	 * @group   JArchive
	 * @return  Joomla\Archive\Bzip2::extract
	 */
	public function testExtractWithStreams()
	{
		if (!\Joomla\Archive\Bzip2::isSupported())
		{
			$this->markTestSkipped('Bzip2 files can not be extracted.');

			return;
		}

		$object = new ArchiveBzip2(array('use_streams' => true));
		$object->extract(__DIR__ . '/logo.bz2', self::$outputPath . '/logo-bz2.png');

		$this->assertFileExists(self::$outputPath . '/logo-bz2.png');
		$this->assertFileEquals(
			self::$outputPath . '/logo-bz2.png',
			__DIR__ . '/logo.png'
		);

		@unlink(self::$outputPath . '/logo-bz2.png');
	}

	/**
	 * Tests the isSupported Method.
	 *
	 * @group   JArchive
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Bzip2::isSupported
	 */
	public function testIsSupported()
	{
		$this->assertEquals(
			extension_loaded('bz2'),
			\Joomla\Archive\Bzip2::isSupported()
		);
	}
}
