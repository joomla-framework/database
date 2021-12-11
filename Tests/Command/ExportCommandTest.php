<?php
/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Command;

use Joomla\Console\Application;
use Joomla\Database\Command\ExportCommand;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Exception\UnsupportedAdapterException;
use Joomla\Filesystem\Folder;
use Joomla\Test\DatabaseTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for Joomla\Database\Command\ExportCommand
 */
class ExportCommandTest extends DatabaseTestCase
{
	/**
	 * Path to the test space
	 *
	 * @var  null|string
	 */
	private $testPath = null;

	/**
	 * Storage for the system's umask
	 *
	 * @var  integer
	 */
	private $umask;

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

		try
		{
			foreach (DatabaseDriver::splitSql(file_get_contents(dirname(__DIR__) . '/Stubs/Schema/mysql.sql')) as $query)
			{
				static::$connection->setQuery($query)
					->execute();
			}
		}
		catch (ExecutionFailureException $exception)
		{
			$this->markTestSkipped(
				\sprintf(
					'Could not load MySQL database: %s',
					$exception->getMessage()
				)
			);
		}

		$this->umask    = umask(0);
		$this->testPath = sys_get_temp_dir() . '/' . microtime(true) . '.' . mt_rand();

		mkdir($this->testPath, 0777, true);

		$this->testPath = realpath($this->testPath);
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown(): void
	{
		Folder::delete($this->testPath);

		umask($this->umask);

		foreach (static::$connection->getTableList() as $table)
		{
			static::$connection->dropTable($table);
		}

		parent::tearDown();
	}

	/**
	 * Loads the example data into the database.
	 *
	 * @return  void
	 */
	protected function loadExampleData(): void
	{
		$data = [
			(object) [
				'id'          => 1,
				'title'       => 'Testing1',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row one',
			],
			(object) [
				'id'          => 2,
				'title'       => 'Testing2',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row two',
			],
			(object) [
				'id'          => 3,
				'title'       => 'Testing3',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row three',
			],
			(object) [
				'id'          => 4,
				'title'       => 'Testing4',
				'start_date'  => '2019-10-26 00:00:00',
				'description' => 'test row four',
			],
		];

		foreach ($data as $row)
		{
			static::$connection->insertObject('#__dbtest', $row);
		}
	}

	public function testTheDatabaseIsExportedWithAllTables()
	{
		$this->loadExampleData();

		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--folder' => $this->testPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand(static::$connection);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('Export completed in', $screenOutput);
	}

	public function testTheDatabaseIsExportedWithAllTablesInZipFormat()
	{
		$this->loadExampleData();

		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--folder' => $this->testPath,
				'--zip'    => true,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand(static::$connection);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('Export completed in', $screenOutput);
	}

	public function testTheDatabaseIsExportedWithASingleTable()
	{
		$this->loadExampleData();

		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--table'  => 'dbtest',
				'--folder' => $this->testPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand(static::$connection);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('Export completed in', $screenOutput);
	}

	public function testTheCommandFailsIfTheDatabaseDriverDoesNotSupportExports()
	{
		$db = $this->createMock(DatabaseDriver::class);
		$db->expects($this->once())
			->method('getExporter')
			->willThrowException(new UnsupportedAdapterException('Testing'));

		$db->expects($this->once())
			->method('getName')
			->willReturn('test');

		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--folder' => $this->testPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand($db);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The "test" database driver does not', $screenOutput);
	}

	public function testTheCommandFailsIfTheRequestedTableDoesNotExistInTheDatabase()
	{
		$this->loadExampleData();

		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--table'  => 'unexisting_table',
				'--folder' => $this->testPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand(static::$connection);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertStringContainsString('The unexisting_table table does not exist', $screenOutput);
	}
}
