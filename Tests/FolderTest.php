<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Tests;

use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;

/**
 * Test class for Joomla\Filesystem\Folder.
 *
 * @since  1.0
 */
class FolderTest extends FilesystemTestCase
{
	/**
	 * Tests the Folder::copy method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCopyWithPathArgPassed()
	{
		$name             = 'tempFolder';
		$copiedFolderName = 'tempCopiedFolderName';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertTrue(
			Folder::copy($name, $copiedFolderName, $this->testPath),
			'The test directory was not copied.'
		);
	}

	/**
	 * Tests the Folder::copy method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCopyWithoutPathArgPassed()
	{
		$name             = 'tempFolder';
		$copiedFolderName = 'tempCopiedFolderName';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertTrue(
			Folder::copy($this->testPath . '/' . $name, $this->testPath . '/' . $copiedFolderName),
			'The test directory was not copied.'
		);
	}
	/**
	 * Tests the Folder::copy method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCopyWithStreams()
	{
		$name             = 'tempFolder';
		$copiedFolderName = 'tempCopiedFolderName';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertTrue(
			Folder::copy($name, $copiedFolderName, $this->testPath, false, true),
			'The test directory was not copied.'
		);
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
		$name             = 'tempFolder';
		$copiedFolderName = 'tempCopiedFolderName';

		Folder::copy($name, $copiedFolderName, $this->testPath);
	}

	/**
	 * Test the Folder::copy method where destination folder exists and the copy is forced.
	 *
	 * @return void
	 *
	 * @since   1.0
	 */
	public function testCopyDestExistAndForced()
	{
		$name             = 'tempFolder';
		$copiedFolderName = 'tempCopiedFolderName';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $copiedFolderName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertTrue(
			Folder::copy($name, $copiedFolderName, $this->testPath, true),
			'The test directory was not forcibly copied.'
		);
	}

	/**
	 * Test the Folder::copy method where destination folder exists and the copy is not forced.
	 *
	 * @return void
	 *
	 * @expectedException Joomla\Filesystem\Exception\FilesystemException
	 * @since   1.0
	 */
	public function testCopyDestExistAndNotForced()
	{
		$name             = 'tempFolder';
		$copiedFolderName = 'tempCopiedFolderName';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $copiedFolderName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		Folder::copy($name, $copiedFolderName, $this->testPath, false);
	}

	/**
	 * Tests the Folder::create method for a nested directory.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCreateNested()
	{
		$this->assertTrue(
			Folder::create($this->testPath . '/tempFolder/subTempFolder'),
			'The nested directory was not created.'
		);
	}

	/**
	 * Tests the Folder::create method for a potential infinite loop.
	 *
	 * @return  void
	 *
	 * @expectedException Joomla\Filesystem\Exception\FilesystemException
	 * @since   1.0
	 */
	public function testCreateInfiniteLoopException()
	{
		Folder::create($this->testPath . '/a/b/c/d/e/f/g/h/i/j/k/l/m/n/o/p/q/r/s/t/u/v/w/x/y/z');
	}

	/**
	 * Tests the Folder::delete method for a nested directory.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testDeleteRecursive()
	{
		$name = 'tempFolder';
		$data = 'Lorem ipsum dolor sit amet';

		if (!Folder::create($this->testPath . '/' . $name . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/' . $name . '/' . $name . '.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertTrue(
			Folder::delete($this->testPath . '/' . $name),
			'The test directory and its children were not deleted.'
		);
	}

	/**
	 * Tests the Folder::delete method blocks removal of the root directory.
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
	 * Tests the Folder::move method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testMoveWithPathArgPassed()
	{
		$name            = 'tempFolder';
		$movedFolderName = 'tempMovedFolderName';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertTrue(
			Folder::move($name, $movedFolderName, $this->testPath),
			'The test directory was not moved.'
		);
	}

	/**
	 * Tests the Folder::move method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testMoveWithoutPathArgPassed()
	{
		$name            = 'tempFolder';
		$movedFolderName = 'tempMovedFolderName';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertTrue(
			Folder::move($this->testPath . '/' . $name, $this->testPath . '/' . $movedFolderName),
			'The test directory was not moved.'
		);
	}

	/**
	 * Tests the Folder::move method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testMoveWithStreams()
	{
		$this->markTestSkipped('Need to debug internals');

		$name            = 'tempFolder';
		$movedFolderName = 'tempMovedFolderName';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertTrue(
			Folder::move($name, $movedFolderName, $this->testPath, true),
			'The test directory was not moved.'
		);
	}

	/**
	 * Test the Folder::move method where source folder doesn't exist.
	 *
	 * @return void
	 *
	 * @since   1.0
	 */
	public function testMoveSrcDontExist()
	{
		$name            = 'tempFolder';
		$movedFolderName = 'tempMovedFolderName';

		$this->assertSame(
			'Cannot find source folder',
			Folder::move($name, $movedFolderName, $this->testPath)
		);
	}

	/**
	 * Test the Folder::move method where destination folder exists and the move is not forced.
	 *
	 * @return void
	 *
	 * @since   1.0
	 */
	public function testMoveDestExist()
	{
		$name            = 'tempFolder';
		$movedFolderName = 'tempMovedFolderName';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $movedFolderName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			'Folder already exists',
			Folder::move($name, $movedFolderName, $this->testPath)
		);
	}

	/**
	 * Tests the Folder::files method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFiles()
	{
		$name = 'tempFolder';
		$data = 'Lorem ipsum dolor sit amet';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.html', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertSame(
			array(
				'index.html',
				'index.txt',
			),
			Folder::files($this->testPath . '/' . $name),
			'The files within a directory should be listed'
		);
	}

	/**
	 * Tests the Folder::files method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFilesWithExcludeList()
	{
		$name = 'tempFolder';
		$data = 'Lorem ipsum dolor sit amet';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.html', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertSame(
			array(
				'index.txt',
			),
			Folder::files($this->testPath . '/' . $name, '.', false, false, array('index.html')),
			'The files within a directory should be listed unless excluded'
		);
	}

	/**
	 * Tests the Folder::files method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFilesWithFullPath()
	{
		$name = 'tempFolder';
		$data = 'Lorem ipsum dolor sit amet';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.html', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertSame(
			array(
				Path::clean($this->testPath . '/' . $name . '/index.html'),
				Path::clean($this->testPath . '/' . $name . '/index.txt'),
			),
			Folder::files($this->testPath . '/' . $name, '.', false, true),
			'Files should be listed with their full paths'
		);
	}

	/**
	 * Tests the Folder::files method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFilesWithFilter()
	{
		$name = 'tempFolder';
		$data = 'Lorem ipsum dolor sit amet';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.html', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertSame(
			array(
				'index.html',
			),
			Folder::files($this->testPath . '/' . $name, 'index.html'),
			'Only files matching the filter should be listed'
		);
	}

	/**
	 * Tests the Folder::files method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFilesWithRecursiveFilter()
	{
		$name = 'tempFolder';
		$data = 'Lorem ipsum dolor sit amet';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.html', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/' . $name . '/index.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/' . $name . '/index.html', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertSame(
			array(
				'index.html',
				'index.html',
			),
			Folder::files($this->testPath . '/' . $name, 'index.html', true),
			'Only files matching the filter in nested directories should be listed'
		);
	}

	/**
	 * Tests the Folder::files method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFilesWithRecursiveFullPath()
	{
		$name = 'tempFolder';
		$data = 'Lorem ipsum dolor sit amet';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/index.html', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/' . $name . '/index.txt', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		if (!File::write($this->testPath . '/' . $name . '/' . $name . '/index.html', $data))
		{
			$this->markTestSkipped('The test file could not be created.');
		}

		$this->assertSame(
			array(
				Path::clean($this->testPath . '/' . $name . '/index.html'),
				Path::clean($this->testPath . '/' . $name . '/index.txt'),
				Path::clean($this->testPath . '/' . $name . '/' . $name . '/index.html'),
				Path::clean($this->testPath . '/' . $name . '/' . $name . '/index.txt'),
			),
			Folder::files($this->testPath . '/' . $name, '.', true, true),
		'Files in all nested directories should be listed with their full paths'
		);
	}

	/**
	 * Tests the Folder::files method.
	 *
	 * @return  void
	 *
	 * @expectedException  \UnexpectedValueException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testFilesWithNonexistingDirectory()
	{
		$name = 'tempFolder';

		Folder::files($this->testPath . '/' . $name);
	}

	/**
	 * Tests the Folder::folders method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFolders()
	{
		$name = 'tempFolder';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				$name,
			),
			Folder::folders($this->testPath),
			'The folders within a directory should be listed'
		);
	}

	/**
	 * Tests the Folder::folders method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFoldersWithExcludeList()
	{
		$name        = 'tempFolder';
		$excludeName = 'otherFolder';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $excludeName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				$name,
			),
			Folder::folders($this->testPath, '.', false, false, array($excludeName)),
			'The folders within a directory should be listed unless excluded'
		);
	}

	/**
	 * Tests the Folder::folders method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFoldersWithFullPath()
	{
		$name = 'tempFolder';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				Path::clean($this->testPath . '/' . $name),
			),
			Folder::folders($this->testPath, '.', false, true),
			'Folders should be listed with their full paths'
		);
	}

	/**
	 * Tests the Folder::folders method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFoldersWithFilter()
	{
		$name        = 'tempFolder';
		$excludeName = 'otherFolder';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $excludeName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				$name,
			),
			Folder::folders($this->testPath, $name),
			'Only folders matching the filter should be listed'
		);
	}

	/**
	 * Tests the Folder::folders method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFoldersWithRecursiveFilter()
	{
		$name        = 'tempFolder';
		$excludeName = 'otherFolder';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $excludeName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $excludeName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $excludeName . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $excludeName . '/' . $excludeName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				$name,
				$name,
				$name,
			),
			Folder::folders($this->testPath, $name, true),
			'Only folders matching the filter in nested directories should be listed'
		);
	}

	/**
	 * Tests the Folder::folders method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testFoldersWithRecursiveFullPath()
	{
		$name        = 'tempFolder';
		$excludeName = 'otherFolder';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $excludeName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $excludeName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $excludeName . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $excludeName . '/' . $excludeName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				Path::clean($this->testPath . '/' . $excludeName),
				Path::clean($this->testPath . '/' . $excludeName . '/' . $excludeName),
				Path::clean($this->testPath . '/' . $excludeName . '/' . $name),
				Path::clean($this->testPath . '/' . $name),
				Path::clean($this->testPath . '/' . $name . '/' . $excludeName),
				Path::clean($this->testPath . '/' . $name . '/' . $name),
			),
			Folder::folders($this->testPath, '.', true, true),
		'Folders in all nested directories should be listed with their full paths'
		);
	}

	/**
	 * Tests the Folder::folders method.
	 *
	 * @return  void
	 *
	 * @expectedException  \UnexpectedValueException
	 * @since   __DEPLOY_VERSION__
	 */
	public function testFoldersWithNonexistingDirectory()
	{
		$name = 'tempFolder';

		Folder::folders($this->testPath . '/' . $name);
	}

	/**
	 * Tests the Folder::listFolderTree method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testListFolderTreeWithEmptyDirectory()
	{
		$name = 'tempFolder';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(),
			Folder::listFolderTree($this->testPath . '/' . $name, '.'),
			'There should not be a folder tree for an empty directory.'
		);
	}

	/**
	 * Tests the Folder::listFolderTree method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testListFolderTreeWithASubirectory()
	{
		$name      = 'tempFolder';
		$childName = 'subTempFolder';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				array(
					'id'       => 1,
					'parent'   => 0,
					'name'     => $childName,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName),
				),
			),
			Folder::listFolderTree($this->testPath . '/' . $name, '.'),
			'The folder tree was not listed as expected.'
		);
	}

	/**
	 * Tests the Folder::listFolderTree method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testListFolderTreeWithMultipleSubirectories()
	{
		$name       = 'tempFolder';
		$childName1 = 'subTempFolder1';
		$childName2 = 'subTempFolder2';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName1))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName2))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				array(
					'id'       => 1,
					'parent'   => 0,
					'name'     => $childName1,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName1),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName1),
				),
				array(
					'id'       => 2,
					'parent'   => 0,
					'name'     => $childName2,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName2),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName2),
				),
			),
			Folder::listFolderTree($this->testPath . '/' . $name, '.'),
			'The folder tree was not listed as expected.'
		);
	}

	/**
	 * Tests the Folder::listFolderTree method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testListFolderTreeWithANestedSubirectory()
	{
		$name         = 'tempFolder';
		$childName    = 'subTempFolder';
		$subChildName = 'subSubTempFolder';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName . '/' . $subChildName))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				array(
					'id'       => 1,
					'parent'   => 0,
					'name'     => $childName,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName),
				),
				array(
					'id'       => 2,
					'parent'   => 1,
					'name'     => $subChildName,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName . '/' . $subChildName),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName . '/' . $subChildName),
				),
			),
			Folder::listFolderTree($this->testPath . '/' . $name, '.'),
			'The folder tree was not listed as expected.'
		);
	}

	/**
	 * Tests the Folder::listFolderTree method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testListFolderTreeWithMultipleNestedSubirectories()
	{
		$name          = 'tempFolder';
		$childName1    = 'subTempFolder1';
		$childName2    = 'subTempFolder2';
		$subChildName1 = 'subSubTempFolder1';
		$subChildName2 = 'subSubTempFolder2';

		if (!Folder::create($this->testPath . '/' . $name))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName1))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName1))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName2))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName2))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName1))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		if (!Folder::create($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName2))
		{
			$this->markTestSkipped('The test directory could not be created.');
		}

		$this->assertSame(
			array(
				array(
					'id'       => 1,
					'parent'   => 0,
					'name'     => $childName1,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName1),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName1),
				),
				array(
					'id'       => 2,
					'parent'   => 1,
					'name'     => $subChildName1,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName1),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName1),
				),
				array(
					'id'       => 3,
					'parent'   => 1,
					'name'     => $subChildName2,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName2),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName1 . '/' . $subChildName2),
				),
				array(
					'id'       => 4,
					'parent'   => 0,
					'name'     => $childName2,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName2),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName2),
				),
				array(
					'id'       => 5,
					'parent'   => 4,
					'name'     => $subChildName1,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName1),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName1),
				),
				array(
					'id'       => 6,
					'parent'   => 4,
					'name'     => $subChildName2,
					'fullname' => Path::clean($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName2),
					'relname'  => Path::clean($this->testPath . '/' . $name . '/' . $childName2 . '/' . $subChildName2),
				),
			),
			Folder::listFolderTree($this->testPath . '/' . $name, '.'),
			'The folder tree was not listed as expected.'
		);
	}

	/**
	 * Tests the Folder::makeSafe method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testMakeSafe()
	{
		$this->assertSame(
			'test1/testdirectory',
			Folder::makeSafe('test1/testdirectory')
		);
	}
}
