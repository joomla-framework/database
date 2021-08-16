<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
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
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Renderer\BladeRenderer.
 */
class BladeRendererTest extends TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		(new Filesystem)->makeDirectory(__DIR__ . '/stubs/blade/cache');
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown(): void
	{
		(new Filesystem)->deleteDirectory(__DIR__ . '/stubs/blade/cache');
	}

	/**
	 * @testdox  The Blade renderer is instantiated with default parameters
	 *
	 * @covers   Joomla\Renderer\BladeRenderer
	 */
	public function testTheBladeRendererIsInstantiatedWithDefaultParameters()
	{
		$this->assertInstanceOf(Factory::class, (new BladeRenderer)->getRenderer());
	}

	/**
	 * @testdox  The Blade renderer is instantiated with injected parameters
	 *
	 * @covers   Joomla\Renderer\BladeRenderer
	 */
	public function testTheBladeRendererIsInstantiatedWithInjectedParameters()
	{
		$factory = $this->makeFactory();

		$this->assertSame($factory, (new BladeRenderer($factory))->getRenderer());
	}

	/**
	 * @testdox  An additional path is added to the renderer
	 *
	 * @covers   Joomla\Renderer\BladeRenderer
	 */
	public function testAnAdditionalPathIsAddedToTheRenderer()
	{
		$path     = __DIR__ . '/stubs/templating';
		$renderer = new BladeRenderer($this->makeFactory());

		$this->assertSame($renderer, $renderer->addFolder($path, 'test'), 'The addFolder method has a fluent interface');
		$this->assertTrue(\in_array($path, $renderer->getRenderer()->getFinder()->getPaths()));
	}

	/**
	 * @testdox  The rendering engine is returned
	 *
	 * @covers   Joomla\Renderer\BladeRenderer
	 */
	public function testTheRenderingEngineIsReturned()
	{
		$factory = $this->makeFactory();

		$this->assertSame($factory, (new BladeRenderer($factory))->getRenderer());
	}

	/**
	 * @testdox  Check that a path exists
	 *
	 * @covers   Joomla\Renderer\BladeRenderer
	 */
	public function testCheckThatAPathExists()
	{
		$this->assertTrue((new BladeRenderer($this->makeFactory()))->pathExists('index'));
	}

	/**
	 * @testdox  The template is rendered
	 *
	 * @covers   Joomla\Renderer\BladeRenderer
	 */
	public function testTheTemplateIsRendered()
	{
		$this->assertStringEqualsFile(__DIR__ . '/stubs/blade/index.blade.php', (new BladeRenderer($this->makeFactory()))->render('index'));
	}

	/**
	 * Make the Factory instance for testing
	 *
	 * @return  Factory
	 */
	private function makeFactory(): Factory
	{
		$filesystem = new Filesystem;

		$resolver = new EngineResolver;
		$resolver->register(
			'blade',
			static function () use ($filesystem): CompilerEngine
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
