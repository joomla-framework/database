<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Repositories\Comments;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Comments.
 *
 * @since  1.0
 */
class CommentsTest extends GitHubTestCase
{
	/**
	 * @var Comments
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
	 * Tests the GetListRepository method.
	 *
	 * @return  void
	 */
	public function testGetListRepository()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-framework/comments')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getListRepository('joomla', 'joomla-framework'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the GetList method.
	 *
	 * @return  void
	 */
	public function testGetList()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-framework/commits/123/comments')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-framework', '123'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Get method.
	 *
	 * @return  void
	 */
	public function testGet()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-framework/comments/123')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-framework', 123),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Edit method.
	 *
	 * @return  void
	 */
	public function testEdit()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('patch')
			->with('/repos/joomla/joomla-framework/comments/123')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->edit('joomla', 'joomla-framework', 123, 'My Comment'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Delete method.
	 *
	 * @return  void
	 */
	public function testDelete()
	{
		$this->response->code = 204;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('delete')
			->with('/repos/joomla/joomla-framework/comments/123')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->delete('joomla', 'joomla-framework', 123),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Create method.
	 *
	 * @return  void
	 */
	public function testCreate()
	{
		$this->response->code = 201;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-framework/commits/123abc/comments')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->create('joomla', 'joomla-framework', '123abc', 'My Comment', 456, 'path/file.php', 789),
			$this->equalTo(json_decode($this->sampleString))
		);
	}
}
