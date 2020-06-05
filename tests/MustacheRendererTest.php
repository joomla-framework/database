<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Tests;

use Joomla\Renderer\MustacheRenderer;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Renderer\MustacheRenderer.
 */
class MustacheRendererTest extends TestCase
{
	/**
	 * Data provider for path existence checks
	 *
	 * @return  \Generator
	 */
	public function dataPathExists(): \Generator
	{
		yield 'Existing file' => ['index.mustache', true];
		yield 'Non-existing file' => ['error.mustache', false];
	}

	/**
	 * @testdox  The Mustache renderer is instantiated with default parameters
	 *
	 * @covers   Joomla\Renderer\MustacheRenderer
	 */
	public function testTheMustacheRendererIsInstantiatedWithDefaultParameters()
	{
		$this->assertInstanceOf(\Mustache_Engine::class, (new MustacheRenderer)->getRenderer());
	}

	/**
	 * @testdox  The rendering engine is returned
	 *
	 * @covers   Joomla\Renderer\MustacheRenderer
	 */
	public function testTheRenderingEngineIsReturned()
	{
		$engine = new \Mustache_Engine;

		$this->assertSame($engine, (new MustacheRenderer($engine))->getRenderer());
	}

	/**
	 * @testdox  Check that a path exists
	 *
	 * @param   string   $file    File to test for existance
	 * @param   boolean  $result  Expected result
	 *
	 * @covers   Joomla\Renderer\MustacheRenderer
	 * @dataProvider  dataPathExists
	 */
	public function testCheckThatAPathExists($file, $result)
	{
		$engine = new \Mustache_Engine(
			[
				'loader' => new \Mustache_Loader_FilesystemLoader(__DIR__ . '/stubs/mustache'),
			]
		);

		$this->assertSame($result, (new MustacheRenderer($engine))->pathExists($file));
	}

	/**
	 * @testdox  The template is rendered
	 *
	 * @covers   Joomla\Renderer\MustacheRenderer
	 */
	public function testTheTemplateIsRendered()
	{
		$path = __DIR__ . '/stubs/mustache';

		$engine = new \Mustache_Engine(
			[
				'loader' => new \Mustache_Loader_FilesystemLoader(__DIR__ . '/stubs/mustache'),
			]
		);

		$this->assertStringEqualsFile($path . '/index.mustache', (new MustacheRenderer($engine))->render('index.mustache'));
	}
}
