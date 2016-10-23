<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Tests;

use Joomla\Renderer\PlatesRenderer;
use League\Plates\Engine;

/**
 * Test class for \Joomla\Renderer\PlatesRenderer.
 */
class PlatesRendererTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox  The Plates renderer is instantiated with default parameters
	 *
	 * @covers   \Joomla\Renderer\PlatesRenderer::__construct
	 */
	public function testThePlatesRendererIsInstantiatedWithDefaultParameters()
	{
		$renderer = new PlatesRenderer;

		$this->assertAttributeInstanceOf(Engine::class, 'renderer', $renderer);
	}

	/**
	 * @testdox  The Plates renderer is instantiated with injected parameters
	 *
	 * @covers   \Joomla\Renderer\PlatesRenderer::__construct
	 */
	public function testThePlatesRendererIsInstantiatedWithInjectedParameters()
	{
		$engine   = new Engine;
		$renderer = new PlatesRenderer($engine);

		$this->assertAttributeSame($engine, 'renderer', $renderer);
	}

	/**
	 * @testdox  An additional path is added to the renderer
	 *
	 * @covers   \Joomla\Renderer\PlatesRenderer::addFolder()
	 */
	public function testAnAdditionalPathIsAddedToTheRenderer()
	{
		$renderer = new PlatesRenderer;
		$path     = __DIR__ . '/stubs/plates';

		$this->assertSame($renderer, $renderer->addFolder($path, 'test'), 'Validates $this is returned');

		// Switch between Plates 2.0 and 3.0 behavior
		if (method_exists($renderer->getRenderer(), 'getFolders'))
		{
			$this->assertTrue($renderer->getRenderer()->getFolders()->exists('test'), 'Validates the folder was added on Plates 3.x');
		}
		else
		{
			$this->assertAttributeContains('test', 'folders', $renderer->getRenderer(), 'Validates the folder was added on Plates 2.x');
		}
	}

	/**
	 * @testdox  The rendering engine is returned
	 *
	 * @covers   \Joomla\Renderer\PlatesRenderer::getRenderer
	 */
	public function testTheRenderingEngineIsReturned()
	{
		$engine   = new Engine;
		$renderer = new PlatesRenderer($engine);

		$this->assertSame($engine, $renderer->getRenderer());
	}

	/**
	 * @testdox  Check that a path exists
	 *
	 * @covers   \Joomla\Renderer\PlatesRenderer::pathExists
	 */
	public function testCheckThatAPathExists()
	{
		$engine   = new Engine(__DIR__ . '/stubs/plates');
		$renderer = new PlatesRenderer($engine);

		$this->assertTrue($renderer->pathExists('index'));
	}

	/**
	 * @testdox  The template is rendered
	 *
	 * @covers   \Joomla\Renderer\PlatesRenderer::render
	 */
	public function testTheTemplateIsRendered()
	{
		$path     = __DIR__ . '/stubs/plates';
		$engine   = new Engine($path);
		$renderer = new PlatesRenderer($engine);

		$this->assertSame(file_get_contents($path . '/index.php'), $renderer->render('index'));
	}

	/**
	 * @testdox  The file extension is set
	 *
	 * @covers   \Joomla\Renderer\PlatesRenderer::setFileExtension
	 */
	public function testTheFileExtensionIsSet()
	{
		$renderer = new PlatesRenderer;

		$this->assertSame($renderer, $renderer->setFileExtension('tpl'), 'Validates $this is returned');
		$this->assertSame('tpl', $renderer->getRenderer()->getFileExtension());
	}
}
