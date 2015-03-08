<?php
/**
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests;

use Joomla\Authentication\Strategies\LocalStrategy;
use Joomla\Authentication\Authentication;
use Joomla\Input\Input;

/**
 * Test class for Authentication
 *
 * @since  1.0
 */
class LocalStrategyTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		$this->input = $this->getMock('Joomla\\Input\\Input');
	}

	/**
	 * Tests the authenticate method with valid credentials.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testValidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->will($this->returnArgument(0));

		$credentialStore = array(
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJG'
		);

		$localStrategy = new LocalStrategy($this->input, $credentialStore);

		$this->assertEquals('username', $localStrategy->authenticate());

		$this->assertEquals(Authentication::SUCCESS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with invalid credentials.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testInvalidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->will($this->returnArgument(0));

		$credentialStore = array(
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		);

		$localStrategy = new LocalStrategy($this->input, $credentialStore);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::INVALID_CREDENTIALS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with no credentials provided.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testNoPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->will($this->returnValue(false));

		$credentialStore = array(
			'username' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		);

		$localStrategy = new LocalStrategy($this->input, $credentialStore);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::NO_CREDENTIALS, $localStrategy->getResult());
	}

	/**
	 * Tests the authenticate method with credentials for an unknown user.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testUserNotExist()
	{
		$this->input->expects($this->any())
			->method('get')
			->will($this->returnArgument(0));

		$credentialStore = array(
			'jimbob' => '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH'
		);

		$localStrategy = new LocalStrategy($this->input, $credentialStore);

		$this->assertEquals(false, $localStrategy->authenticate());

		$this->assertEquals(Authentication::NO_SUCH_USER, $localStrategy->getResult());
	}
}
