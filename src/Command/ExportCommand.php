<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Command;

use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseInterface;
use Joomla\Archive\Archive;
use Joomla\Filesystem\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
	 * @var    DatabaseInterface
	 * @since  __DEPLOY_VERSION__
	 */
	private $db;

	/**
	 * Instantiate the command.
	 *
	 * @param   DatabaseInterface  $db  Database connector
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(DatabaseInterface $db)
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

		$total_time = microtime(true);

		$tables   = $this->db->getTableList();
		$prefix   = $this->db->getPrefix();
		$exp      = $this->db->getExporter()->withStructure();

		$folderPath = $input->getOption('folder');
		$tableName = $input->getOption('table');
		$all = $input->getOption('all');
		$zip = $input->getOption('zip');

		$zipFile = $folderPath . 'data_exported_' . $this->db->getDateFormat() . '.zip';

		if (($tableName == null) && ($all == null))
		{
			$symfonyStyle->warning("Either the --table or --all option must be specified");
			return 1;
		}

		if($tableName)
		{
			if (!\in_array($tableName, $tables))
			{
				$symfonyStyle->error($tableName . ' does not exist in the database.');
				return 1;
			}
			$tables = array($tableName);
		}

		if ($zip)
		{
			$archive = new Archive;
			$zipArchive = $archive->getAdapter('zip');
		}

		foreach ($tables as $table)
		{
			if (strpos(substr($table, 0, strlen($prefix)), $prefix) !== false)
			{
				$task_i_time = microtime(true);
				$filename    = $folderPath . '/' . $table . '.xml';
				$symfonyStyle->newLine();
				$symfonyStyle->text('Exporting ' . $table . '....');
				$data = (string) $exp->from($table)->withData(true);

				if (file_exists($filename))
				{
					File::delete($filename);
				}

				File::write($filename, $data);

				if ($zip)
				{
					$zipFilesArray[] = array('name' => $table . '.xml', 'data' => $data);
					$zipArchive->create($zipFile, $zipFilesArray);
					File::delete($filename);
				}

				$symfonyStyle->text('Exported in ' . round(microtime(true) - $task_i_time, 3));
			}
		}

		$symfonyStyle->text('Total time: ' . round(microtime(true) - $total_time, 3));
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
