<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Password;

use Joomla\Authentication\Password\Argon2iHandler;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Authentication\Password\Argon2iHandler
 */
class Argon2iHandlerTest extends TestCase
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		if (!Argon2iHandler::isSupported())
		{
			$this->markTestSkipped('Argon2i algorithm is not supported.');
		}

		parent::setUp();
	}

	/**
	 * @testdox  A password is hashed and validated
	 *
	 * @covers   Joomla\Authentication\Password\Argon2iHandler::hashPassword
	 * @covers   Joomla\Authentication\Password\Argon2iHandler::validatePassword
	 */
	public function testAPasswordIsHashedAndValidated()
	{
		$handler = new Argon2iHandler;
		$hash = $handler->hashPassword('password');
		$this->assertTrue($handler->validatePassword('password', $hash), 'The hashed password was not validated.');
	}
}
