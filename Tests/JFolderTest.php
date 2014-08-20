<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use org\bovigo\vfs\vfsStream;

/**
 * Test class for JFolder.
 *
 * @since  1.0
 */
class FolderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Joomla\Filesystem\Folder
	 *
	 * @since __VERSION_NO__
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 *
	 * @since __VERSION_NO__
	 */
	protected function setUp()
	{
		parent::setUp();

		vfsStream::setup('root');
	}

	/**
	 * Tests the Folder::copy method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCopy()
	{
		$name = 'tempFolder';
		$copiedFolderName = 'tempCopiedFolderName';
		$path = vfsStream::url('root');

		Folder::create($path . '/' . $name);

		$this->assertThat(
			Folder::copy($name, $copiedFolderName, $path),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be copied successfully.'
		);
		Folder::delete($path . '/' . $copiedFolderName);

		$this->assertThat(
			Folder::copy($path . '/' . $name, $path . '/' . $copiedFolderName),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be copied successfully.'
		);
		Folder::delete($path . '/' . $copiedFolderName);

		Folder::delete($path . '/' . $name);
	}

	/**
	 * Test the Folder::copy method where source folder doesn't exist.
	 *
	 * @return void
	 *
	 * @expectedException Joomla\Filesystem\Exception\FilesystemException
	 * @since   1.0
	 */
	public function testCopySrcDontExist()
	{
		$name = 'tempFolder';
		$copiedFolderName = 'tempCopiedFolderName';
		$path = vfsStream::url('root') . '/tmp';

		Folder::copy($path . '/' . $name . 'foobar', $path . '/' . $copiedFolderName);

		Folder::delete($path . '/' . $copiedFolderName);
		Folder::delete($path . '/' . $name);
	}

	/**
	 * Test the Folder::copy method where destination folder exist already.
	 *
	 * @return void
	 *
	 * @since   1.0
	 */
	public function testCopyDestExist()
	{
		$name = 'tempFolder';
		$copiedFolderName = 'tempCopiedFolderName';
		$path = vfsStream::url('root');

		Folder::create($path . '/' . $name);
		Folder::create($path . '/' . $copiedFolderName);

		// Destination folder exist already and copy is forced.
		$this->assertThat(
			Folder::copy($name, $copiedFolderName, $path, true),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be copied successfully.'
		);

		try
		{
			Folder::copy($name, $copiedFolderName, $path);
		}
		catch (Exception $exception)
		{
			// Destination folder exist already and copy is not forced.
			$this->assertInstanceOf(
				'RuntimeException',
				$exception,
				'Line:' . __LINE__ . ' Folder should not be copied successfully.'
			);
		}

		Folder::delete($path . '/' . $copiedFolderName);

		Folder::delete($path . '/' . $name);
	}

	/**
	 * Tests the Folder::create method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCreate()
	{
		$name = 'tempFolder';
		$path = vfsStream::url('root');

		$this->assertThat(
			Folder::create($path . '/' . $name),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be created successfully.'
		);

		// Already existing directory (made by previous call).
		$this->assertThat(
			Folder::create($path . '/' . $name),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be created successfully.'
		);

		Folder::delete($path . '/' . $name);

		// Creating parent directory recursively.
		$this->assertThat(
			Folder::create($path . '/' . $name . '/' . $name),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be created successfully.'
		);

		Folder::delete($path . '/' . $name . '/' . $name);
	}

	/**
	 * Tests the Folder::create method.
	 *
	 * @return  void
	 *
	 * @expectedException Joomla\Filesystem\Exception\FilesystemException
	 * @since   1.0
	 */
	public function testCreateInfiniteLoopException()
	{
		$name = 'tempFolder';

		// Checking for infinite loop in the path.
		$path = vfsStream::url('root') . '/a/b/c/d/e/f/g/h/i/j/k/l/m/n/o/p/q/r/s/t/u/v/w/x/y/z';
		$this->assertThat(
			Folder::create($path . '/' . $name),
			$this->isFalse(),
			'Line:' . __LINE__ . ' Folder should be created successfully.'
		);

		Folder::delete($path . '/' . $name);
	}

	/**
	 * Tests the Folder::delete method.
	 *
	 * @return  void
	 *
	 * @requires PHP 5.4
	 * @since   1.0
	 */
	public function testDelete()
	{
		$name = 'tempFolder';
		$path = vfsStream::url('root');

		Folder::create($path . '/' . $name);

		$this->assertThat(
			Folder::delete($path . '/' . $name),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be deleted successfully.'
		);

		// Create a folder and a sub-folder and file in it.
		$data = 'Lorem ipsum dolor sit amet';
		Folder::create($path . '/' . $name);
		File::write($path . '/' . $name . '/' . $name . '.txt', $data);
		Folder::create($path . '/' . $name . '/' . $name);

		$this->assertThat(
			Folder::delete($path . '/' . $name),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder and its sub folder & files should be deleted successfully.'
		);
	}

	/**
	 * Tests the Folder::delete method.
	 *
	 * @return  void
	 *
	 * @expectedException  Joomla\Filesystem\Exception\FilesystemException
	 * @since   1.0
	 */
	public function testDeleteBaseDir()
	{
		Folder::delete('');
	}

	/**
	 * Tests the Folder::delete method.
	 *
	 * @return  void
	 *
	 * @expectedException  UnexpectedValueException
	 * @since   1.0
	 */
	public function testDeleteFile()
	{
		$name = 'tempFile';
		$path = vfsStream::url('root') . '/tmp';
		$data = 'Lorem ipsum dolor sit amet';

		// Create tmp directory at vfsStream root
		mkdir($path);

		// Create a temp file to test copy operation
		file_put_contents($path . '/' . $name, $data);

		// Testing file delete.
		Folder::delete($path . '/' . $name);

		unlink($path . '/' . $name);
	}

	/**
	 * Tests the Folder::delete method with an array as an input
	 *
	 * @return  void
	 *
	 * @expectedException  UnexpectedValueException
	 * @since __VERSION_NO__
	 */
	public function testDeleteArrayPath()
	{
		Folder::delete(array('/path/to/folder'));
	}

	/**
	 * Tests the Folder::move method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testMove()
	{
		$name = 'tempFolder';
		$movedFolderName = 'tempMovedFolderName';
		$path = __DIR__;

		Folder::create($path . '/' . $name);

		$this->assertThat(
			Folder::move($name, $movedFolderName, $path),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be moved successfully.'
		);

		// Testing using streams.
		$this->assertThat(
			Folder::move($movedFolderName, $name, $path, true),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be moved successfully.'
		);

		$this->assertThat(
			Folder::move($path . '/' . $name, $path . '/' . $movedFolderName),
			$this->isTrue(),
			'Line:' . __LINE__ . ' Folder should be moved successfully.'
		);

		// Testing condition of source folder don't exist.
		$this->assertEquals(
			Folder::move($name, $movedFolderName, $path),
			'Cannot find source folder',
			'Line:' . __LINE__ . ' Folder should not be moved successfully.'
		);

		// Testing condition of dest folder exist already.
		$this->assertEquals(
			Folder::move($movedFolderName, $movedFolderName, $path),
			'Folder already exists',
			'Line:' . __LINE__ . ' Folder should not be moved successfully.'
		);

		Folder::delete($path . '/' . $movedFolderName);
	}

	/**
	 * Tests the Folder::files method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Filesystem\Folder::files
	 * @covers  Joomla\Filesystem\Folder::_items
	 * @since   1.0
	 */
	public function testFiles()
	{
		$root = vfsStream::url('root') . '/tmp';

		mkdir($root);

		// Make some test files and folders
		mkdir(Path::clean($root . '/test'), 0777, true);
		file_put_contents(Path::clean($root . '/test/index.html'), 'test');
		file_put_contents(Path::clean($root . '/test/index.txt'), 'test');
		mkdir(Path::clean($root . '/test/test'), 0777, true);
		file_put_contents(Path::clean($root . '/test/test/index.html'), 'test');
		file_put_contents(Path::clean($root . '/test/test/index.txt'), 'test');

		// Use of realpath to ensure test works for on all platforms
		$result = Folder::files(Path::clean($root . '/test'), 'index.*', true, true, array('index.html'));

		$this->assertEquals(
			array(
				Path::clean($root . '/test/index.txt'),
				Path::clean($root . '/test/test/index.txt')
			),
			$result,
			'Line: ' . __LINE__ . ' Should exclude index.html files'
		);

		// Use of realpath to ensure test works for on all platforms
		$result = Folder::files(Path::clean($root . '/test'), 'index.html', true, true);

		$this->assertEquals(
			array(
				Path::clean($root . '/test/index.html'),
				Path::clean($root . '/test/test/index.html')
			),
			$result,
			'Line: ' . __LINE__ . ' Should include full path of both index.html files'
		);

		$this->assertEquals(
			array(
				Path::clean('index.html'),
				Path::clean('index.html')
			),
			Folder::files(Path::clean($root . '/test'), 'index.html', true, false),
			'Line: ' . __LINE__ . ' Should include only file names of both index.html files'
		);

		// Use of realpath to ensure test works for on all platforms
		$result = Folder::files(Path::clean($root . '/test'), 'index.html', false, true);

		$this->assertEquals(
			array(
				Path::clean($root . '/test/index.html')
			),
			$result,
			'Line: ' . __LINE__ . ' Non-recursive should only return top folder file full path'
		);

		$this->assertEquals(
			array(
				Path::clean('index.html')
			),
			Folder::files(Path::clean($root . '/test'), 'index.html', false, false),
			'Line: ' . __LINE__ . ' non-recursive should return only file name of top folder file'
		);

		$this->assertEquals(
			array(),
			Folder::files(Path::clean($root . '/test'), 'nothing.here', true, true, array(), array()),
			'Line: ' . __LINE__ . ' When nothing matches the filter, should return empty array'
		);
	}

	/**
	 * Tests the Folder::files method.
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Filesystem\Folder::files
	 * @expectedException  \UnexpectedValueException
	 * @since __VERSION_NO__
	 */
	public function testFilesException()
	{
		Folder::files('/this/is/not/a/path');
	}

	/**
	 * Tests the Folder::folders method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Filesystem\Folder::files
	 * @covers  Joomla\Filesystem\Folder::folders
	 * @covers  Joomla\Filesystem\Folder::_items
	 * @since   1.0
	 */
	public function testFolders()
	{
		$root = vfsStream::url('root') . '/tmp';

		mkdir($root);

		// Create the test folders
		mkdir(Path::clean($root . '/test'), 0777, true);
		mkdir(Path::clean($root . '/test/foo1'), 0777, true);
		mkdir(Path::clean($root . '/test/foo1/bar1'), 0777, true);
		mkdir(Path::clean($root . '/test/foo1/bar2'), 0777, true);
		mkdir(Path::clean($root . '/test/foo2'), 0777, true);
		mkdir(Path::clean($root . '/test/foo2/bar1'), 0777, true);
		mkdir(Path::clean($root . '/test/foo2/bar2'), 0777, true);

		$this->assertEquals(
			array(),
			Folder::folders(Path::clean($root . '/test'), 'bar1', true, true, array('foo1', 'foo2'))
		);

		// Use of realpath to ensure test works for on all platforms
		$result = Folder::folders(Path::clean($root . '/test'), 'bar1', true, true, array('foo1'));

		$this->assertEquals(
			array(Path::clean($root . '/test/foo2/bar1')),
			$result
		);

		// Use of realpath to ensure test works for on all platforms
		$result = Folder::folders(Path::clean($root . '/test'), 'bar1', true, true);

		$this->assertEquals(
			array(
				Path::clean($root . '/test/foo1/bar1'),
				Path::clean($root . '/test/foo2/bar1'),
			),
			$result
		);

		// Use of realpath to ensure test works for on all platforms
		$result = Folder::folders(Path::clean($root . '/test'), 'bar', true, true);

		$this->assertEquals(
			array(
				Path::clean($root . '/test/foo1/bar1'),
				Path::clean($root . '/test/foo1/bar2'),
				Path::clean($root . '/test/foo2/bar1'),
				Path::clean($root . '/test/foo2/bar2'),
			),
			$result
		);

		// Use of realpath to ensure test works for on all platforms
		$result = Folder::folders(Path::clean($root . '/test'), '.', true, true);

		$this->assertEquals(
			array(
				Path::clean($root . '/test/foo1'),
				Path::clean($root . '/test/foo1/bar1'),
				Path::clean($root . '/test/foo1/bar2'),
				Path::clean($root . '/test/foo2'),
				Path::clean($root . '/test/foo2/bar1'),
				Path::clean($root . '/test/foo2/bar2'),
			),
			$result
		);

		$this->assertEquals(
			array(
				Path::clean('bar1'),
				Path::clean('bar1'),
				Path::clean('bar2'),
				Path::clean('bar2'),
				Path::clean('foo1'),
				Path::clean('foo2'),
			),
			Folder::folders(Path::clean($root . '/test'), '.', true, false)
		);

		// Use of realpath to ensure test works for on all platforms
		$result = Folder::folders(Path::clean($root . '/test'), '.', false, true);

		$this->assertEquals(
			array(
				Path::clean($root . '/test/foo1'),
				Path::clean($root . '/test/foo2'),
			),
			$result
		);

		$this->assertEquals(
			array(
				Path::clean('foo1'),
				Path::clean('foo2'),
			),
			Folder::folders(Path::clean($root . '/test'), '.', false, false, array(), array())
		);

		// Clean up the folders
		rmdir(Path::clean($root . '/test/foo2/bar2'));
		rmdir(Path::clean($root . '/test/foo2/bar1'));
		rmdir(Path::clean($root . '/test/foo2'));
		rmdir(Path::clean($root . '/test/foo1/bar2'));
		rmdir(Path::clean($root . '/test/foo1/bar1'));
		rmdir(Path::clean($root . '/test/foo1'));
		rmdir(Path::clean($root . '/test'));
	}

	/**
	 * Tests the Folder::folders method for an exception.
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Filesystem\Folder::folders
	 * @expectedException  \UnexpectedValueException
	 * @since __VERSION_NO__
	 */
	public function testFoldersException()
	{
		Folder::folders('this/is/not/a/path');
	}

	/**
	 * Tests the Folder::listFolderTree method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testListFolderTree()
	{
		$name = 'tempFolder';
		$path = vfsStream::url('root');

		// -tempFolder
		Folder::create("$path/$name");
		$this->assertEquals(
			Folder::listFolderTree("$path/$name", '.'),
			array(),
			'Line: ' . __LINE__ . ' Observed folder tree is not correct.');

		// -tempFolder
		// ---SubFolder
		$subfullname = "$path/$name/SubFolder";
		$subrelname = str_replace(JPATH_ROOT, '', $subfullname);
		Folder::create($subfullname);
		$this->assertEquals(
			Folder::listFolderTree("$path/$name", '.'),
			array(
				array(
					'id' => 1,
					'parent' => 0,
					'name' => 'SubFolder',
					'fullname' => $subfullname,
					'relname' => $subrelname
				)
			),
			'Line: ' . __LINE__ . ' Observed folder tree is not correct.');

		/* -tempFolder
			---SubFolder
			---AnotherSubFolder
		*/
		$anothersubfullname = "$path/$name/AnotherSubFolder";
		$anothersubrelname = str_replace(JPATH_ROOT, '', $anothersubfullname);
		Folder::create($anothersubfullname);
		$this->assertEquals(
			Folder::listFolderTree("$path/$name", '.'),
			array(
				array(
					'id' => 1,
					'parent' => 0,
					'name' => 'AnotherSubFolder',
					'fullname' => $anothersubfullname,
					'relname' => $anothersubrelname
				),
				array(
					'id' => 2,
					'parent' => 0,
					'name' => 'SubFolder',
					'fullname' => $subfullname,
					'relname' => $subrelname
				)

			),
			'Line: ' . __LINE__ . ' Observed folder tree is not correct.');

		/* -tempFolder
				-SubFolder
					-SubSubFolder
				-AnotherSubFolder
		*/
		$subsubfullname = "$subfullname/SubSubFolder";
		$subsubrelname = str_replace(JPATH_ROOT, '', $subsubfullname);
		Folder::create($subsubfullname);
		$this->assertEquals(
			Folder::listFolderTree("$path/$name", '.'),
			array(
				array(
					'id' => 1,
					'parent' => 0,
					'name' => 'AnotherSubFolder',
					'fullname' => $anothersubfullname,
					'relname' => $anothersubrelname
				),
				array(
					'id' => 2,
					'parent' => 0,
					'name' => 'SubFolder',
					'fullname' => $subfullname,
					'relname' => $subrelname
				),
				array(
					'id' => 3,
					'parent' => 2,
					'name' => 'SubSubFolder',
					'fullname' => $subsubfullname,
					'relname' => $subsubrelname
				)

			),
			'Line: ' . __LINE__ . ' Observed folder tree is not correct.');

		Folder::delete($path . '/' . $name);
	}

	/**
	 * Tests the Folder::makeSafe method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Filesystem\Folder::makeSafe
	 * @since   1.0
	 */
	public function testMakeSafe()
	{
		$actual = Folder::makeSafe('test1/testdirectory');
		$this->assertEquals('test1/testdirectory', $actual);
	}
}
