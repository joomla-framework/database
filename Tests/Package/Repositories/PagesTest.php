<?php
/**
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Repositories\Pages;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Pages.
 *
 * @since  1.0
 */
class PagesTest extends GitHubTestCase
{
	/**
	 * @var Pages
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

		$this->object = new Pages($this->options, $this->client);
	}

	/**
	 * Tests the GetInfo method.
	 *
	 * @return  void
	 */
	public function testGetInfo()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/{owner}/{repo}/pages')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getInfo('{owner}', '{repo}'),
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
			->with('/repos/{owner}/{repo}/pages/builds')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('{owner}', '{repo}'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the GetLatest method.
	 *
	 * @return  void
	 */
	public function testGetLatest()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/{owner}/{repo}/pages/builds/latest')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getLatest('{owner}', '{repo}'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}
}
