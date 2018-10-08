<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests;

use Joomla\Console\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Console\Application
 */
class ApplicationTest extends TestCase
{
	/**
	 * @var  Application
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$this->object = new Application;
	}

	/**
	 * @covers  Joomla\Console\Application::execute
	 */
	public function testTheApplicationIsExecuted()
	{
		$output = new BufferedOutput;

		$this->object->setAutoExit(false);
		$this->object->execute(null, $output);

		$this->assertNotEmpty($output->fetch());
	}
}
