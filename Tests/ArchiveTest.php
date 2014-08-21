<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use Joomla\Archive\Archive as Archive;
use Joomla\Archive\Zip as ArchiveZip;
use Joomla\Archive\Tar as ArchiveTar;
use Joomla\Archive\Gzip as ArchiveGzip;
use Joomla\Archive\Bzip2 as ArchiveBzip2;

/**
 * Test class for Joomla\Archive\Archive.
 *
 * @since  1.0
 */
class ArchiveTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Object under test
	 *
	 * @var    Archive
	 * @since  1.0
	 */
	protected $fixture;

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
	 * @since  1.0
	 */
	protected static $inputPath;

	/**
	 * Sets up the fixture.
	 *
	 * This method is called before a test is executed.
	 *
	 * @return  mixed
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

		$this->fixture = new Archive;
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
	 * Test data for extracting ZIP : testExtract.
	 *
	 * @return  void
	 *
	 * @since   __VERSION_NO__
	 */
	public function dataExtract()
	{
		return array(
			array('logo.zip', 'Zip', 'logo-zip.png', false),
			array('logo.tar', 'Tar', 'logo-tar.png', false),
			array('logo.gz', 'Gzip', 'logo-gz.png', true),
			array('logo.bz2', 'Bzip2', 'logo-bz2.png', true),
			array('logo.tar.gz', 'Gzip', 'logo-tar-gz.png', false),
			array('logo.tar.bz2', 'Bzip2', 'logo-tar-bz2.png', false),
		);
	}

	/**
	 * Tests extracting ZIP.
	 *
	 * @param   string  $filename           Name of the file to extract
	 * @param   string  $adapterType        Type of adaptar that will be used
	 * @param   string  $extractedFilename  Name of the file to extracted file
	 * @param   bool    $isOutputFile       Whether output is a dirctory or file
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Archive::extract
	 * @dataProvider dataExtract
	 * @since   1.0
	 */
	public function testExtract($filename, $adapterType, $extractedFilename, $isOutputFile = false)
	{
		if (!is_dir(self::$outputPath))
		{
			$this->markTestSkipped("Couldn't create folder.");

			return;
		}

		if (!is_writable(self::$outputPath) || !is_writable($this->fixture->options['tmp_path']))
		{
			$this->markTestSkipped("Folder not writable.");

			return;
		}

		$adapter = "Joomla\\Archive\\$adapterType";

		if (!$adapter::isSupported())
		{
			$this->markTestSkipped($adapterType . ' files can not be extracted.');

			return;
		}

		$outputPath = self::$outputPath . ($isOutputFile ? "/$extractedFilename" : '');

		$this->assertTrue(
			$this->fixture->extract(self::$inputPath . "/$filename", $outputPath)
		);

		$this->assertFileExists(self::$outputPath . "/$extractedFilename");

		@unlink(self::$outputPath . "/$extractedFilename");
	}

	/**
	 * Tests extracting ZIP.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Archive\Archive::extract
	 * @expectedException InvalidArgumentException
	 * @since   1.0
	 */
	public function testExtractUnknown()
	{
		if (!is_dir(self::$outputPath))
		{
			$this->markTestSkipped("Couldn't create folder.");

			return;
		}

		$this->fixture->extract(
			self::$inputPath . '/logo.dat',
			self::$outputPath
		);
	}

	/**
	 * Tests getting adapter.
	 *
	 * @return  mixed
	 *
	 * @covers  Joomla\Archive\Archive::getAdapter
	 * @since   1.0
	 */
	public function testGetAdapter()
	{
		$zip = $this->fixture->getAdapter('zip');
		$this->assertInstanceOf('Joomla\\Archive\\Zip', $zip);

		$bzip2 = $this->fixture->getAdapter('bzip2');
		$this->assertInstanceOf('Joomla\\Archive\\Bzip2', $bzip2);

		$gzip = $this->fixture->getAdapter('gzip');
		$this->assertInstanceOf('Joomla\\Archive\\Gzip', $gzip);

		$tar = $this->fixture->getAdapter('tar');
		$this->assertInstanceOf('Joomla\\Archive\\Tar', $tar);
	}

	/**
	 * Test getAdapter exception.
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Archive\Archive::getAdapter
	 * @expectedException  \InvalidArgumentException
	 * @since              1.0
	 */
	public function testGetAdapterException()
	{
		$this->fixture->getAdapter('unknown');
	}

	/**
	 * Test getAdapter exception message.
	 *
	 * @return  void
	 *
	 * @since  1.0
	 */
	public function testGetAdapterExceptionMessage()
	{
		try
		{
			$this->fixture->getAdapter('unknown');
		}

		catch (\InvalidArgumentException $e)
		{
			$this->assertEquals(
				'Archive adapter "unknown" (class "Joomla\\Archive\\Unknown") not found or supported.',
				$e->getMessage()
			);
		}
	}

	/**
	 * Test setAdapter.
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Archive\Archive::setAdapter
	 *
	 * @since              1.0
	 */
	public function testSetAdapter()
	{
		$class = 'Joomla\\Archive\\Zip';
		$this->assertInstanceOf(
			'Joomla\\Archive\\Archive',
			$this->fixture->setAdapter('zip', new $class)
		);
	}

	/**
	 * Test setAdapter exception.
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Archive\Archive::setAdapter
	 * @expectedException  \InvalidArgumentException
	 * @since              1.0
	 */
	public function testSetAdapterUnknownException()
	{
		$this->fixture->setAdapter('unknown', 'unknown-class');
	}

	/**
	 * Test setAdapter exception message.
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Archive\Archive::setAdapter
	 * @since  1.0
	 */
	public function testSetAdapterExceptionMessage()
	{
		try
		{
			$this->fixture->setAdapter('unknown', 'FooArchiveAdapter');
		}

		catch (\InvalidArgumentException $e)
		{
			$this->assertEquals(
				'Archive adapter "unknown" (class "FooArchiveAdapter") not found.',
				$e->getMessage()
			);
		}
	}
}
