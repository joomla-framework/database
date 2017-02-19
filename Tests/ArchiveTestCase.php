<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Archive\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base test case for Archive tests
 */
abstract class ArchiveTestCase extends TestCase
{
	/**
	 * Input directory
	 *
	 * @var  string
	 */
	protected $inputPath;

	/**
	 * Output directory
	 *
	 * @var  string
	 */
	protected $outputPath;

	/**
	 * Sets up the fixture.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->inputPath  = __DIR__ . '/testdata';
		$this->outputPath = __DIR__ . '/output';

		if (!is_dir($this->outputPath))
		{
			if (!mkdir($this->outputPath, 0777))
			{
				$this->markTestSkipped("Couldn't create folder " . $this->outputPath);
			}
		}
	}
}
