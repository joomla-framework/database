<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Mediawiki\Tests;

use Joomla\Registry\Registry;
use Joomla\Mediawiki\Users;

/**
 * Test class for Users.
 *
 * @since  1.0
 */
class UsersTest extends \PHPUnit_Framework_TestCase
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
	 * @var    Users  Object under test.
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

		$this->object = new Users($this->options, $this->client);
	}

	/**
	 * Tests the login method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testLogin()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the logout method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testLogout()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the getUserInfo method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetUserInfo()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=users&ususers=Joomla&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getUserInfo(array('Joomla')),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getCurrentUserInfo method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetCurrentUserInfo()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&meta=userinfo&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getCurrentUserInfo(),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the getUserContribs method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testGetUserContribs()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=usercontribs&ucuser=Joomla&format=xml')
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getUserContribs('Joomla'),
			$this->equalTo(simplexml_load_string($this->sampleString))
		);
	}

	/**
	 * Tests the blockUser method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testBlockUser()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the unBlockUserByName method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testUnBlockUserByName()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the unBlockUserByID method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testUnBlockUserByID()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the assignGroup method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testAssignGroup()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
        );
	}

	/**
	 * Tests the emailUser method
	 *
	 * @return void
	 *
	 * @since  1.0
	 */
	public function testEmailUser()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
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
