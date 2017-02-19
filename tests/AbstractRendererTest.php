<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Renderer\Tests;

use Joomla\Renderer\AbstractRenderer;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Renderer\AbstractRenderer.
 */
class AbstractRendererTest extends TestCase
{
	/**
	 * @testdox  A data key is set to the renderer
	 *
	 * @covers   \Joomla\Renderer\AbstractRenderer::set
	 */
	public function testADataKeyIsSetToTheRenderer()
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|AbstractRenderer $renderer */
		$renderer = $this->getMockBuilder(AbstractRenderer::class)
			->getMockForAbstractClass();

		$renderer->set('foo', 'bar');

		$this->assertAttributeSame(['foo' => 'bar'], 'data', $renderer);
	}

	/**
	 * @testdox  A data array is set to the renderer
	 *
	 * @covers   \Joomla\Renderer\AbstractRenderer::setData
	 */
	public function testADataArrayIsSetToTheRenderer()
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|AbstractRenderer $renderer */
		$renderer = $this->getMockBuilder(AbstractRenderer::class)
			->getMockForAbstractClass();

		$renderer->setData(['foo' => 'bar']);

		$this->assertAttributeSame(['foo' => 'bar'], 'data', $renderer);
	}

	/**
	 * @testdox  The renderer's data array is reset
	 *
	 * @covers   \Joomla\Renderer\AbstractRenderer::unsetData()
	 */
	public function testTheRenderersDataArrayIsReset()
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|AbstractRenderer $renderer */
		$renderer = $this->getMockBuilder(AbstractRenderer::class)
			->getMockForAbstractClass();

		$renderer->setData(['foo' => 'bar']);
		$renderer->unsetData();

		$this->assertAttributeEmpty('data', $renderer);
	}
}
