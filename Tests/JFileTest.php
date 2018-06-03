<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Filesystem\File.
 *
 * @since  1.0
 */
class JFileTest extends TestCase
{
	/**
	 * @var    File
	 * @since  __DEPLOY_VERSION__
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new File;

		vfsStream::setup('root');
	}

	/**
	 * Provides the data to test the makeSafe method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function dataTestStripExt()
	{
		return array(
			array(
				'foobar.php',
				'foobar',
			),
			array(
				'foobar..php',
				'foobar.',
			),
			array(
				'foobar.php.',
				'foobar.php',
			),
		);
	}

	/**
	 * Test makeSafe method
	 *
	 * @param   string  $fileName        The name of the file with extension
	 * @param   string  $nameWithoutExt  Name without extension
	 *
	 * @return void
	 *
	 * @covers        Joomla\Filesystem\File::stripExt
	 * @dataProvider  dataTestStripExt
	 * @since         1.0
	 */
	public function testStripExt($fileName, $nameWithoutExt)
	{
		$this->assertEquals(
			$this->object->stripExt($fileName),
			$nameWithoutExt,
			'Line:' . __LINE__ . ' file extension should be stripped.'
		);
	}

	/**
	 * Provides the data to test the makeSafe method.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function dataTestMakeSafe()
	{
		return array(
			array(
				'joomla.',
				array('#^\.#'),
				'joomla',
				'There should be no fullstop on the end of a filename',
			),
			array(
				'Test j00mla_5-1.html',
				array('#^\.#'),
				'Test j00mla_5-1.html',
				'Alphanumeric symbols, dots, dashes, spaces and underscores should not be filtered',
			),
			array(
				'Test j00mla_5-1.html',
				array('#^\.#', '/\s+/'),
				'Testj00mla_5-1.html',
				'Using strip chars parameter here to strip all spaces',
			),
			array(
				'joomla.php!.',
				array('#^\.#'),
				'joomla.php',
				'Non-alphanumeric symbols should be filtered to avoid disguising file extensions',
			),
			array(
				'joomla.php.!',
				array('#^\.#'),
				'joomla.php',
				'Non-alphanumeric symbols should be filtered to avoid disguising file extensions',
			),
			array(
				'.gitignore',
				array(),
				'.gitignore',
				'Files starting with a fullstop should be allowed when strip chars parameter is empty',
			),
		);
	}

	/**
	 * Test makeSafe method.
	 *
	 * @param   string  $name        The name of the file to test filtering of
	 * @param   array   $stripChars  Whether to filter spaces out the name or not
	 * @param   string  $expected    The expected safe file name
	 * @param   string  $message     The message to show on failure of test
	 *
	 * @return void
	 *
	 * @covers        Joomla\Filesystem\File::makeSafe
	 * @dataProvider  dataTestMakeSafe
	 * @since         1.0
	 */
	public function testMakeSafe($name, $stripChars, $expected, $message)
	{
		$this->assertEquals($this->object->makeSafe($name, $stripChars), $expected, $message);
	}

	/**
	 * Test copy method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Filesystem\File::copy
	 * @since   1.0
	 */
	public function testCopy()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		$copiedFileName = 'foo';
		$this->assertTrue(
			File::copy($path . '/' . $name, $path . '/' . $copiedFileName),
			'Line:' . __LINE__ . ' File should copy successfully.'
		);

		$this->assertFileEquals(
			$path . '/' . $name,
			$path . '/' . $copiedFileName,
			'Line:' . __LINE__ . ' Content should remain intact after copy.'
		);

		$copiedFileName = 'bar';
		$this->assertTrue(
			File::copy($name, $copiedFileName, $path),
			'Line:' . __LINE__ . ' File should copy successfully.'
		);

		$this->assertFileEquals(
			$path . '/' . $name,
			$path . '/' . $copiedFileName,
			'Line:' . __LINE__ . ' Content should remain intact after copy.'
		);
	}

	/**
	 * Test copy method using streams.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Filesystem\File::copy
	 * @requires PHP 5.4
	 * @since   1.0
	 */
	public function testCopyUsingStreams()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		$copiedFileName = 'foobar';
		$this->assertTrue(
			File::copy($name, $copiedFileName, $path, true),
			'Line:' . __LINE__ . ' File should copy successfully.'
		);

		$this->assertFileEquals(
			$path . '/' . $name,
			$path . '/' . $copiedFileName,
			'Line:' . __LINE__ . ' Content should remain intact after copy.'
		);
	}

	/**
	 * Test makeCopy method for an exception
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Filesystem\File::copy
	 * @expectedException  \UnexpectedValueException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testCopyException()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$copiedFileName = 'copiedTempFile';

		File::copy(
			$path . '/' . $name . 'foobar',
			$path . '/' . $copiedFileName
		);
	}

	/**
	 * Test delete method.
	 *
	 * @return void
	 *
	 * @covers    Joomla\Filesystem\File::delete
	 * @requires  PHP 5.4
	 * @since     1.0
	 */
	public function testDelete()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test delete operation
		file_put_contents($path . '/' . $name, $data);

		$this->assertFileExists($path . '/' . $name);

		$this->assertTrue(
			File::delete($path . '/' . $name),
			'Line:' . __LINE__ . ' File should be deleted successfully.'
		);

		$this->assertFileNotExists($path . '/' . $name);
	}

	/**
	 * Test move method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Filesystem\File::move
	 * @since   1.0
	 */
	public function testMove()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$movedFileName = 'movedTempFile';
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		$this->assertFileExists($path . '/' . $name);

		$this->assertTrue(
			File::move($path . '/' . $name, $path . '/' . $movedFileName),
			'Line:' . __LINE__ . ' File should be moved successfully.'
		);

		$this->assertFileNotExists($path . '/' . $name);
		$this->assertFileExists($path . '/' . $movedFileName);

		$this->assertTrue(
			File::move($movedFileName, $name, $path),
			'Line:' . __LINE__ . ' File should be moved successfully.'
		);

		$this->assertFileNotExists($path . '/' . $movedFileName);
		$this->assertFileExists($path . '/' . $name);
	}

	/**
	 * Test move method using streams.
	 *
	 * @return void
	 *
	 * @covers   Joomla\Filesystem\File::move
	 * @requires PHP 5.4
	 * @since    1.0
	 */
	public function testMoveUsingStreams()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$movedFileName = 'movedTempFile';
		$data = 'Lorem ipsum dolor sit amet';

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		$this->assertFileExists($path . '/' . $name);

		$this->assertTrue(
			File::move($name, $movedFileName, $path, true),
			'Line:' . __LINE__ . ' File should be moved successfully.'
		);

		$this->assertFileNotExists($path . '/' . $name);
		$this->assertFileExists($path . '/' . $movedFileName);
	}

	/**
	 * Test write method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Filesystem\File::write
	 * @since   1.0
	 */
	public function testWrite()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';

		// Create a file on pre existing path.
		$this->assertTrue(
			File::write($path . '/' . $name, $data),
			'Line:' . __LINE__ . ' File should be written successfully.'
		);
		$this->assertStringEqualsFile(
			$path . '/' . $name,
			$data
		);

		// Create a file on non-existing path.
		$this->assertTrue(
			File::write($path . '/TempFolder/' . $name, $data),
			'Line:' . __LINE__ . ' File should be written successfully.'
		);
		$this->assertStringEqualsFile(
			$path . '/' . $name,
			$data
		);
	}

	/**
	 * Test write method using streams.
	 *
	 * @return void
	 *
	 * @covers        Joomla\Filesystem\File::write
	 * @requires PHP 5.4
	 * @since         1.0
	 */
	public function testWriteUsingStreams()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$data = 'Lorem ipsum dolor sit amet';

		$this->assertTrue(
			File::write($path . '/' . $name, $data, true),
			'Line:' . __LINE__ . ' File should be written successfully.'
		);
		$this->assertStringEqualsFile(
			$path . '/' . $name,
			$data
		);
	}

	/**
	 * Test upload method.
	 *
	 * @return void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUpload()
	{
		$name = 'tempFile';
		$path = __DIR__ . '/tmp';
		$uploadedFileName = 'uploadedFileName';
		$data = 'Lorem ipsum dolor sit amet';
		include_once __DIR__ . '/Stubs/PHPUploadStub.php';

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		$_FILES = array(
			'test' => array(
				'name' => 'test.jpg',
				'tmp_name' => $path . '/' . $name
			)
		);

		$this->assertTrue(
			File::upload($path . '/' . $name, $path . '/' . $uploadedFileName)
		);
		unlink($path . '/' . $uploadedFileName);

		$this->assertTrue(
			File::upload($path . '/' . $name, $path . '/' . $uploadedFileName, true)
		);
		unlink($path . '/' . $uploadedFileName);

		unlink($path . '/' . $name);
		unset($_FILES);
	}

	/**
	 * Test upload method's destination inaccessible exception.
	 *
	 * @return void
	 *
	 * @expectedException \Joomla\Filesystem\Exception\FilesystemException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testUploadDestInaccessibleException()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root');
		$uploadedFileName = 'uploadedFileName';
		$data = 'Lorem ipsum dolor sit amet';
		include_once __DIR__ . '/Stubs/PHPUploadStub.php';

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		File::upload($path . '/' . $name, '/' . $uploadedFileName);
	}
}
