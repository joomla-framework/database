<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
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
	 * @covers   \Joomla\Renderer\MustacheRenderer::__construct
	 */
	public function testTheMustacheRendererIsInstantiatedWithDefaultParameters()
	{
		$renderer = new MustacheRenderer;

		$this->assertAttributeInstanceOf('Mustache_Engine', 'renderer', $renderer);
	}

	/**
	 * @testdox  The Mustache renderer is instantiated with injected parameters
	 *
	 * @covers   \Joomla\Renderer\MustacheRenderer::__construct
	 */
	public function testTheMustacheRendererIsInstantiatedWithInjectedParameters()
	{
		$engine   = new \Mustache_Engine;
		$renderer = new MustacheRenderer($engine);

		$this->assertAttributeSame($engine, 'renderer', $renderer);
	}

	/**
	 * @testdox  The rendering engine is returned
	 *
	 * @covers   \Joomla\Renderer\MustacheRenderer::getRenderer
	 */
	public function testTheRenderingEngineIsReturned()
	{
		$engine   = new \Mustache_Engine;
		$renderer = new MustacheRenderer($engine);

		$this->assertSame($engine, $renderer->getRenderer());
	}

	/**
	 * @testdox  Check that a path exists
	 *
	 * @covers   \Joomla\Renderer\MustacheRenderer::pathExists
	 * @dataProvider  dataPathExists
	 *
	 * @param   string   $file    File to test for existance
	 * @param   boolean  $result  Expected result
	 */
	public function testCheckThatAPathExists($file, $result)
	{
		$engine = new \Mustache_Engine(
			[
				'loader' => new \Mustache_Loader_FilesystemLoader(__DIR__ . '/stubs/mustache'),
			]
		);

		$renderer = new MustacheRenderer($engine);

		$this->assertSame($result, $renderer->pathExists($file));
	}

	/**
	 * @testdox  The template is rendered
	 *
	 * @covers   \Joomla\Renderer\MustacheRenderer::render
	 */
	public function testTheTemplateIsRendered()
	{
		$path = __DIR__ . '/stubs/mustache';

		$engine = new \Mustache_Engine(
			[
				'loader' => new \Mustache_Loader_FilesystemLoader(__DIR__ . '/stubs/mustache'),
			]
		);

		$renderer = new MustacheRenderer($engine);

		$this->assertSame(file_get_contents($path . '/index.mustache'), $renderer->render('index.mustache'));
	}
}
