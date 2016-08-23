<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Repositories\Keys;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Keys.
 *
 * @since  1.0
 */
class KeysTest extends GitHubTestCase
{
	/**
	 * @var Keys
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

		$this->object = new Keys($this->options, $this->client);
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
			->with('/repos/joomla/joomla-platform/keys')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-platform'),
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
			->with('/repos/joomla/joomla-platform/keys/1')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-platform', 1),
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
			->with('/repos/joomla/joomla-platform/keys')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->create('joomla', 'joomla-platform', 'email@example.com', '123abc'),
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
			->with('/repos/joomla/joomla-platform/keys/1')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->edit('joomla', 'joomla-platform', 1, 'email@example.com', '123abc'),
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
		$this->response->body = true;

		$this->client->expects($this->once())
			->method('delete')
			->with('/repos/joomla/joomla-platform/keys/1')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->delete('joomla', 'joomla-platform', 1),
			$this->equalTo($this->response->body)
		);
	}
}
