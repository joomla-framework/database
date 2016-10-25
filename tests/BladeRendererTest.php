<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Tests;

use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Joomla\Renderer\BladeRenderer;

/**
 * Test class for \Joomla\Renderer\BladeRenderer.
 */
class BladeRendererTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox  The Blade renderer is instantiated with default parameters
	 *
	 * @covers   \Joomla\Renderer\BladeRenderer::__construct
	 */
	public function testTheBladeRendererIsInstantiatedWithDefaultParameters()
	{
		$renderer = new BladeRenderer;

		$this->assertAttributeInstanceOf(Factory::class, 'renderer', $renderer);
	}

	/**
	 * @testdox  The Blade renderer is instantiated with injected parameters
	 *
	 * @covers   \Joomla\Renderer\BladeRenderer::__construct
	 */
	public function testTheBladeRendererIsInstantiatedWithInjectedParameters()
	{
		$factory  = $this->makeFactory();
		$renderer = new BladeRenderer($factory);

		$this->assertAttributeSame($factory, 'renderer', $renderer);
	}

	/**
	 * @testdox  An additional path is added to the renderer
	 *
	 * @covers   \Joomla\Renderer\BladeRenderer::addFolder()
	 */
	public function testAnAdditionalPathIsAddedToTheRenderer()
	{
		$path     = __DIR__ . '/stubs/templating';
		$factory  = $this->makeFactory();
		$renderer = new BladeRenderer($factory);

		$this->assertSame($renderer, $renderer->addFolder($path, 'test'), 'Validates $this is returned');
		$this->assertTrue(in_array($path, $renderer->getRenderer()->getFinder()->getPaths()));
	}

	/**
	 * @testdox  The rendering engine is returned
	 *
	 * @covers   \Joomla\Renderer\BladeRenderer::getRenderer
	 */
	public function testTheRenderingEngineIsReturned()
	{
		$factory  = $this->makeFactory();
		$renderer = new BladeRenderer($factory);

		$this->assertSame($factory, $renderer->getRenderer());
	}

	/**
	 * @testdox  Check that a path exists
	 *
	 * @covers   \Joomla\Renderer\BladeRenderer::pathExists
	 */
	public function testCheckThatAPathExists()
	{
		$factory  = $this->makeFactory();
		$renderer = new BladeRenderer($factory);

		$this->assertTrue($renderer->pathExists('index'));
	}

	/**
	 * @testdox  The template is rendered
	 *
	 * @covers   \Joomla\Renderer\BladeRenderer::render
	 */
	public function testTheTemplateIsRendered()
	{
		$path = __DIR__ . '/stubs/blade';

		$factory  = $this->makeFactory();
		$renderer = new BladeRenderer($factory);

		$this->assertSame(file_get_contents($path . '/index.blade.php'), $renderer->render('index'));
	}

	/**
	 * @testdox  Setting the file extension is unsupported
	 *
	 * @covers   \Joomla\Renderer\BladeRenderer::setFileExtension
	 */
	public function testSettingTheFileExtensionIsUnsupported()
	{
		$factory  = $this->makeFactory();
		$renderer = new BladeRenderer($factory);

		$this->assertSame($renderer, $renderer->setFileExtension('php'), 'Validates $this is returned');
	}

	/**
	 * Make the Factory instance for testing
	 *
	 * @return  Factory
	 */
	private function makeFactory()
	{
		$filesystem = new Filesystem;

		$resolver = new EngineResolver;
		$resolver->register(
			'blade',
			function () use ($filesystem)
			{
				return new CompilerEngine(new BladeCompiler($filesystem, __DIR__ . '/stubs/blade/cache'));
			}
		);

		return new Factory(
			$resolver,
			new FileViewFinder($filesystem, [__DIR__ . '/stubs/blade']),
			new Dispatcher
		);
	}
}
