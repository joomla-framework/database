<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Activity\Watching;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for the GitHub API package.
 *
 * @since  1.0
 */
class WatchingTest extends GitHubTestCase
{
	/**
	 * @var    Watching  Object under test.
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

		$this->object = new Watching($this->options, $this->client);
	}

	/**
	 * Tests the getList method
	 *
	 * @return  void
	 */
	public function testGetList()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/subscribers', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getRepositories method
	 *
	 * @return  void
	 */
	public function testGetRepositories()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/user/subscriptions', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getRepositories(),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getRepositoriesUser method
	 *
	 * @return  void
	 */
	public function testGetRepositoriesUser()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/users/joomla/subscriptions', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getRepositories('joomla'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getSubscription method
	 *
	 * @return  void
	 */
	public function testGetSubscription()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/subscription', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getSubscription('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the setSubscription method
	 *
	 * @return  void
	 */
	public function testSetSubscription()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('put')
			->with('/repos/joomla/joomla-platform/subscription', '{"subscribed":true,"ignored":false}', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->setSubscription('joomla', 'joomla-platform', true, false),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the deleteSubscription method
	 *
	 * @return  void
	 */
	public function testDeleteSubscription()
	{
		$this->response->code = 204;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('delete')
			->with('/repos/joomla/joomla-platform/subscription', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->deleteSubscription('joomla', 'joomla-platform'),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the check method
	 *
	 * @return  void
	 */
	public function testCheck()
	{
		$this->response->code = 204;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('get')
			->with('/user/subscriptions/joomla/joomla-platform', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->check('joomla', 'joomla-platform'),
			$this->equalTo(true)
		);
	}

	/**
	 * Tests the checkFalse method
	 *
	 * @return  void
	 */
	public function testCheckFalse()
	{
		$this->response->code = 404;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('get')
			->with('/user/subscriptions/joomla/joomla-platform', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->check('joomla', 'joomla-platform'),
			$this->equalTo(false)
		);
	}

	/**
	 * Tests the checkUnexpected method
	 *
	 * @expectedException \UnexpectedValueException
	 * @return  void
	 */
	public function testCheckUnexpected()
	{
		$this->response->code = 666;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('get')
			->with('/user/subscriptions/joomla/joomla-platform', array(), 0)
			->will($this->returnValue($this->response));

		$this->object->check('joomla', 'joomla-platform');
	}

	/**
	 * Tests the watch method
	 *
	 * @return  void
	 */
	public function testWatch()
	{
		$this->response->code = 204;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('put')
			->with('/user/subscriptions/joomla/joomla-platform', '', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->watch('joomla', 'joomla-platform'),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the unwatch method
	 *
	 * @return  void
	 */
	public function testUnwatch()
	{
		$this->response->code = 204;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('delete')
			->with('/user/subscriptions/joomla/joomla-platform', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->unwatch('joomla', 'joomla-platform'),
			$this->equalTo($this->response->body)
		);
	}
}
