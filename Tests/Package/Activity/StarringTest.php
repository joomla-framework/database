<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Activity\Starring;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for the GitHub API package.
 *
 * @since  1.0
 */
class StarringTest extends GitHubTestCase
{
	/**
	 * @var    Starring  Object under test.
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

		$this->object = new Starring($this->options, $this->client);
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
			->with('/repos/joomla/joomla-platform/stargazers', array(), 0)
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
			->with('/user/starred?sort=created&direction=desc', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getRepositories(),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getRepositories method - invalid sort option
	 *
	 * @return  void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetRepositoriesInvalidSort()
	{
		$this->object->getRepositories('', 'invalid');
	}

	/**
	 * Tests the getRepositories method - invalid direction option
	 *
	 * @return  void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetRepositoriesInvalidDirection()
	{
		$this->object->getRepositories('', 'created', 'invalid');
	}

	/**
	 * Tests the check method
	 *
	 * @return  void
	 */
	public function testCheck()
	{
		$this->response->code = 204;
		$this->response->body = true;

		$this->client->expects($this->once())
			->method('get')
			->with('/user/starred/joomla/joomla-platform', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->check('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
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
		$this->response->body = false;

		$this->client->expects($this->once())
			->method('get')
			->with('/user/starred/joomla/joomla-platform', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->check('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
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
		$this->response->body = false;

		$this->client->expects($this->once())
			->method('get')
			->with('/user/starred/joomla/joomla-platform', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->check('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the star method
	 *
	 * @return  void
	 */
	public function testStar()
	{
		$this->response->code = 204;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('put')
			->with('/user/starred/joomla/joomla-platform', '', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->star('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the unstar method
	 *
	 * @return  void
	 */
	public function testUnstar()
	{
		$this->response->code = 204;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('delete')
			->with('/user/starred/joomla/joomla-platform', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->unstar('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
		);
	}
}
