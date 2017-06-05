<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Gzip as ArchiveGzip;
use Joomla\Test\TestHelper;

/**
 * Test class for Joomla\Archive\Gzip.
 */
class GzipTest extends ArchiveTestCase
{
	/**
	 * @testdox  The gzip adapter is instantiated correctly
	 *
	 * @covers   Joomla\Archive\Gzip::__construct
	 */
	public function test__construct()
	{
		$object = new ArchiveGzip;

		$this->assertAttributeEmpty('options', $object);

		$options = array('use_streams' => false);
		$object  = new ArchiveGzip($options);

		$this->assertAttributeSame($options, 'options', $object);
	}

	/**
	 * @testdox  An archive can be extracted
	 *
	 * @covers   Joomla\Archive\Gzip::extract
	 */
	public function testExtract()
	{
		if (!ArchiveGzip::isSupported())
		{
			$this->markTestSkipped('Gzip files can not be extracted.');

			return;
		}

		$object = new ArchiveGzip;

		$object->extract(
			$this->inputPath . '/logo.png.gz',
			$this->outputPath . '/logo-gz.png'
		);

		$this->assertFileExists($this->outputPath . '/logo-gz.png');
		$this->assertFileEquals(
			$this->outputPath . '/logo-gz.png',
			$this->inputPath . '/logo.png'
		);

		@unlink($this->outputPath . '/logo-gz.png');
	}

	/**
	 * @testdox  An archive can be extracted via streams
	 *
	 * @covers   Joomla\Archive\Gzip::extract
	 * @uses     Joomla\Archive\Gzip::getFilePosition
	 */
	public function testExtractWithStreams()
	{
		$this->markTestSkipped('There is a bug, see https://bugs.php.net/bug.php?id=63195&edit=1');

		if (!ArchiveGzip::isSupported())
		{
			$this->markTestSkipped('Gzip files can not be extracted.');
		}

		$object = new ArchiveGzip(array('use_streams' => true));
		$object->extract(
			$this->inputPath . '/logo.png.gz',
			$this->outputPath . '/logo-gz.png'
		);

		$this->assertFileExists($this->outputPath . '/logo-gz.png');
		$this->assertFileEquals(
			$this->outputPath . '/logo-gz.png',
			$this->inputPath . '/logo.png'
		);

		@unlink($this->outputPath . '/logo-gz.png');
	}

	/**
	 * @testdox  The adapter detects if the environment is supported
	 *
	 * @covers   Joomla\Archive\Gzip::isSupported
	 */
	public function testIsSupported()
	{
		$this->assertSame(
			extension_loaded('zlib'),
			ArchiveGzip::isSupported()
		);
	}

	/**
	 * @testdox  The file position is detected
	 *
	 * @covers   Joomla\Archive\Gzip::getFilePosition
	 */
	public function testGetFilePosition()
	{
		$object = new ArchiveGzip;

		// @todo use an all flags enabled file
		TestHelper::setValue(
			$object,
			'data',
			file_get_contents($this->inputPath . '/logo.png.gz')
		);

		$this->assertEquals(
			22,
			$object->getFilePosition()
		);
	}
}
