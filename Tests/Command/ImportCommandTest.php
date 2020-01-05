<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Command;

use Joomla\Console\Application;
use Joomla\Database\Command\ImportCommand;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\UnsupportedAdapterException;
use Joomla\Test\DatabaseTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for Joomla\Database\Command\ImportCommand
 */
class ImportCommandTest extends DatabaseTestCase
{
	/**
	 * Path to the database stubs
	 *
	 * @var  null|string
	 */
	private $stubPath = null;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass(): void
	{
		if (!\defined('JPATH_ROOT'))
		{
			self::markTestSkipped('Constant `JPATH_ROOT` is not defined.');
		}

		parent::setUpBeforeClass();

		if (!static::$connection || static::$connection->getName() !== 'mysql')
		{
			self::markTestSkipped('MySQL database not configured.');
		}
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->stubPath = dirname(__DIR__) . '/Stubs/Importer';
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown(): void
	{
		foreach (static::$connection->getTableList() as $table)
		{
			static::$connection->dropTable($table);
		}

		parent::tearDown();
	}

	public function testTheDatabaseIsImportedWithAllTables()
	{
		$input  = new ArrayInput(
			[
				'command'  => 'database:import',
				'--all'    => true,
				'--folder' => $this->stubPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ImportCommand(static::$connection);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('Import completed in', $screenOutput);
	}

	public function testTheDatabaseIsImportedWithASingleTable()
	{
		$input  = new ArrayInput(
			[
				'command'  => 'database:import',
				'--table'  => 'dbtest',
				'--folder' => $this->stubPath,
				'--zip'    => 'dbtest.zip',
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ImportCommand(static::$connection);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('Import completed in', $screenOutput);
	}

	public function testTheCommandFailsIfTheDatabaseDriverDoesNotSupportImports()
	{
		$db = $this->createMock(DatabaseDriver::class);
		$db->expects($this->once())
			->method('getImporter')
			->willThrowException(new UnsupportedAdapterException('Testing'));

		$db->expects($this->once())
			->method('getName')
			->willReturn('test');

		$input  = new ArrayInput(
			[
				'command'  => 'database:import',
				'--folder' => $this->stubPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ImportCommand($db);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The "test" database driver does not', $screenOutput);
	}

	public function testTheCommandFailsIfRequiredOptionsAreMissing()
	{
		$input  = new ArrayInput(
			[
				'command'  => 'database:import',
				'--folder' => $this->stubPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ImportCommand(static::$connection);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('Either the --table or --all option', $screenOutput);
	}

	public function testTheCommandFailsIfTheRequestedTableDoesNotHaveAnImportFile()
	{
		$input  = new ArrayInput(
			[
				'command'  => 'database:import',
				'--table'  => 'unexisting_table',
				'--folder' => dirname($this->stubPath),
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ImportCommand(static::$connection);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The unexisting_table.xml file does not exist.', $screenOutput);
	}
}
