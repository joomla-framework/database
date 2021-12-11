<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Tests\PhpUnit;

use PHPUnit\Framework\TestCase;

/**
 * Compatibility test case used for PHPUnit 7.x and later
 */
abstract class PhpUnit7TestCase extends TestCase
{
	/**
	 * This method is called before the first test of this test class is run.
	 */
	public static function setUpBeforeClass(): void
	{
		self::doSetUpBeforeClass();
	}

	/**
	 * This method is called after the last test of this test class is run.
	 */
	public static function tearDownAfterClass(): void
	{
		self::doTearDownAfterClass();
	}

	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp(): void
	{
		$this->doSetUp();
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown(): void
	{
		$this->doTearDown();
	}

	/**
	 * Compatibility method for `setUpBeforeClass()`
	 */
	protected static function doSetUpBeforeClass()
	{
		parent::setUpBeforeClass();
	}

	/**
	 * Compatibility method for `tearDownAfterClass()`
	 */
	protected static function doTearDownAfterClass()
	{
		parent::tearDownAfterClass();
	}

	/**
	 * Compatibility method for `setUp()`
	 */
	protected function doSetUp()
	{
		parent::setUp();
	}

	/**
	 * Compatibility method for `tearDown()`
	 */
	protected function doTearDown()
	{
		parent::tearDown();
	}
}
