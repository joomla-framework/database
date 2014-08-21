<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Tar as ArchiveTar;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Archive\Tar.
 *
 * @since  1.0
 */
class TarTest extends \PHPUnit_Framework_TestCase
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
	 * @var Joomla\Archive\Tar
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

		$this->object = new ArchiveTar;
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
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Tar::__construct
	 */
	public function test__construct()
	{
		$object = new ArchiveTar;

		$this->assertEmpty(
			TestHelper::getValue($object, 'options')
		);

		$options = array('foo' => 'bar');
		$object = new ArchiveTar($options);

		$this->assertEquals(
			$options,
			TestHelper::getValue($object, 'options')
		);
	}

	/**
	 * Tests the extract Method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Tar::extract
	 * @covers  Joomla\Archive\Tar::getTarInfo
	 */
	public function testExtract()
	{
		if (!ArchiveTar::isSupported())
		{
			$this->markTestSkipped('Tar files can not be extracted.');

			return;
		}

		$this->object->extract(self::$inputPath . '/logo.tar', self::$outputPath);
		$this->assertFileExists(self::$outputPath . '/logo-tar.png');

		if (is_file(self::$outputPath . '/logo-tar.png'))
		{
			unlink(self::$outputPath . '/logo-tar.png');
		}
	}

	/**
	 * Tests the isSupported Method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Tar::isSupported
	 */
	public function testIsSupported()
	{
		$this->assertTrue(
			ArchiveTar::isSupported()
		);
	}
}
