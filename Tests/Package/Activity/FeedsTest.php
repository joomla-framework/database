<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Activity\Feeds;
use Joomla\Registry\Registry;

/**
 * Test class for the GitHub API package.
 *
 * @since  __DEPLOY_VERSION__
 */
class EventsTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Registry  Options for the GitHub object.
	 * @since  __DEPLOY_VERSION__
	 */
	protected $options;

	/**
	 * @var    \PHPUnit_Framework_MockObject_MockObject  Mock client object.
	 * @since  __DEPLOY_VERSION__
	 */
	protected $client;

	/**
	 * @var    Feeds  Object under test.
	 * @since  __DEPLOY_VERSION__
	 */
	protected $object;

	/**
	 * @var    \Joomla\Http\Response  Mock response object.
	 * @since  __DEPLOY_VERSION__
	 */
	protected $response;

	/**
	 * @var    string  Sample JSON string.
	 * @since  __DEPLOY_VERSION__
	 */
	protected $sampleString = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

	/**
	 * @var    string  Sample JSON error message.
	 * @since  __DEPLOY_VERSION__
	 */
	protected $errorString = '{"message": "Generic Error"}';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->options  = new Registry;
		$this->client   = $this->getMock('\\Joomla\\Github\\Http', array('get', 'post', 'delete', 'patch', 'put'));
		$this->response = $this->getMock('\\Joomla\\Http\\Response');

		$this->object = new Feeds($this->options, $this->client);
	}

	/**
	 * Tests the getFeeds method
	 *
	 * @return  void
	 */
	public function testGetFeeds()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/feeds')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getFeeds(),
			$this->equalTo(json_decode($this->response->body))
		);
	}
}
