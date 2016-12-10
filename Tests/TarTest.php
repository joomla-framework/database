<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Tar as ArchiveTar;

/**
 * Test class for Joomla\Archive\Tar.
 */
class TarTest extends ArchiveTestCase
{
	/**
	 * @testdox  The tar adapter is instantiated correctly
	 *
	 * @covers   Joomla\Archive\Tar::__construct
	 */
	public function test__construct()
	{
		$object = new ArchiveTar;

		$this->assertAttributeEmpty('options', $object);

		$options = array('foo' => 'bar');
		$object = new ArchiveTar($options);

		$this->assertAttributeSame($options, 'options', $object);
	}

	/**
	 * @testdox  An archive can be extracted
	 *
	 * @covers   Joomla\Archive\Tar::extract
	 * @covers   Joomla\Archive\Tar::getTarInfo
	 */
	public function testExtract()
	{
		if (!ArchiveTar::isSupported())
		{
			$this->markTestSkipped('Tar files can not be extracted.');
		}

		$object = new ArchiveTar;

		$object->extract($this->inputPath . '/logo.tar', $this->outputPath);
		$this->assertFileExists($this->outputPath . '/logo-tar.png');

		if (is_file($this->outputPath . '/logo-tar.png'))
		{
			unlink($this->outputPath . '/logo-tar.png');
		}
	}

	/**
	 * @testdox  The adapter detects if the environment is supported
	 *
	 * @covers   Joomla\Archive\Tar::isSupported
	 */
	public function testIsSupported()
	{
		$this->assertTrue(ArchiveTar::isSupported());
	}
}
