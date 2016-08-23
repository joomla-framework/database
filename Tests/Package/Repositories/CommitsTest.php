<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Repositories\Commits;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Commits.
 *
 * @since  1.0
 */
class CommitsTest extends GitHubTestCase
{
	/**
	 * @var    Commits  Object under test.
	 * @since  12.1
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new Commits($this->options, $this->client);
	}

	/**
	 * Tests the getCommit method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetCommit()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/commits/abc1234')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-platform', 'abc1234'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the getCommit method - failure
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @expectedException  \DomainException
	 */
	public function testGetCommitFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/commits/abc1234')
			->will($this->returnValue($this->response));

		$this->object->get('joomla', 'joomla-platform', 'abc1234');
	}

	/**
	 * Tests the getList method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetList()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/commits')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the getList method - failure
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @expectedException  \DomainException
	 */
	public function testGetListFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/commits')
			->will($this->returnValue($this->response));

		$this->object->getList('joomla', 'joomla-platform');
	}

	/**
	 * Tests the Compare method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testCompare()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/compare/123abc...456def')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->compare('joomla', 'joomla-platform', '123abc', '456def'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}
}
