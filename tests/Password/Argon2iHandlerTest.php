<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\Password;

use Joomla\Authentication\Password\Argon2iHandler;
use Joomla\Authentication\Tests\CompatTestCase;

/**
 * Test class for \Joomla\Authentication\Password\Argon2iHandler
 */
class Argon2iHandlerTest extends CompatTestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 */
	protected function doSetUp()
	{
		if (!Argon2iHandler::isSupported())
		{
			static::markTestSkipped('Argon2i algorithm is not supported.');
		}

		parent::doSetUp();
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
