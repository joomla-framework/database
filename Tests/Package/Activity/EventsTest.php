<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Activity\Events;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for the GitHub API package.
 *
 * @since  1.0
 */
class EventsTest extends GitHubTestCase
{
	/**
	 * @var    Events  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * @var string
	 * @since  1.0
	 */
	protected $owner = 'joomla';

	/**
	 * @var string
	 * @since  1.0
	 */
	protected $repo = 'joomla-framework';

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

		$this->object = new Events($this->options, $this->client);
	}

	/**
	 * Tests the getPublic method
	 *
	 * @return  void
	 */
	public function testGetPublic()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/events')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getPublic(),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getRepository method
	 *
	 * @return  void
	 */
	public function testGetRepository()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$path = '/repos/' . $this->owner . '/' . $this->repo . '/events';

		$this->client->expects($this->once())
			->method('get')
			->with($path)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getRepository($this->owner, $this->repo),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getIssue method
	 *
	 * @return  void
	 */
	public function testGetIssue()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$path = '/repos/' . $this->owner . '/' . $this->repo . '/issues/events';

		$this->client->expects($this->once())
			->method('get')
			->with($path)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getIssue($this->owner, $this->repo),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getNetwork method
	 *
	 * @return  void
	 */
	public function testGetNetwork()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$path = '/networks/' . $this->owner . '/' . $this->repo . '/events';

		$this->client->expects($this->once())
			->method('get')
			->with($path)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getNetwork($this->owner, $this->repo),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getOrg method
	 *
	 * @return  void
	 */
	public function testGetOrg()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$path = '/orgs/' . $this->owner . '/events';

		$this->client->expects($this->once())
			->method('get')
			->with($path)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getOrg($this->owner),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getUser method
	 *
	 * @return  void
	 */
	public function testGetUser()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$path = '/users/' . $this->owner . '/received_events';

		$this->client->expects($this->once())
			->method('get')
			->with($path)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getUser($this->owner),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getUserPublic method
	 *
	 * @return  void
	 */
	public function testGetUserPublic()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$path = '/users/' . $this->owner . '/received_events/public';

		$this->client->expects($this->once())
			->method('get')
			->with($path)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getUserPublic($this->owner),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getByUser method
	 *
	 * @return  void
	 */
	public function testGetByUser()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$path = '/users/' . $this->owner . '/events';

		$this->client->expects($this->once())
			->method('get')
			->with($path)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getByUser($this->owner),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getByUserPublic method
	 *
	 * @return  void
	 */
	public function testGetByUserPublic()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$path = '/users/' . $this->owner . '/events/public';

		$this->client->expects($this->once())
			->method('get')
			->with($path)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getByUserPublic($this->owner),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getUserOrg method
	 *
	 * @return  void
	 */
	public function testGetUserOrg()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$path = '/users/' . $this->owner . '/events/orgs/' . $this->repo;

		$this->client->expects($this->once())
			->method('get')
			->with($path)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getUserOrg($this->owner, $this->repo),
			$this->equalTo(json_decode($this->response->body))
		);
	}
}
