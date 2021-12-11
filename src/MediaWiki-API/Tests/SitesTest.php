<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Mediawiki\Tests;

use Joomla\Registry\Registry;
use Joomla\Mediawiki\Sites;

/**
 * Test class for Sites.
 *
 * @since  1.0
 */
class SitesTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Registry  Options for the Mediawiki object.
	 * @since  1.0
	 */
	protected $options;

	/**
	 * @var    \PHPUnit_Framework_MockObject_MockObject  Mock client object.
	 * @since  1.0
	 */
	protected $client;

	/**
	 * @var    Sites  Object under test.
	 * @since  1.0
	 */
	protected $object;

	/**
	 * @var    \Joomla\Http\Response  Mock response object.
	 * @since  1.0
	 */
	protected $response;

	/**
	 * @var    string  Sample xml string.
	 * @since  1.0
	 */
	protected $sampleString = '<a><b></b><c></c></a>';

	/**
	 * @var    string  Sample xml error message.
	 * @since  1.0
	 */
	protected $errorString = '<message>Generic Error</message>';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	protected function setUp()
	{
		$this->options = new Registry;

		$errorLevel = error_reporting();
		error_reporting($errorLevel & ~E_DEPRECATED);

		$this->client = $this->getMock('\\Joomla\\Mediawiki\\Http', array('get', 'post', 'delete', 'patch', 'put'));
		$this->response = $this->getMock('\\Joomla\\Http\\Response');

		error_reporting($errorLevel);

		$this->object = new Sites($this->options, $this->client);
	}

	/**
	 * Tests the getSiteInfo method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetSiteInfo()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&meta=siteinfo&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getSiteInfo(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getEvents method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetEvents()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=logevents&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getEvents(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getRecentChanges method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetRecentChanges()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=recentchanges&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getRecentChanges(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getProtectedTitles method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetProtectedTitles()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=protectedtitles&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getProtectedTitles(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}
}
