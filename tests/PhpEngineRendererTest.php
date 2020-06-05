<?php
/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Tests;

use Joomla\Renderer\PhpEngineRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * Test class for \Joomla\Renderer\PhpEngineRenderer.
 */
class PhpEngineRendererTest extends TestCase
{
	/**
	 * @testdox  The rendering engine is returned
	 *
	 * @covers   Joomla\Renderer\PhpEngineRenderer
	 */
	public function testTheRenderingEngineIsReturned()
	{
		$engine = $this->makeEngine();

		$this->assertSame($engine, (new PhpEngineRenderer($engine))->getRenderer());
	}

	/**
	 * @testdox  Check that a path exists
	 *
	 * @covers   Joomla\Renderer\PhpEngineRenderer
	 */
	public function testCheckThatAPathExists()
	{
		$this->assertTrue((new PhpEngineRenderer($this->makeEngine()))->pathExists('index.php'));
	}

	/**
	 * @testdox  The template is rendered
	 *
	 * @covers   Joomla\Renderer\PhpEngineRenderer
	 */
	public function testTheTemplateIsRendered()
	{
		$path = __DIR__ . '/stubs/templating';

		$engine   = $this->makeEngine();
		$renderer = new PhpEngineRenderer($engine);

		$this->assertStringEqualsFile($path . '/index.php', $renderer->render('index.php'));
	}

	/**
	 * Make the PhpEngine instance for testing
	 *
	 * @return  PhpEngine
	 */
	private function makeEngine()
	{
		return new PhpEngine(new TemplateNameParser, new FilesystemLoader([__DIR__ . '/stubs/templating/%name%']));
	}
}
