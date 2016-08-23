<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests\Issues;

use Joomla\Github\Package\Issues\Events;
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
			->with('/repos/joomla/joomla-platform/issues/1/events', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-platform', '1'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getListRepository method
	 *
	 * @return  void
	 */
	public function testGetListRepository()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/issues/1/comments', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getListRepository('joomla', 'joomla-platform', '1'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the get method
	 *
	 * @return  void
	 */
	public function testGet()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/issues/events/1', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-platform', '1'),
			$this->equalTo(json_decode($this->response->body))
		);
	}
}
