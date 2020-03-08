<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Password;

use Joomla\Authentication\Password\Argon2idHandler;
use Joomla\Authentication\Tests\CompatTestCase;

/**
 * Test class for \Joomla\Authentication\Password\Argon2idHandler
 */
class Argon2idHandlerTest extends CompatTestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 */
	protected static function doSetUpBeforeClass()
	{
		if (!Argon2idHandler::isSupported())
		{
			self::markTestSkipped('Argon2id algorithm is not supported.');
		}

		parent::doSetUpBeforeClass();
	}

	/**
	 * @testdox  A password is hashed and validated
	 *
	 * @covers   Joomla\Authentication\Password\Argon2idHandler::hashPassword
	 * @covers   Joomla\Authentication\Password\Argon2idHandler::validatePassword
	 */
	public function testAPasswordIsHashedAndValidated()
	{
		$handler = new Argon2idHandler;
		$hash = $handler->hashPassword('password');
		$this->assertTrue($handler->validatePassword('password', $hash), 'The hashed password was not validated.');
	}
}
