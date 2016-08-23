<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Emojis;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Emojis.
 *
 * @since  1.1.2
 */
class EmojisTest extends GitHubTestCase
{
	/**
	 * @var Emojis
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Emojis($this->options, $this->client);
	}

	/**
	 * Tests the getList method
	 *
	 * @return void
	 */
	public function testGetList()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/emojis')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList(),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the getList method - simulated failure
	 *
	 * @return void
	 */
	public function testGetListFailure()
	{
		$exception = false;

		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$this->client->expects($this->once())
			->method('get')
			->with('/emojis')
			->will($this->returnValue($this->response));

		try
		{
			$this->object->getList();
		}
		catch (\DomainException $e)
		{
			$exception = true;

			$this->assertThat(
				$e->getMessage(),
				$this->equalTo(json_decode($this->errorString)->message)
			);
		}

		$this->assertTrue($exception);
	}
}
