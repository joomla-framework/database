<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Repositories\Merging;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Merging.
 *
 * @since  1.0
 */
class MergingTest extends GitHubTestCase
{
	/**
	 * @var Merging
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @since   1.0
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Merging($this->options, $this->client);
	}

	/**
	 * Tests the Perform method.
	 *
	 * @return  void
	 */
	public function testPerform()
	{
		$this->response->code = 201;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/merges')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->perform('joomla', 'joomla-platform', '123', '456', 'My Message'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Perform method.
	 *
	 * @return  void
	 *
	 * @expectedException \UnexpectedValueException
	 */
	public function testPerformNoOp()
	{
		$this->response->code = 204;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/merges')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->perform('joomla', 'joomla-platform', '123', '456', 'My Message'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Perform method.
	 *
	 * @expectedException \UnexpectedValueException
	 *
	 * @return  void
	 */
	public function testPerformMissing()
	{
		$this->response->code = 404;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/merges')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->perform('joomla', 'joomla-platform', '123', '456', 'My Message'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Perform method.
	 *
	 * @expectedException \UnexpectedValueException
	 *
	 * @return  void
	 */
	public function testPerformConflict()
	{
		$this->response->code = 409;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/merges')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->perform('joomla', 'joomla-platform', '123', '456', 'My Message'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Perform method.
	 *
	 * @expectedException \UnexpectedValueException
	 *
	 * @return  void
	 */
	public function testPerformUnexpected()
	{
		$this->response->code = 666;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/merges')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->perform('joomla', 'joomla-platform', '123', '456', 'My Message'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}
}
