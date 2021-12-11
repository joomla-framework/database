<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\View\Tests;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/stubs/tbase.php';

/**
 * Tests for the Joomla\View\AbstractView class.
 *
 * @since  1.0
 */
class AbstractViewTest extends TestCase
{
	/**
	 * @var    \Joomla\View\AbstractView
	 * @since  1.0
	 */
	private $instance;

	/**
	 * Tests the __construct method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\View\AbstractView::__construct
	 * @since   1.0
	 */
	public function test__construct()
	{
		$this->assertAttributeInstanceOf('Joomla\\Model\\ModelInterface', 'model', $this->instance);
	}

	/**
	 * Tests the escape method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\View\AbstractView::escape
	 * @since   1.0
	 */
	public function testEscape()
	{
		$this->assertEquals('foo', $this->instance->escape('foo'));
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$model = $this->getMockBuilder('Joomla\\Model\\ModelInterface')->getMock();

		$this->instance = new BaseView($model);
	}
}
