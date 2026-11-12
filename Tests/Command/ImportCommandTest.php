<?php

/**
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Command;

use Joomla\Console\Application;
use Joomla\Database\Command\ImportCommand;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseImporter;
use Joomla\Database\Exception\UnsupportedAdapterException;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Test class for Joomla\Database\Command\ImportCommand
 */
class ImportCommandTest extends TestCase
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
        if (!\defined('JPATH_ROOT')) {
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

        $this->stubPath = dirname(__DIR__) . '/Stubs/Importer';
    }

    public function testTheDatabaseIsImportedWithAllTables()
    {
        $db       = $this->createMock(DatabaseDriver::class);
        $importer = $this->createMock(DatabaseImporter::class);

        $importer->expects($this->once())
            ->method('withStructure')
            ->with(true)
            ->willReturnSelf();
        $importer->expects($this->once())
            ->method('asXml')
            ->willReturnSelf();
        $importer->expects($this->once())
            ->method('mergeStructure');
        $importer->expects($this->once())
            ->method('importData');

        $db->expects($this->once())
            ->method('getImporter')
            ->willReturn($importer);
        $db->expects($this->once())
            ->method('dropTable')
            ->with('dbtest', true);

        $input  = new ArrayInput(
            [
                'command'  => 'database:import',
                '--folder' => $this->stubPath,
            ]
        );
        $output = new BufferedOutput();

        $application = new Application($input, $output);

        $command = new ImportCommand($db);
        $command->setApplication($application);

        $this->assertSame(0, $command->execute($input, $output));

        $screenOutput = $output->fetch();
        $this->assertStringContainsString('Importing dbtest from dbtest.xml', $screenOutput);
        $this->assertStringContainsString('Processing the dbtest table', $screenOutput);
        $this->assertStringContainsString('Imported data for dbtest.xml in', $screenOutput);
        $this->assertStringContainsString('Import completed in', $screenOutput);
    }

    #[RequiresPhpExtension('zip')]
    public function testAZipFileIsExtractedInsideTheFolder()
    {
        $folder = sys_get_temp_dir() . '/joomla-db-import-' . uniqid();
        mkdir($folder);

        $zip = new \ZipArchive();
        $zip->open($folder . '/dump.zip', \ZipArchive::CREATE);
        $zip->addFile($this->stubPath . '/dbtest.xml', 'dbtest.xml');
        $zip->close();

        $db       = $this->createMock(DatabaseDriver::class);
        $importer = $this->createMock(DatabaseImporter::class);

        $importer->method('withStructure')
            ->willReturnSelf();
        $importer->method('asXml')
            ->willReturnSelf();
        // Let the import fail, so that the extracted files are left behind and their location can be checked
        $importer->expects($this->once())
            ->method('importData')
            ->willThrowException(new \RuntimeException('Import failed'));

        $db->method('getPrefix')
            ->willReturn('db');
        $db->method('getImporter')
            ->willReturn($importer);
        $db->expects($this->once())
            ->method('dropTable')
            ->with('dbtest', true);

        $input  = new ArrayInput(
            [
                'command'  => 'database:import',
                '--folder' => $folder,
                '--zip'    => 'dump.zip',
            ]
        );
        $output = new BufferedOutput();

        $command = new ImportCommand($db);
        $command->setApplication(new Application($input, $output));

        try {
            $this->assertSame(1, $command->execute($input, $output));
            $this->assertStringContainsString('Import failed', $output->fetch());
            $this->assertFileExists($folder . '/dump/dbtest.xml', 'The archive must be extracted into a sub folder of the folder');
            $this->assertDirectoryDoesNotExist($folder . 'dump');
        } finally {
            foreach ([$folder . '/dump', $folder . 'dump'] as $extracted) {
                @unlink($extracted . '/dbtest.xml');
                @rmdir($extracted);
            }

            @unlink($folder . '/dump.zip');
            @rmdir($folder);
        }
    }

    public function testTheDatabaseIsImportedWithASingleTable()
    {
        $db       = $this->createMock(DatabaseDriver::class);
        $importer = $this->createMock(DatabaseImporter::class);

        $importer->expects($this->once())
            ->method('withStructure')
            ->with(true)
            ->willReturnSelf();
        $importer->expects($this->once())
            ->method('asXml')
            ->willReturnSelf();
        $importer->expects($this->once())
            ->method('mergeStructure');
        $importer->expects($this->once())
            ->method('importData');

        $db->expects($this->once())
            ->method('getImporter')
            ->willReturn($importer);
        $db->expects($this->once())
            ->method('dropTable')
            ->with('dbtest', true);

        $input  = new ArrayInput(
            [
                'command'  => 'database:import',
                '--table'  => 'dbtest',
                '--folder' => $this->stubPath,
            ]
        );
        $output = new BufferedOutput();

        $application = new Application($input, $output);

        $command = new ImportCommand($db);
        $command->setApplication($application);

        $this->assertSame(0, $command->execute($input, $output));

        $screenOutput = $output->fetch();
        $this->assertStringContainsString('Importing dbtest from dbtest.xml', $screenOutput);
        $this->assertStringContainsString('Processing the dbtest table', $screenOutput);
        $this->assertStringContainsString('Imported data for dbtest.xml in', $screenOutput);
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
        $output = new BufferedOutput();

        $application = new Application($input, $output);

        $command = new ImportCommand($db);
        $command->setApplication($application);

        $this->assertSame(1, $command->execute($input, $output));

        $screenOutput = $output->fetch();
        $this->assertStringContainsString('The "test" database driver does not', $screenOutput);
    }

    public function testTheCommandFailsIfTheRequestedTableDoesNotHaveAnImportFile()
    {
        $db       = $this->createMock(DatabaseDriver::class);
        $importer = $this->createMock(DatabaseImporter::class);

        $importer->expects($this->once())
            ->method('withStructure')
            ->with(true)
            ->willReturnSelf();
        $importer->expects($this->once())
            ->method('asXml')
            ->willReturnSelf();
        $importer->expects($this->never())
            ->method('mergeStructure');
        $importer->expects($this->never())
            ->method('importData');

        $db->expects($this->once())
            ->method('getImporter')
            ->willReturn($importer);
        $db->expects($this->never())
            ->method('dropTable');

        $input  = new ArrayInput(
            [
                'command'  => 'database:import',
                '--table'  => 'unexisting_table',
                '--folder' => dirname($this->stubPath),
            ]
        );
        $output = new BufferedOutput();

        $application = new Application($input, $output);

        $command = new ImportCommand($db);
        $command->setApplication($application);

        $this->assertSame(1, $command->execute($input, $output));

        $screenOutput = $output->fetch();
        $this->assertStringContainsString('The unexisting_table.xml file does not exist.', $screenOutput);
    }
}
