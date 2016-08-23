<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Search;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Activity.
 *
 * @since  1.0
 */
class SearchTest extends GitHubTestCase
{
	/**
	 * @var Search
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

		$this->object = new Search($this->options, $this->client);
	}

	/**
	 * Tests the issues method
	 *
	 * @return  void
	 */
	public function testIssues()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/legacy/issues/search/joomla/joomla-platform/open/github')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->issues('joomla', 'joomla-platform', 'open', 'github'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the issuesInvalidState method
	 *
	 * @return  void
	 *
	 * @expectedException \UnexpectedValueException
	 */
	public function testIssuesInvalidState()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->object->issues('joomla', 'joomla-platform', 'invalid', 'github');
	}

	/**
	 * Tests the repositories method
	 *
	 * @return  void
	 */
	public function testRepositories()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/legacy/repos/search/joomla')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->repositories('joomla'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the users method
	 *
	 * @return  void
	 */
	public function testUsers()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/legacy/user/search/joomla')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->users('joomla'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the email method
	 *
	 * @return  void
	 */
	public function testEmail()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/legacy/user/email/email@joomla')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->email('email@joomla'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}
}
