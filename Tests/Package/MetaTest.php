<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Meta;
use Joomla\Github\Tests\Stub\GitHubTestCase;

/**
 * Test class for Joomla\Github\Meta.
 *
 * @since  1.0
 */
class MetaTest extends GitHubTestCase
{
	/**
	 * @var    Meta  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * @var    string  Sample JSON string.
	 * @since  1.0
	 */
	protected $sampleString = '{"hooks":["127.0.0.1/32","192.168.1.1/32","10.10.1.1/27"],"git":["127.0.0.1/32"]}';

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

		$this->object = new Meta($this->options, $this->client);
	}

	/**
	 * Tests the getMeta method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetMeta()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$decodedResponse = new \stdClass;
		$decodedResponse->hooks = array('127.0.0.1/32', '192.168.1.1/32', '10.10.1.1/27');
		$decodedResponse->git   = array('127.0.0.1/32');

		$this->client->expects($this->once())
			->method('get')
			->with('/meta')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getMeta(),
			$this->equalTo($decodedResponse)
		);
	}

	/**
	 * Tests the getMeta method - failure
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @expectedException  \DomainException
	 */
	public function testGetMetaFailure()
	{
		$this->response->code = 500;
		$this->response->body = $this->errorString;

		$this->client->expects($this->once())
			->method('get')
			->with('/meta')
			->will($this->returnValue($this->response));

		$this->object->getMeta();
	}
}
