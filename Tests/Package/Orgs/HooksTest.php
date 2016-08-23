<?php
/**
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests\Orgs;

use Joomla\Github\Package\Orgs\Hooks;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Members.
 *
 * @since  1.0
 */
class HooksTest extends GitHubTestCase
{
	/**
	 * @var Hooks
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

		$this->object = new Hooks($this->options, $this->client);
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
			->with('/orgs/joomla/hooks')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla'),
			$this->equalTo(json_decode($this->sampleString))
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
			->with('/orgs/joomla/hooks/123')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 123),
			$this->equalTo(json_decode($this->sampleString))
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
			->with('/orgs/joomla/hooks')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->create('joomla', '{url}', 'json', '{secret}'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the create method
	 *
	 * @return  void
	 *
	 * @expectedException \UnexpectedValueException
	 */
	public function testCreateFailure()
	{
		$this->object->create('joomla', '{url}', '{invalid}');
	}

	/**
	 * Tests the edit method
	 *
	 * @return  void
	 */
	public function testEdit()
	{
		$this->response->code = 201;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('post')
			->with('/orgs/{org}/hooks')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->edit('{org}', '{url}', 'json', '{secret}', 1, array('create'), true),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the edit method
	 *
	 * @return  void
	 *
	 * @expectedException \UnexpectedValueException
	 */
	public function testEditFailure()
	{
		$this->object->edit('joomla', '{url}', '{invalid}');
	}

	/**
	 * Tests the edit method
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testEditFailure2()
	{
		$this->object->edit('{org}', '{url}', 'json', '{secret}', 1, array('{invalid}'));
	}

	/**
	 * Tests the ping method
	 *
	 * @return  void
	 */
	public function testPing()
	{
		$this->response->code = 204;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('post')
			->with('/orgs/joomla/hooks/123/pings')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->ping('joomla', 123),
			$this->equalTo(json_decode($this->sampleString))
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
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('delete')
			->with('/orgs/joomla/hooks/123')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->delete('joomla', 123),
			$this->equalTo(json_decode($this->sampleString))
		);
	}
}
