<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Strategies;

use Joomla\Authentication\Strategies\LocalStrategy;
use Joomla\Authentication\Authentication;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Authentication\Strategies\LocalStrategy
 */
class LocalStrategyTest extends TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 */
	protected function setUp()
	{
		$this->input = $this->getMockBuilder('Joomla\\Input\\Input')->getMock();
	}

	/**
	 * Tests the authenticate method with valid credentials.
	 */
	public function testValidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$credentialStore = array(
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJG'
		);

		$localStrategy = new LocalStrategy($this->input, $credentialStore);

		$this->assertEquals('username', $localStrategy->authenticate());

		$this->assertEquals(Authentication::SUCCESS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with invalid credentials.
	 */
	public function testInvalidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$credentialStore = array(
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		);

		$localStrategy = new LocalStrategy($this->input, $credentialStore);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::INVALID_CREDENTIALS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with no credentials provided.
	 */
	public function testNoPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturn(false);

		$credentialStore = array(
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		);

		$localStrategy = new LocalStrategy($this->input, $credentialStore);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::NO_CREDENTIALS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with credentials for an unknown user.
	 */
	public function testUserNotExist()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$credentialStore = array(
			'jimbob' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		);

		$localStrategy = new LocalStrategy($this->input, $credentialStore);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::NO_SUCH_USER, $localStrategy->getResult());
	}
}
