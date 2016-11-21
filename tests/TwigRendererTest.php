<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Tests;

use Joomla\Renderer\TwigRenderer;

/**
 * Test class for \Joomla\Renderer\TwigRenderer.
 */
class TwigRendererTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox  The Twig renderer is instantiated with default parameters
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::__construct
	 */
	public function testTheTwigRendererIsInstantiatedWithDefaultParameters()
	{
		$renderer = new TwigRenderer;

		$this->assertAttributeInstanceOf('Twig_Environment', 'renderer', $renderer);

		$this->assertAttributeInstanceOf(
			'Twig_Loader_Filesystem',
			'loader',
			$renderer->getRenderer(),
			'A default Twig_Loader_Filesystem instance should be set as the loader for the environment.'
		);
	}

	/**
	 * @testdox  The Twig renderer is instantiated with injected parameters
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::__construct
	 */
	public function testTheTwigRendererIsInstantiatedWithInjectedParameters()
	{
		$environment = new \Twig_Environment(new \Twig_Loader_Array([]));
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
		$this->assertTrue(in_array($path, $renderer->getRenderer()->getLoader()->getPaths()));
	}

	/**
	 * @testdox  The rendering engine is returned
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::getRenderer
	 */
	public function testTheRenderingEngineIsReturned()
	{
		$environment = new \Twig_Environment(new \Twig_Loader_Array([]));
		$renderer    = new TwigRenderer($environment);

		$this->assertSame($environment, $renderer->getRenderer());
	}

	/**
	 * @testdox  Check that a path exists when the loader implements Twig_ExistsLoaderInterface
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::pathExists
	 */
	public function testCheckThatAPathExistsWhenTheLoaderImplementsTwigExistsLoaderInterface()
	{
		$renderer = new TwigRenderer;
		$renderer->addFolder(__DIR__ . '/stubs/twig');

		$this->assertTrue($renderer->pathExists('index.twig'));
	}

	/**
	 * @testdox  A path cannot be checked for existence when the loader does not implement Twig_ExistsLoaderInterface
	 *
	 * @covers   \Joomla\Renderer\TwigRenderer::pathExists
	 */
	public function testAPathCannotBeCheckedForExistenceWhenTheLoaderDoesNotImplementTwigExistsLoaderInterface()
	{
		$loader = $this->getMockBuilder('Twig_LoaderInterface')
			->getMock();

		$renderer = new TwigRenderer(new \Twig_Environment($loader));

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
