<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Repositories\Releases;
use Joomla\Registry\Registry;

/**
 * Test class for the GitHub API package.
 *
 * @since  1.0
 */
class ReleasesTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Registry  Options for the GitHub object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    \PHPUnit_Framework_MockObject_MockObject  Mock client object.
	 * @since  1.0
	 */
	protected $client;

	/**
	 * @var    Releases  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * @var    \Joomla\Http\Response  Mock response object.
	 * @since  1.0
	 */
	protected $response;

	/**
	 * @var    string  Sample JSON string.
	 * @since  12.3
	 */
	protected $sampleString = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

	/**
	 * @var    string  Sample JSON error message.
	 * @since  12.3
	 */
	protected $errorString = '{"message": "Generic Error"}';

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

		$this->options  = new Registry;

		$this->client = $this->getMockBuilder('\\Joomla\\Github\\Http')
			->setMethods(array('get', 'post', 'delete', 'patch', 'put'))
			->getMock();

		$this->response = $this->getMockBuilder('\\Joomla\\Http\\Response')->getMock();

		$this->object = new Releases($this->options, $this->client);
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
			->with('/repos/joomla/joomla-platform/releases/12345', array(), 0)
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

		$data = '{"tag_name":"0.1","target_commitish":"targetCommitish","name":"master","body":"New release","draft":false,"prerelease":false}';
		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/releases', $data, array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->create('joomla', 'joomla-platform', '0.1', 'targetCommitish', 'master', 'New release', false, false),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the create method with failure.
	 *
	 * @return  void
	 */
	public function testCreateFailure()
	{
		$this->response->code = 201;
		$this->response->body = $this->sampleString;

		$data = '{"tag_name":"0.1","target_commitish":"targetCommitish","name":"master","body":"New release","draft":false,"prerelease":false}';
		$this->client->expects($this->once())
			->method('post')
			->with('/repos/joomla/joomla-platform/releases', $data, array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->create('joomla', 'joomla-platform', '0.1', 'targetCommitish', 'master', 'New release', false, false),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the edit method.
	 *
	 * @return  void
	 */
	public function testEdit()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$releaseId = 123;

		$data = '{"tag_name":"tagName","target_commitish":"targetCommitish","name":"name","body":"body","draft":"draft","prerelease":"preRelease"}';
		$this->client->expects($this->once())
			->method('patch')
			->with('/repos/joomla/joomla-platform/releases/' . $releaseId, $data, array(), 0)

			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->edit('joomla', 'joomla-platform', $releaseId, 'tagName', 'targetCommitish', 'name', 'body', 'draft', 'preRelease'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getList method.
	 *
	 * @return  void
	 */
	public function testGetList()
	{
		$this->response->code = 200;
		$this->response->body = '[{"tag_name":"1"},{"tag_name":"2"}]';

		$releases = array();

		foreach (json_decode($this->response->body) as $release)
		{
			$releases[$release->tag_name] = $release;
		}

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/releases', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList('joomla', 'joomla-platform'),
			$this->equalTo($releases)
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
			->with('/repos/joomla/joomla-platform/releases/123')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->delete('joomla', 'joomla-platform', '123'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getLatest method.
	 *
	 * @return  void
	 */
	public function testGetLatest()
	{
		$this->response->code = 200;
		$this->response->body = '[]';

		$releases = array();

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/releases/latest', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getLatest('joomla', 'joomla-platform'),
			$this->equalTo($releases)
		);
	}

	/**
	 * Tests the getByTag method
	 *
	 * @return  void
	 */
	public function testGetByTag()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/releases/tags/{tag}', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getByTag('joomla', 'joomla-platform', '{tag}'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getListAssets method
	 *
	 * @return  void
	 */
	public function testGetListAssets()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/releases/123/assets', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getListAssets('joomla', 'joomla-platform', 123),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the getAsset method
	 *
	 * @return  void
	 */
	public function testGetAsset()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/releases/assets/123', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getAsset('joomla', 'joomla-platform', 123),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the editAsset method
	 *
	 * @return  void
	 */
	public function testEditAsset()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$data = '{"name":"{name}","label":"{label}"}';

		$this->client->expects($this->once())
			->method('patch')
			->with('/repos/joomla/joomla-platform/releases/assets/123', $data, array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->editAsset('joomla', 'joomla-platform', 123, '{name}', '{label}'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the deleteAsset method
	 *
	 * @return  void
	 */
	public function testDeleteAsset()
	{
		$this->response->code = 204;
		$this->response->body = true;

		$this->client->expects($this->once())
			->method('delete')
			->with('/repos/joomla/joomla-platform/releases/assets/123', array(), 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->deleteAsset('joomla', 'joomla-platform', 123),
			$this->equalTo($this->response->body)
		);
	}
}
