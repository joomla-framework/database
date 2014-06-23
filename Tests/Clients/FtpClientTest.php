<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Clients\Tests;

use Joomla\Filesystem\Clients\FtpClient;
use Joomla\Test\TestHelper;

/**
 * Test class for FtpClient.
 *
 * @since  1.0
 */
class FtpClientTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    FtpClient
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->object = new FtpClient;

		include_once __DIR__ . "/../Stubs/PHPFTPStub.php";
	}

	/**
	 * Test...
	 *
	 * @return void
	 */
	public function test__construct()
	{
		$object = new FtpClient;

		$this->assertEquals(
			FTP_BINARY,
			TestHelper::getValue($object, 'type')
		);

		$this->assertEquals(
			15,
			TestHelper::getValue($object, 'timeout')
		);

		$object = new FtpClient(array('type' => FTP_ASCII, 'timeout' => 200));

		$this->assertEquals(
			FTP_ASCII,
			TestHelper::getValue($object, 'type')
		);

		$this->assertEquals(
			200,
			TestHelper::getValue($object, 'timeout')
		);
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGetInstance().
	 *
	 * @return void
	 */
	public function testGetInstance()
	{
		$object = FtpClient::getInstance();

		// Connet to 127.0.0.1
		$this->assertInstanceOf(
			'\\Joomla\\Filesystem\\Clients\\FtpClient',
			$object
		);

		// Retrieve using sig., set options and login
		$oldObject = FtpClient::getInstance(
			'127.0.0.1',
			'21',
			array('type' => FTP_ASCII, 'timeout' => 200)
		);

		$this->assertEquals(
			$object,
			$oldObject
		);

		$this->assertEquals(
			FTP_ASCII,
			TestHelper::getValue($oldObject, 'type')
		);

		$this->assertEquals(
			200,
			TestHelper::getValue($oldObject, 'timeout')
		);

        //  Login
        $object = FtpClient::getInstance(
            'localhost',
            '21',
            array('type' => FTP_ASCII, 'timeout' => 200),
            'anonymous',
            ''
        );

        $this->assertInstanceOf(
            '\\Joomla\\Filesystem\\Clients\\FtpClient',
            $object
        );
	}

	/**
	 * Test...
	 *
	 * @todo Implement testSetOptions().
	 *
	 * @return void
	 */
	public function testSetOptions()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testConnect().
	 *
	 * @return void
	 */
	public function testConnect()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testIsConnected().
	 *
	 * @return void
	 */
	public function testIsConnected()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testLogin().
	 *
	 * @return void
	 */
	public function testLogin()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testQuit().
	 *
	 * @return void
	 */
	public function testQuit()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testPwd().
	 *
	 * @return void
	 */
	public function testPwd()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testSyst().
	 *
	 * @return void
	 */
	public function testSyst()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testChdir().
	 *
	 * @return void
	 */
	public function testChdir()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testReinit().
	 *
	 * @return void
	 */
	public function testReinit()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testRename().
	 *
	 * @return void
	 */
	public function testRename()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testChmod().
	 *
	 * @return void
	 */
	public function testChmod()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testDelete().
	 *
	 * @return void
	 */
	public function testDelete()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testMkdir().
	 *
	 * @return void
	 */
	public function testMkdir()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testRestart().
	 *
	 * @return void
	 */
	public function testRestart()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testCreate().
	 *
	 * @return void
	 */
	public function testCreate()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testRead().
	 *
	 * @return void
	 */
	public function testRead()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testGet().
	 *
	 * @return void
	 */
	public function testGet()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testStore().
	 *
	 * @return void
	 */
	public function testStore()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testWrite().
	 *
	 * @return void
	 */
	public function testWrite()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testListNames().
	 *
	 * @return void
	 */
	public function testListNames()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement testListDetails().
	 *
	 * @return void
	 */
	public function testListDetails()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement test_putCmd().
	 *
	 * @return void
	 */
	public function test_putCmd()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement test_verifyResponse().
	 *
	 * @return void
	 */
	public function test_verifyResponse()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement test_passive().
	 *
	 * @return void
	 */
	public function test_passive()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement test_findMode().
	 *
	 * @return void
	 */
	public function test_findMode()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * Test...
	 *
	 * @todo Implement test_mode().
	 *
	 * @return void
	 */
	public function test_mode()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete('This test has not been implemented yet.');
	}
}
