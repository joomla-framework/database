<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests\Issues;

use Joomla\Github\Package\Issues\Assignees;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for the GitHub API package.
 *
 * @since  1.0
 */
class AssigneesTest extends GitHubTestCase
{
	/**
	 * @var    Assignees  Object under test.
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

		$this->object = new Assignees($this->options, $this->client);
	}

	/**
	 * Tests the getList method
	 *
	 * @return void
	 */
	public function testGetList()
	{
		$this->response->code = 200;
		$this->response->body = '[
	{
	"login": "octocat",
	"id": 1,
	"avatar_url": "https://github.com/images/error/octocat_happy.gif",
	"gravatar_id": "somehexcode",
	"url": "https://api.github.com/users/octocat"
	}
	]';

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/' . $this->owner . '/' . $this->repo . '/assignees', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList($this->owner, $this->repo),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getList method
	 * Response:
	 * If the given assignee login belongs to an assignee for the repository,
	 * a 204 header with no content is returned.
	 * Otherwise a 404 status code is returned.
	 *
	 * @return void
	 */
	public function testCheck()
	{
		$this->response->code = 204;
		$this->response->body = '';

		$assignee = 'elkuku';

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/' . $this->owner . '/' . $this->repo . '/assignees/' . $assignee, array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->check($this->owner, $this->repo, $assignee),
			$this->equalTo(true)
		);
	}

	/**
	 * Tests the getList method with a negative response
	 * Response:
	 * If the given assignee login belongs to an assignee for the repository,
	 * a 204 header with no content is returned.
	 * Otherwise a 404 status code is returned.
	 *
	 * @return void
	 */
	public function testCheckNo()
	{
		$this->response->code = 404;
		$this->response->body = '';

		$assignee = 'elkuku';

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/' . $this->owner . '/' . $this->repo . '/assignees/' . $assignee, array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->check($this->owner, $this->repo, $assignee),
			$this->equalTo(false)
		);
	}

	/**
	 * Tests the getList method with a negative response
	 * Response:
	 * If the given assignee login belongs to an assignee for the repository,
	 * a 204 header with no content is returned.
	 * Otherwise a 404 status code is returned.
	 *
	 * @expectedException \DomainException
	 *
	 * @return void
	 */
	public function testCheckException()
	{
		$this->response->code = 666;
		$this->response->body = '';

		$assignee = 'elkuku';

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/' . $this->owner . '/' . $this->repo . '/assignees/' . $assignee, array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->check($this->owner, $this->repo, $assignee),
			$this->equalTo(false)
		);
	}

	/**
	 * Tests the add method
	 *
	 * @return void
	 */
	public function testAdd()
	{
		$this->response->code = 201;
		$this->response->body = '[
	{
	"login": "octocat",
	"id": 1,
	"avatar_url": "https://github.com/images/error/octocat_happy.gif",
	"gravatar_id": "somehexcode",
	"url": "https://api.github.com/users/octocat"
	}
	]';

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/' . $this->owner . '/' . $this->repo . '/issues/123/assignees', json_encode(array('assignees' => array('joomla'))))
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->add($this->owner, $this->repo, 123, array('joomla')),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the remove method
	 *
	 * @return void
	 */
	public function testRemove()
	{
		$this->response->code = 200;
		$this->response->body = '[
	{
	"login": "octocat",
	"id": 1,
	"avatar_url": "https://github.com/images/error/octocat_happy.gif",
	"gravatar_id": "somehexcode",
	"url": "https://api.github.com/users/octocat"
	}
	]';

		$this->client->expects($this->once())
			->method('delete')
			->with('/repos/' . $this->owner . '/' . $this->repo . '/issues/123/assignees', array(), null, json_encode(array('assignees' => array('joomla'))))
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->remove($this->owner, $this->repo, 123, array('joomla')),
			$this->equalTo(json_decode($this->response->body))
		);
	}
}
