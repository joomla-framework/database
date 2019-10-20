<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Command;

use Joomla\Console\Application;
use Joomla\Database\Command\ExportCommand;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseExporter;
use Joomla\Database\Exception\UnsupportedAdapterException;
use Joomla\Database\Tests\Cases\MysqlCase;
use Joomla\Filesystem\Folder;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for \Joomla\Database\Command\ExportCommand
 */
class ExportCommandTest extends MysqlCase
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
	public static function setUpBeforeClass()
	{
		if (!\defined('JPATH_ROOT'))
		{
			self::markTestSkipped('Constant `JPATH_ROOT` is not defined.');
		}

		parent::setUpBeforeClass();
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

		parent::tearDown();
	}

	public function testTheDatabaseIsExportedWithAllTables()
	{
		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--all'    => true,
				'--folder' => $this->testPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand(static::$driver);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertContains('Export completed in', $screenOutput);
	}

	public function testTheDatabaseIsExportedWithAllTablesInZipFormat()
	{
		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--all'    => true,
				'--folder' => $this->testPath,
				'--zip'    => true,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand(static::$driver);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertContains('Export completed in', $screenOutput);
	}

	public function testTheDatabaseIsExportedWithASingleTable()
	{
		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--table'  => 'dbtest',
				'--folder' => $this->testPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand(static::$driver);
		$command->setApplication($application);

		$this->assertSame(0, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertContains('Export completed in', $screenOutput);
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
		$this->assertContains('The "test" database driver does not', $screenOutput);
	}

	public function testTheCommandFailsIfRequiredOptionsAreMissing()
	{
		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--folder' => $this->testPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand(static::$driver);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertContains('Either the --table or --all option', $screenOutput);
	}

	public function testTheCommandFailsIfTheRequestedTableDoesNotExistInTheDatabase()
	{
		$exporter = $this->createMock(DatabaseExporter::class);
		$exporter->expects($this->once())
			->method('withStructure')
			->willReturnSelf();

		$db = $this->createMock(DatabaseDriver::class);
		$db->expects($this->once())
			->method('getExporter')
			->willReturn($exporter);

		$db->expects($this->once())
			->method('getTableList')
			->willReturn([]);

		$db->expects($this->once())
			->method('getPrefix')
			->willReturn('');

		$input  = new ArrayInput(
			[
				'command'  => 'database:export',
				'--table'  => 'dbtest',
				'--folder' => $this->testPath,
			]
		);
		$output = new BufferedOutput;

		$application = new Application($input, $output);

		$command = new ExportCommand($db);
		$command->setApplication($application);

		$this->assertSame(1, $command->execute($input, $output));

		$screenOutput = $output->fetch();
		$this->assertContains('The dbtest table does not exist', $screenOutput);
	}
}
