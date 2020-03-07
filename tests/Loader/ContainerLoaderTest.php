<?php
/**
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Tests\Loader;

use Joomla\Console\Loader\ContainerLoader;
use Joomla\Console\Tests\Fixtures\Command\NamespacedCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Test class for \Joomla\Console\Loader\ContainerLoader
 */
class ContainerLoaderTest extends TestCase
{
	/**
	 * @var  ContainerInterface|MockObject
	 */
	protected $container;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		$this->container = $this->createMock(ContainerInterface::class);
	}

	/**
	 * @covers  Joomla\Console\Loader\ContainerLoader
	 * @uses    Joomla\Console\Command\AbstractCommand
	 */
	public function testTheLoaderRetrievesACommand()
	{
		$command = new NamespacedCommand;

		$commandName = $command->getName();
		$serviceId   = 'test.loader';

		$this->container->expects($this->once())
			->method('has')
			->with($serviceId)
			->willReturn(true);

		$this->container->expects($this->once())
			->method('get')
			->with($serviceId)
			->willReturn($command);

		$this->assertSame(
			$command,
			(new ContainerLoader($this->container, [$commandName => $serviceId]))->get($commandName)
		);
	}

	/**
	 * @covers  Joomla\Console\Loader\ContainerLoader
	 */
	public function testTheLoaderDoesNotRetrieveAnUnknownCommand()
	{
		$this->expectException(CommandNotFoundException::class);

		$commandName = 'test:loader';
		$serviceId   = 'test.loader';

		$this->container->expects($this->once())
			->method('has')
			->with($serviceId)
			->willReturn(false);

		$this->container->expects($this->never())
			->method('get');

		(new ContainerLoader($this->container, [$commandName => $serviceId]))->get($commandName);
	}

	/**
	 * @covers  Joomla\Console\Loader\ContainerLoader
	 */
	public function testTheLoaderHasACommand()
	{
		$commandName = 'test:loader';
		$serviceId   = 'test.loader';

		$this->container->expects($this->once())
			->method('has')
			->with($serviceId)
			->willReturn(true);

		$this->assertTrue(
			(new ContainerLoader($this->container, [$commandName => $serviceId]))->has($commandName)
		);
	}
}
