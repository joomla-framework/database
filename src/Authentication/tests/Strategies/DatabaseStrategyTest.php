<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Strategies;

use Joomla\Authentication\Authentication;
use Joomla\Authentication\Password\HandlerInterface;
use Joomla\Authentication\Strategies\DatabaseStrategy;
use Joomla\Authentication\Tests\CompatTestCase;
use Joomla\Database\DatabaseDriver;
use Joomla\Input\Input;

/**
 * Test class for \Joomla\Authentication\Strategies\DatabaseStrategy
 */
class DatabaseStrategyTest extends CompatTestCase
{
	/**
	 * @var  DatabaseDriver|\PHPUnit_Framework_MockObject_MockObject
	 */
	private $db;

	/**
	 * @var  Input|\PHPUnit_Framework_MockObject_MockObject
	 */
	private $input;

	/**
	 * @var  HandlerInterface|\PHPUnit_Framework_MockObject_MockObject
	 */
	private $passwordHandler;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 */
	protected function doSetUp()
	{
		$this->db              = $this->getMockBuilder('Joomla\\Database\\DatabaseDriver')->disableOriginalConstructor()->getMock();
		$this->input           = $this->getMockBuilder('Joomla\\Input\\Input')->getMock();
		$this->passwordHandler = $this->getMockBuilder('Joomla\\Authentication\\Password\\HandlerInterface')->getMock();

		parent::doSetUp();
	}

	/**
	 * Tests the authenticate method with valid credentials.
	 */
	public function testValidPassword()
	{
		$query = $this->getMockBuilder('Joomla\\Database\\DatabaseQuery')->getMock();
		$query->expects($this->any())
			->method('select')
			->willReturnSelf();

		$query->expects($this->any())
			->method('from')
			->willReturnSelf();

		$query->expects($this->any())
			->method('where')
			->willReturnSelf();

		$this->db->expects($this->any())
			->method('getQuery')
			->willReturn($query);

		$this->db->expects($this->once())
			->method('setQuery')
			->with($query)
			->willReturnSelf();

		$this->db->expects($this->once())
			->method('loadResult')
			->willReturn('$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJG');

		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->any())
			->method('validatePassword')
			->willReturn(true);

		$strategy = new DatabaseStrategy($this->input, $this->db, array(), $this->passwordHandler);

		$this->assertEquals('username', $strategy->authenticate());
		$this->assertEquals(Authentication::SUCCESS, $strategy->getResult());
	}

	/**
	 * Tests the authenticate method with invalid credentials.
	 */
	public function testInvalidPassword()
	{
		$query = $this->getMockBuilder('Joomla\\Database\\DatabaseQuery')->getMock();
		$query->expects($this->any())
			->method('select')
			->willReturnSelf();

		$query->expects($this->any())
			->method('from')
			->willReturnSelf();

		$query->expects($this->any())
			->method('where')
			->willReturnSelf();

		$this->db->expects($this->any())
			->method('getQuery')
			->willReturn($query);

		$this->db->expects($this->once())
			->method('setQuery')
			->with($query)
			->willReturnSelf();

		$this->db->expects($this->once())
			->method('loadResult')
			->willReturn('$2y$10$.vpEGa99w.WUetDFJXjMn.RiKRhZ/ImzxtOjtoJ0VFDV8S7ua0uJH');

		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->any())
			->method('validatePassword')
			->willReturn(false);

		$strategy = new DatabaseStrategy($this->input, $this->db, array(), $this->passwordHandler);

		$this->assertEquals(false, $strategy->authenticate());
		$this->assertEquals(Authentication::INVALID_CREDENTIALS, $strategy->getResult());
	}

	/**
	 * Tests the authenticate method with no credentials provided.
	 */
	public function testNoPassword()
	{
		$this->db->expects($this->never())
			->method('setQuery');

		$this->input->expects($this->any())
			->method('get')
			->willReturn(false);

		$this->passwordHandler->expects($this->never())
			->method('validatePassword');

		$strategy = new DatabaseStrategy($this->input, $this->db, array(), $this->passwordHandler);

		$this->assertEquals(false, $strategy->authenticate());
		$this->assertEquals(Authentication::NO_CREDENTIALS, $strategy->getResult());
	}

	/**
	 * Tests the authenticate method with credentials for an unknown user.
	 */
	public function testUserNotExist()
	{
		$query = $this->getMockBuilder('Joomla\\Database\\DatabaseQuery')->getMock();
		$query->expects($this->any())
			->method('select')
			->willReturnSelf();

		$query->expects($this->any())
			->method('from')
			->willReturnSelf();

		$query->expects($this->any())
			->method('where')
			->willReturnSelf();

		$this->db->expects($this->any())
			->method('getQuery')
			->willReturn($query);

		$this->db->expects($this->once())
			->method('setQuery')
			->with($query)
			->willReturnSelf();

		$this->db->expects($this->once())
			->method('loadResult')
			->willReturn(null);

		$this->input->expects($this->any())
			->method('get')
			->willReturnArgument(0);

		$this->passwordHandler->expects($this->never())
			->method('validatePassword');

		$strategy = new DatabaseStrategy($this->input, $this->db, array(), $this->passwordHandler);

		$this->assertEquals(false, $strategy->authenticate());
		$this->assertEquals(Authentication::NO_SUCH_USER, $strategy->getResult());
	}
}
