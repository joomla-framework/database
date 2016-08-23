<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Repositories\Forks;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Forks.
 *
 * @since  1.0
 */
class ForksTest extends GitHubTestCase
{
	/**
	 * @var    Forks  Object under test.
	 * @since  11.4
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

		$this->object = new Forks($this->options, $this->client);
	}

	/**
	 * Tests the create method
	 *
	 * @return void
	 */
	public function testCreate()
	{
		$this->response->code = 202;
		$this->response->body = $this->sampleString;

		// Build the request data.
		$data = json_encode(
			array(
				'org' => 'jenkins-jools'
			)
		);

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/forks', $data)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->create('joomla', 'joomla-platform', 'jenkins-jools'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the create method - failure
	 *
	 * @expectedException  \DomainException
	 *
	 * @return void
	 */
	public function testCreateFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		// Build the request data.
		$data = json_encode(
			array()
		);

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/forks', $data)
			->will($this->returnValue($this->response));

		$this->object->create('joomla', 'joomla-platform', '');
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
			->with('/repos/joomla/joomla-platform/forks')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getList method - failure
	 *
	 * @expectedException  \DomainException
	 *
	 * @return void
	 */
	public function testGetListFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/forks')
			->will($this->returnValue($this->response));

		$this->object->getList('joomla', 'joomla-platform');
	}
}
