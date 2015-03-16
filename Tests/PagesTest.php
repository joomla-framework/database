<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Mediawiki\Tests;

use Joomla\Registry\Registry;
use Joomla\Mediawiki\Pages;

/**
 * Test class for Pages.
 *
 * @since  1.0
 */
class PagesTest extends \PHPUnit_Framework_TestCase
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
	 * @var    Pages  Object under test.
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
		$this->client = $this->getMock('\\Joomla\\Mediawiki\\Http', array('get', 'post', 'delete', 'patch', 'put'));
		$this->response = $this->getMock('\\Joomla\\Http\\Response');

		$this->object = new Pages($this->options, $this->client);
	}

	/**
	 * Tests the editPage method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testEditPage()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the deletePageByName method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testDeletePageByName()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the deletePageByID method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testDeletePageByID()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the undeletePage method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testUndeletePage()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the movePageByName method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testMovePageByName()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the movePageByID method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testMovePageByID()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the rollback method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testRollback()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the changeProtection method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testChangeProtection()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the getPageInfo method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetPageInfo()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=info&titles=Main Page&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getPageInfo(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getPageProperties method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetPageProperties()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=pageprops&titles=Main Page&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getPageProperties(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getRevisions method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetRevisions()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=pageprops&titles=Main Page&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getPageProperties(array('Main Page')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getBackLinks method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetBackLinks()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=backlinks&bltitle=Joomla&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getBackLinks('Joomla'),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getIWBackLinks method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetIWBackLinks()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=iwbacklinks&iwbltitle=Joomla&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getIWBackLinks('Joomla'),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getToken method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetToken()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}
}
