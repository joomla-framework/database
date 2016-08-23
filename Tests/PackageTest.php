<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Github;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Joomla\Github\Github.
 *
 * @since  1.0
 */
class PackageTest extends GitHubTestCase
{
	/**
	 * @var    Github  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Github($this->options, $this->client);
	}

	/**
	 * Tests the magic __get method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function test__Get()
	{
		$this->assertThat(
			$this->object->repositories->forks,
			$this->isInstanceOf('Joomla\Github\Package\Repositories\Forks')
		);
	}

	/**
	 * Tests the magic __get method with an invalid parameter.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function test__GetInvalid()
	{
		$this->object->repositories->INVALID;
	}
}
