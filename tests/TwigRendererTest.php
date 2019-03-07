<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Tests;

use Joomla\Renderer\TwigRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

/**
 * Test class for \Joomla\Renderer\TwigRenderer.
 */
class TwigRendererTest extends TestCase
{
	/**
	 * @testdox  The Twig renderer is instantiated with default parameters
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::__construct
	 */
	public function testTheTwigRendererIsInstantiatedWithDefaultParameters()
	{
		$renderer = new TwigRenderer;

		$this->assertAttributeInstanceOf(Environment::class, 'renderer', $renderer);

		$this->assertAttributeInstanceOf(
			FilesystemLoader::class,
			'loader',
			$renderer->getRenderer(),
			'A default FilesystemLoader instance should be set as the loader for the environment.'
		);
	}

	/**
	 * @testdox  The Twig renderer is instantiated with injected parameters
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::__construct
	 */
	public function testTheTwigRendererIsInstantiatedWithInjectedParameters()
	{
		$environment = new Environment(new ArrayLoader([]));
		$renderer    = new TwigRenderer($environment);

		$this->assertAttributeSame($environment, 'renderer', $renderer);
	}

	/**
	 * @testdox  A path is added to the environment's loader when it is a filesystem loader
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::addFolder
	 */
	public function testAPathIsAddedToTheEnvironmentsLoaderWhenItIsAFilesystemLoader()
	{
		$renderer = new TwigRenderer;
		$path     = __DIR__ . '/stubs/twig';

		$this->assertSame($renderer, $renderer->addFolder($path), 'Validates $this is returned');
		$this->assertTrue(\in_array($path, $renderer->getRenderer()->getLoader()->getPaths()));
	}

	/**
	 * @testdox  The rendering engine is returned
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::getRenderer
	 */
	public function testTheRenderingEngineIsReturned()
	{
		$environment = new Environment(new ArrayLoader([]));
		$renderer    = new TwigRenderer($environment);

		$this->assertSame($environment, $renderer->getRenderer());
	}

	/**
	 * @testdox  Check that a path exists when the loader supports checking for existence
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::pathExists
	 */
	public function testCheckThatAPathExistsWhenTheLoaderSupportsCheckingForExistence()
	{
		$renderer = new TwigRenderer;
		$renderer->addFolder(__DIR__ . '/stubs/twig');

		$this->assertTrue($renderer->pathExists('index.twig'));
	}

	/**
	 * @testdox  A path cannot be checked for existence when the loader does not support checking it
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::pathExists
	 */
	public function testAPathCannotBeCheckedForExistenceWhenTheLoaderDoesNotSupportCheckingIt()
	{
		// Only test when LoaderInterface does not contain the exists method
		if (method_exists(LoaderInterface::class, 'exists'))
		{
			$this->markTestSkipped('Test only applies for Twig 1.x');
		}

		$loader = $this->getMockBuilder(LoaderInterface::class)
			->getMock();

		$renderer = new TwigRenderer(new Environment($loader));

		$this->assertTrue($renderer->pathExists('index.twig'));
	}

	/**
	 * @testdox  The template is rendered
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::render
	 */
	public function testTheTemplateIsRendered()
	{
		$path = __DIR__ . '/stubs/twig';

		$renderer = new TwigRenderer;
		$renderer->addFolder($path);

		$this->assertSame(file_get_contents($path . '/index.twig'), $renderer->render('index.twig'));
	}
}
