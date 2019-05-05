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
use Joomla\Filesystem\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;

/**
 * Console command for exporting the database
 *
 * @since  __DEPLOY_VERSION__
 */
class ExportCommand extends AbstractCommand
{
	/**
	 * The default command name
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $defaultName = 'database:export';

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

		$symfonyStyle->title('Exporting Database');

		$totalTime  = microtime(true);
		$date       = getdate();
		$dateFormat = sprintf("%s-%02s-%02s", $date['year'], $date['mon'], $date['mday']);

		$tables   = $this->db->getTableList();
		$prefix   = $this->db->getPrefix();
		$exporter = $this->db->getExporter()->withStructure();

		$folderPath = $input->getOption('folder');
		$tableName  = $input->getOption('table');
		$all        = $input->getOption('all');
		$zip        = $input->getOption('zip');

		$zipFile = $folderPath . '/' . 'data_exported_' . $dateFormat . '.zip';

		if ($tableName === null && $all === null)
		{
			$symfonyStyle->warning("Either the --table or --all option must be specified");

			return 1;
		}

		if ($tableName)
		{
			if (!\in_array($tableName, $tables))
			{
				$symfonyStyle->error($tableName . ' does not exist in the database.');

				return 1;
			}

			$tables = [$tableName];
		}

		if ($zip)
		{
			$zipArchive = (new Archive)->getAdapter('zip');
		}

		foreach ($tables as $table)
		{
			if (strpos(substr($table, 0, strlen($prefix)), $prefix) !== false)
			{
				$taskTime = microtime(true);
				$filename = $folderPath . '/' . $table . '.xml';

				$symfonyStyle->text('Exporting ' . $table . '....');

				$data = (string) $exporter->from($table)->withData(true);

				if (file_exists($filename))
				{
					File::delete($filename);
				}

				File::write($filename, $data);

				if ($zip)
				{
					$zipFilesArray[] = ['name' => $table . '.xml', 'data' => $data];
					$zipArchive->create($zipFile, $zipFilesArray);
					File::delete($filename);
				}

				$symfonyStyle->text('Exported in ' . round(microtime(true) - $taskTime, 3));
			}
		}

		$symfonyStyle->text('Total time: ' . round(microtime(true) - $totalTime, 3));
		$symfonyStyle->success('Finished Exporting Database');

		return 0;
	}

	/**
	 * Configure the command.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function configure()
	{
		$this->setDescription('Exports the database');
		$this->addOption('folder', null, InputOption::VALUE_OPTIONAL, 'Dump in folder path', '.');
		$this->addOption('table', null, InputOption::VALUE_REQUIRED, 'Dump table name');
		$this->addOption('all', null, InputOption::VALUE_NONE, 'Dump all tables');
		$this->addOption('zip', null, InputOption::VALUE_NONE, 'Dump in zip format');
	}
}
