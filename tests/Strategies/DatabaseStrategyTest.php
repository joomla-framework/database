<?php
/**
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Strategies;

use Joomla\Authentication\Strategies\DatabaseStrategy;
use Joomla\Authentication\Authentication;
use Joomla\Input\Input;
use Joomla\Test\TestDatabase;

/**
 * Test class for \Joomla\Authentication\Strategies\DatabaseStrategy
 */
class DatabaseStrategyTest extends TestDatabase
{
	/**
	 * Inserts a user into the test database
	 *
	 * @param   string  $username  Test username
	 * @param   string  $password  Test hashed password
	 */
	private function addUser($username, $password)
	{
		// Insert the user into the table
		$db = self::$driver;

		$db->setQuery(
			$db->getQuery(true)
				->insert('#__users')
				->columns(array('username', 'password'))
				->values($db->quote($username) . ',' . $db->quote($password))
		)->execute();
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 */
	protected function setUp()
	{
		$this->input = $this->getMock('Joomla\\Input\\Input');
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 */
	protected function tearDown()
	{
		// Truncate the table
		$db = self::$driver;

		$db->setQuery(
			$db->getQuery(true)
				->delete('#__users')
		)->execute();
	}

	/**
	 * Tests the authenticate method with valid credentials.
	 */
	public function testValidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->addUser('username', '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJG');

		$strategy = new DatabaseStrategy($this->input, self::$driver);

		$this->assertEquals('username', $strategy->authenticate());
		$this->assertEquals(Authentication::SUCCESS, $strategy->getResult());
	}

	/**
	 * Tests the authenticate method with invalid credentials.
	 */
	public function testInvalidPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->addUser('username', '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH');

		$strategy = new DatabaseStrategy($this->input, self::$driver);

		$this->assertEquals(false, $strategy->authenticate());
		$this->assertEquals(Authentication::INVALID_CREDENTIALS, $strategy->getResult());
	}

	/**
	 * Tests the authenticate method with no credentials provided.
	 */
	public function testNoPassword()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturn(false);

		$this->addUser('username', '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH');

		$strategy = new DatabaseStrategy($this->input, self::$driver);

		$this->assertEquals(false, $strategy->authenticate());
		$this->assertEquals(Authentication::NO_CREDENTIALS, $strategy->getResult());
	}

	/**
	 * Tests the authenticate method with credentials for an unknown user.
	 */
	public function testUserNotExist()
	{
		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->addUser('jimbob', '$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH');

		$strategy = new DatabaseStrategy($this->input, self::$driver);

		$this->assertEquals(false, $strategy->authenticate());
		$this->assertEquals(Authentication::NO_SUCH_USER, $strategy->getResult());
	}
}
