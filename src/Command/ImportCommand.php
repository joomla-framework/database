<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Command;

use Joomla\Archive\Archive;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Database\Exception\UnsupportedAdapterException;
use Joomla\Filesystem\Exception\FilesystemException;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;

/**
 * Console command for importing the database
 *
 * @since  __DEPLOY_VERSION__
 */
class ImportCommand extends AbstractCommand
{
	/**
	 * The default command name
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $defaultName = 'database:import';

	/**
	 * Database connector
	 *
	 * @var    DatabaseDriver
	 * @since  __DEPLOY_VERSION__
	 */
	private $db;

	/**
	 * Instantiate the command.
	 *
	 * @param   DatabaseDriver  $db  Database connector
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->db = $db;

		parent::__construct();
	}

	/**
	 * Checks if the zip file contains database export files
	 *
	 * @param   string  $archive  A zip archive to analyze
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	private function checkZipFile($archive)
	{
		if (!extension_loaded('zip'))
		{
			throw new \RuntimeException('Zip extension is not loaded');
		}

		$zip = zip_open($archive);

		if (!\is_resource($zip))
		{
			throw new \RuntimeException('Unable to open archive');
		}

		while ($file = @zip_read($zip))
		{
			if (strpos(zip_entry_name($file), $this->db->getPrefix()) === false)
			{
				zip_entry_close($file);
				@zip_close($zip);
				throw new \RuntimeException('Unable to find prefix');
			}

			zip_entry_close($file);
		}

		@zip_close($zip);
	}

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$symfonyStyle = new SymfonyStyle($input, $output);

		$symfonyStyle->title('Importing Database');

		$totalTime = microtime(true);

		// Make sure the database supports imports before we get going
		try
		{
			$importer = $this->db->getImporter()
				->withStructure()
				->asXml();
		}
		catch (UnsupportedAdapterException $e)
		{
			$symfonyStyle->error(sprintf('The "%s" database driver does not support importing data.', $this->db->getName()));

			return 1;
		}

		$folderPath = $input->getOption('folder');
		$tableName  = $input->getOption('table');
		$all        = $input->getOption('all');
		$zipFile    = $input->getOption('zip');

		if ($tableName === null && $all === false)
		{
			$symfonyStyle->warning('Either the --table or --all option must be specified');

			return 1;
		}

		if ($zipFile)
		{
			$zipPath = $folderPath . '/' . $zipFile;

			try
			{
				$this->checkZipFile($zipPath);
			}
			catch (\RuntimeException $e)
			{
				$symfonyStyle->error($e->getMessage());

				return 1;
			}

			if (class_exists('\\Joomla\\Filesystem\\File'))
			{
				$folderPath .= File::stripExt($zipFile);
			}
			else
			{
				$symfonyStyle->error('The Joomla Filesystem dependency is not loaded.');

				return 1;
			}

			if (class_exists('\\Joomla\\Filesystem\\Folder'))
			{
				try
				{
					Folder::create($folderPath);
				}
				catch (FilesystemException $e)
				{
					$symfonyStyle->error($e->getMessage());

					return 1;
				}
			}
			else
			{
				$symfonyStyle->error('The Joomla Filesystem dependency is not loaded.');

				return 1;
			}

			if (class_exists('\\Joomla\\Archive\\Archive'))
			{
				try
				{
					(new Archive)->extract($zipPath, $folderPath);
				}
				catch (\RuntimeException $e)
				{
					$symfonyStyle->error($e->getMessage());
					Folder::delete($folderPath);

					return 1;
				}
			}
			else
			{
				$symfonyStyle->error('The Joomla Archive dependency is not loaded.');

				return 1;
			}
		}

		if ($tableName)
		{
			$tables = [$tableName . '.xml'];
		}
		else
		{
			$tables = Folder::files($folderPath, '\.xml$');
		}

		foreach ($tables as $table)
		{
			$taskTime = microtime(true);
			$percorso = $folderPath . '/' . $table;

			// Check file
			if (!file_exists($percorso))
			{
				$symfonyStyle->error(sprintf('The %s file does not exist.', $table));

				return 1;
			}

			$tableName = str_replace('.xml', '', $table);
			$symfonyStyle->text(sprintf('Importing %1$s from %2$s', $tableName, $table));

			$importer->from(file_get_contents($percorso));

			$symfonyStyle->text(sprintf('Processing the %s table', $tableName));

			try
			{
				$this->db->dropTable($tableName, true);
			}
			catch (ExecutionFailureException $e)
			{
				$symfonyStyle->error(sprintf('Error executing the DROP TABLE statement for %1$s: %2$s', $tableName, $e->getMessage()));

				return 1;
			}

			try
			{
				$importer->mergeStructure();
			}
			catch (\Exception $e)
			{
				$symfonyStyle->error(sprintf('Error merging the structure for %1$s: %2$s', $tableName, $e->getMessage()));

				return 1;
			}

			try
			{
				$importer->importData();
			}
			catch (\Exception $e)
			{
				$symfonyStyle->error(sprintf('Error importing the data for %1$s: %2$s', $tableName, $e->getMessage()));

				return 1;
			}

			$symfonyStyle->text(sprintf('Imported data for %s in %d seconds', $table, round(microtime(true) - $taskTime, 3)));
		}

		if ($zipFile)
		{
			Folder::delete($folderPath);
		}

		$symfonyStyle->success(sprintf('Import completed in %d seconds', round(microtime(true) - $totalTime, 3)));

		return 0;
	}

	/**
	 * Configure the command.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function configure(): void
	{
		$this->setDescription('Import the database');
		$this->addOption('folder', null, InputOption::VALUE_OPTIONAL, 'Path to the folder containing files to import', '.');
		$this->addOption('zip', null, InputOption::VALUE_REQUIRED, 'The name of a ZIP file to import');
		$this->addOption('table', null, InputOption::VALUE_REQUIRED, 'The name of the database table to import');
		$this->addOption('all', null, InputOption::VALUE_NONE, 'Flag indicating all database tables should be imported');
	}
}
