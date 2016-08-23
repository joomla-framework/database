<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests\Issues;

use Joomla\Github\Package\Issues\Comments;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for the GitHub API package.
 *
 * @since  1.0
 */
class CommentsTest extends GitHubTestCase
{
	/**
	 * @var    Comments  Object under test.
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

		$this->object = new Comments($this->options, $this->client);
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
			->with('/repos/joomla/joomla-platform/issues/1/comments', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-platform', '1'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getRepositoryList method
	 *
	 * @return  void
	 */
	public function testGetRepositoryList()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/issues/comments?sort=created&direction=asc', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getRepositoryList('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getRepositoryListInvalidSort method
	 *
	 * @expectedException \UnexpectedValueException
	 * @return  void
	 */
	public function testGetRepositoryListInvalidSort()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->object->getRepositoryList('joomla', 'joomla-platform', 'invalid');
	}

	/**
	 * Tests the getRepositoryListInvalidDirection method
	 *
	 * @expectedException \UnexpectedValueException
	 * @return  void
	 */
	public function testGetRepositoryListInvalidDirection()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->object->getRepositoryList('joomla', 'joomla-platform', 'created', 'invalid');
	}

	/**
	 * Tests the getRepositoryListSince method
	 *
	 * @return  void
	 */
	public function testGetRepositoryListSince()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$date = new \DateTime('1966-09-15 12:34:56', new \DateTimeZone('UTC'));

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/issues/comments?sort=created&direction=asc&since=1966-09-15T12:34:56+00:00', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getRepositoryList('joomla', 'joomla-platform', 'created', 'asc', $date),
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
			->with('/repos/joomla/joomla-platform/issues/comments/1', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-platform', 1),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the edit method
	 *
	 * @return  void
	 */
	public function testEdit()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('patch')
			->with('/repos/joomla/joomla-platform/issues/comments/1', '{"body":"Hello"}', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->edit('joomla', 'joomla-platform', 1, 'Hello'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the create method
	 *
	 * @return  void
	 */
	public function testCreate()
	{
		$this->response->code = 201;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/issues/1/comments', '{"body":"Hello"}', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->create('joomla', 'joomla-platform', 1, 'Hello'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the delete method
	 *
	 * @return  void
	 */
	public function testDelete()
	{
		$this->response->code = 204;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('delete')
			->with('/repos/joomla/joomla-platform/issues/comments/1', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->delete('joomla', 'joomla-platform', 1),
			$this->equalTo(true)
		);
	}
}
