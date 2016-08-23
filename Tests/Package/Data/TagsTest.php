<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Data\Tags;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for the GitHub API package.
 *
 * @since  1.0
 */
class TagsTest extends GitHubTestCase
{
	/**
	 * @var    Tags  Object under test.
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

		$this->object = new Tags($this->options, $this->client);
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
			->with('/repos/joomla/joomla-platform/git/tags/12345', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-platform', '12345'),
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

		$data = '{"tag":"0.1","message":"Message","object":"12345","type":"commit","tagger":{"name":"elkuku","email":"email@example.com","date":"123456789"}}';
		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/git/tags', $data, array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->create('joomla', 'joomla-platform', '0.1', 'Message', '12345', 'commit', 'elkuku', 'email@example.com', '123456789'),
			$this->equalTo(json_decode($this->response->body))
		);
	}
}
