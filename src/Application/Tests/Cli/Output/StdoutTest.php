<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application\Tests\Cli\Output;

use Joomla\Application\Cli\Output\Stdout;
use Joomla\Application\Tests\Cli\Output\Processor\TestProcessor;
use Joomla\Application\Tests\CompatTestCase;

/**
 * Test class for Joomla\Application\Cli\Output\Stdout.
 */
class StdoutTest extends CompatTestCase
{
	/**
	 * Object under test
	 *
	 * @var  Stdout
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function doSetUp()
	{
		$this->object = new Stdout;

		parent::doSetUp();
	}

	/**
	 * Tests the setProcessor and getProcessor methods for an injected processor
	 */
	public function testSetAndGetProcessor()
	{
		$processor = new TestProcessor;

		$this->object->setProcessor($processor);

		$this->assertSame(
			$processor,
			$this->object->getProcessor()
		);
	}

	/**
	 * Tests injecting a processor when instantiating the output object
	 */
	public function test__constructProcessorInjection()
	{
		$processor = new TestProcessor;
		$object    = new Stdout($processor);

		$this->assertSame(
			$processor,
			$object->getProcessor()
		);
	}
}
