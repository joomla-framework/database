<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Tests;

use Joomla\Renderer\AbstractRenderer;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Renderer\AbstractRenderer.
 */
class AbstractRendererTest extends TestCase
{
	/**
	 * @testdox  A data key is set to the renderer
	 *
	 * @covers   Joomla\Renderer\AbstractRenderer
	 */
	public function testADataKeyIsSetToTheRenderer()
	{
		/** @var MockObject|AbstractRenderer $renderer */
		$renderer = $this->getMockForAbstractClass(AbstractRenderer::class);

		$renderer->set('foo', 'bar');

		$this->assertSame(['foo' => 'bar'], TestHelper::getValue($renderer, 'data'));
	}

	/**
	 * @testdox  A data array is set to the renderer
	 *
	 * @covers   Joomla\Renderer\AbstractRenderer
	 */
	public function testADataArrayIsSetToTheRenderer()
	{
		/** @var MockObject|AbstractRenderer $renderer */
		$renderer = $this->getMockForAbstractClass(AbstractRenderer::class);

		$renderer->setData(['foo' => 'bar']);

		$this->assertSame(['foo' => 'bar'], TestHelper::getValue($renderer, 'data'));
	}

	/**
	 * @testdox  The renderer's data array is reset
	 *
	 * @covers   Joomla\Renderer\AbstractRenderer
	 */
	public function testTheRenderersDataArrayIsReset()
	{
		/** @var MockObject|AbstractRenderer $renderer */
		$renderer = $this->getMockForAbstractClass(AbstractRenderer::class);

		$renderer->setData(['foo' => 'bar']);
		$renderer->unsetData();

		$this->assertEmpty(TestHelper::getValue($renderer, 'data'));
	}
}
