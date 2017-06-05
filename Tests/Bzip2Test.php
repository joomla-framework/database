<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Bzip2 as ArchiveBzip2;

/**
 * Test class for Joomla\Archive\Bzip2.
 */
class Bzip2Test extends ArchiveTestCase
{
	/**
	 * @testdox  The bzip2 adapter is instantiated correctly
	 *
	 * @covers   Joomla\Archive\Bzip2::__construct
	 */
	public function test__construct()
	{
		$object = new ArchiveBzip2;

		$this->assertAttributeEmpty('options', $object);

		$options = array('use_streams' => false);
		$object  = new ArchiveBzip2($options);

		$this->assertAttributeSame($options, 'options', $object);
	}

	/**
	 * @testdox  An archive can be extracted
	 *
	 * @covers   Joomla\Archive\Bzip2::extract
	 */
	public function testExtract()
	{
		if (!ArchiveBzip2::isSupported())
		{
			$this->markTestSkipped('Bzip2 files can not be extracted.');
		}

		$object = new ArchiveBzip2;

		$object->extract(
			$this->inputPath . '/logo.png.bz2',
			$this->outputPath . '/logo-bz2.png'
		);

		$this->assertFileExists($this->outputPath . '/logo-bz2.png');
		$this->assertFileEquals(
			$this->outputPath . '/logo-bz2.png',
			$this->inputPath . '/logo.png'
		);

		@unlink($this->outputPath . '/logo-bz2.png');
	}

	/**
	 * @testdox  An archive can be extracted via streams
	 *
	 * @covers   Joomla\Archive\Bzip2::extract
	 */
	public function testExtractWithStreams()
	{
		$this->markTestSkipped('There is a bug, see https://bugs.php.net/bug.php?id=63195&edit=1');

		if (!ArchiveBzip2::isSupported())
		{
			$this->markTestSkipped('Bzip2 files can not be extracted.');
		}

		$object = new ArchiveBzip2(array('use_streams' => true));
		$object->extract(
			$this->inputPath . '/logo.png.bz2',
			$this->outputPath . '/logo-bz2.png'
		);

		$this->assertFileExists($this->outputPath . '/logo-bz2.png');
		$this->assertFileEquals(
			$this->outputPath . '/logo-bz2.png',
			$this->inputPath . '/logo.png'
		);

		@unlink($this->outputPath . '/logo-bz2.png');
	}

	/**
	 * @testdox  The adapter detects if the environment is supported
	 *
	 * @covers   Joomla\Archive\Bzip2::isSupported
	 */
	public function testIsSupported()
	{
		$this->assertSame(
			extension_loaded('bz2'),
			ArchiveBzip2::isSupported()
		);
	}
}
