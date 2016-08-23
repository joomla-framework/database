<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Repositories\Collaborators;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Collaborators.
 *
 * @since  1.0
 */
class CollaboratorsTest extends GitHubTestCase
{
	/**
	 * @var Collaborators
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

		$this->object = new Collaborators($this->options, $this->client);
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
			->with('/repos/joomla/joomla-framework/collaborators')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-framework'),
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
		$this->response->code = 204;
		$this->response->body = true;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-framework/collaborators/elkuku')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-framework', 'elkuku'),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the GetNegative method.
	 *
	 * @return  void
	 */
	public function testGetNegative()
	{
		$this->response->code = 404;
		$this->response->body = false;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-framework/collaborators/elkuku')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-framework', 'elkuku'),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the GetUnexpected method.
	 *
	 * @return  void
	 *
	 * @expectedException \UnexpectedValueException
	 */
	public function testGetUnexpected()
	{
		$this->response->code = 666;
		$this->response->body = null;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-framework/collaborators/elkuku')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-framework', 'elkuku'),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the Add method.
	 *
	 * @return  void
	 */
	public function testAdd()
	{
		$this->response->code = 204;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('put')
			->with('/repos/joomla/joomla-framework/collaborators/elkuku')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->add('joomla', 'joomla-framework', 'elkuku'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Remove method.
	 *
	 * @return  void
	 */
	public function testRemove()
	{
		$this->response->code = 204;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('delete')
			->with('/repos/joomla/joomla-framework/collaborators/elkuku')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->remove('joomla', 'joomla-framework', 'elkuku'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}
}
