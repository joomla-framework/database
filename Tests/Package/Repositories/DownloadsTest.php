<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Repositories\Downloads;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Downloads.
 *
 * @since  1.0
 */
class DownloadsTest extends GitHubTestCase
{
	/**
	 * @var Downloads
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

		$this->object = new Downloads($this->options, $this->client);
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
			->with('/repos/joomla/joomla-platform/downloads')
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
			->with('/repos/joomla/joomla-platform/downloads/123abc')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->get('joomla', 'joomla-platform', '123abc'),
			$this->equalTo(json_decode($this->sampleString))
		);
	}

	/**
	 * Tests the Create method.
	 *
	 * @return  void
	 *
	 * @expectedException  \RuntimeException
	 * @expectedExceptionMessage  The GitHub API no longer supports creating downloads. The Releases API should be used instead.
	 */
	public function testCreate()
	{
		$this->object->create('joomla', 'joomla-platform', 'aaa.zip', 1234, 'Description', 'content_type');
	}

	/**
	 * Tests the Upload method.
	 *
	 * @return  void
	 *
	 * @expectedException  \RuntimeException
	 * @expectedExceptionMessage  The GitHub API no longer supports creating downloads. The Releases API should be used instead.
	 */
	public function testUpload()
	{
		$this->object->upload(
			'joomla', 'joomla-platform', 123, 'a/b/aaa.zip', 'acl', 201, 'aaa.zip', '123abc', '123abc', '123abc', 'content_type', '@aaa.zip'
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
			->with('/repos/joomla/joomla-platform/downloads/123')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->delete('joomla', 'joomla-platform', 123),
			$this->equalTo(json_decode($this->sampleString))
		);
	}
}
