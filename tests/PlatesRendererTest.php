<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Tests;

use Joomla\Renderer\PlatesRenderer;
use League\Plates\Engine;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Renderer\PlatesRenderer.
 */
class PlatesRendererTest extends TestCase
{
	/**
	 * @testdox  The Plates renderer is instantiated with default parameters
	 *
	 * @covers   Joomla\Renderer\PlatesRenderer
	 */
	public function testThePlatesRendererIsInstantiatedWithDefaultParameters()
	{
		$this->assertInstanceOf(Engine::class, (new PlatesRenderer)->getRenderer());
	}

	/**
	 * @testdox  The Plates renderer is instantiated with injected parameters
	 *
	 * @covers   Joomla\Renderer\PlatesRenderer
	 */
	public function testThePlatesRendererIsInstantiatedWithInjectedParameters()
	{
		$engine = new Engine;

		$this->assertSame($engine, (new PlatesRenderer($engine))->getRenderer());
	}

	/**
	 * @testdox  An additional path is added to the renderer
	 *
	 * @covers   Joomla\Renderer\PlatesRenderer
	 */
	public function testAnAdditionalPathIsAddedToTheRenderer()
	{
		$renderer = new PlatesRenderer;
		$path     = __DIR__ . '/stubs/plates';

		$this->assertSame($renderer, $renderer->addFolder($path, 'test'), 'The addFolder method has a fluent interface');
		$this->assertTrue($renderer->getRenderer()->getFolders()->exists('test'));
	}

	/**
	 * @testdox  The rendering engine is returned
	 *
	 * @covers   Joomla\Renderer\PlatesRenderer
	 */
	public function testTheRenderingEngineIsReturned()
	{
		$engine = new Engine;

		$this->assertSame($engine, (new PlatesRenderer($engine))->getRenderer());
	}

	/**
	 * @testdox  Check that a path exists
	 *
	 * @covers   Joomla\Renderer\PlatesRenderer
	 */
	public function testCheckThatAPathExists()
	{
		$this->assertTrue((new PlatesRenderer(new Engine(__DIR__ . '/stubs/plates')))->pathExists('index'));
	}

	/**
	 * @testdox  The template is rendered
	 *
	 * @covers   Joomla\Renderer\PlatesRenderer
	 */
	public function testTheTemplateIsRendered()
	{
		$path = __DIR__ . '/stubs/plates';

		$this->assertStringEqualsFile($path . '/index.php', (new PlatesRenderer(new Engine($path)))->render('index'));
	}

	/**
	 * @testdox  The file extension is set
	 *
	 * @covers   Joomla\Renderer\PlatesRenderer
	 */
	public function testTheFileExtensionIsSet()
	{
		$renderer = new PlatesRenderer;

		$this->assertSame($renderer, $renderer->setFileExtension('tpl'), 'The setFileExtension has a fluent interface');
		$this->assertSame('tpl', $renderer->getRenderer()->getFileExtension());
	}
}
