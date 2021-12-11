<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Service;

use Joomla\Database\DatabaseFactory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Service\DatabaseProvider;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Database\Service\DatabaseProvider
 */
class DatabaseProviderTest extends TestCase
{
	/**
	 * DI Container for testing
	 *
	 * @var  Container
	 */
	private $container;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		// Create a DI container for testing
		$this->container = new Container;
		$config = new Registry;
		$config->set('database.driver', 'sqlite');
		$config->set('database.name', ':memory:');
		$config->set('database.prefix', 'jos_');
		$this->container->set('config', $config);
	}

	/**
	 * @testdox  Verify that the DatabaseProvider returns a DatabaseInterface object
	 */
	public function testVerifyTheDatabaseDriverIsRegisteredToTheContainer()
	{
		$this->container->registerServiceProvider(new DatabaseProvider);

		$this->assertInstanceOf(
			DatabaseInterface::class, $this->container->get(DatabaseInterface::class)
		);
	}

	/**
	 * @testdox  Verify that the DatabaseProvider returns a DatabaseFactory object
	 */
	public function testVerifyTheDatabaseFactoryIsRegisteredToTheContainer()
	{
		$this->container->registerServiceProvider(new DatabaseProvider);

		$this->assertInstanceOf(
			DatabaseFactory::class, $this->container->get(DatabaseFactory::class)
		);
	}
}
