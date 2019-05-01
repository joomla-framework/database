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
class ExporterCommand extends AbstractCommand
{
	/**
	 * The default command name
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $defaultName = 'database:exporter';

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
		$output->writeln([
			'DbExporterCmd',
      '=============',
      '',
    ]);
		$total_time = microtime(true);

		$tables   = $this->db->getTableList();
		$prefix   = $this->db->getPrefix();
		$exp      = $this->db->getExporter()->withStructure();

		$folderPath = $input->getOption('folder');
		$tableName = $input->getOption('table');
		$all = $input->getArgument('all');
		$zip = $input->getArgument('zip');

		$zipFile = $folderPath . 'data_exported_' . $this->db->getDateFormat() . '.zip';

		if (($tableName == null) && ($all == null))
		{
			$symfonyStyle->warning("[WARNING] Missing or wrong parameters");
			return 0;
		}

		if($tableName)
		{
			if (!\in_array($tableName, $tables))
			{
				$symfonyStyle->error('Not Found ' . $tableName . '....');
				$symfonyStyle->newLine();
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
		$this->addArgument('all', InputArgument::OPTIONAL, 'Dump all tables');
		$this->addArgument('zip', InputArgument::OPTIONAL, 'Dump in zip format');
		$this->setHelp(
<<<EOF
The <info>%command.name%</info> command for exporting the database

<info>php %command.full_name%</info>
EOF
		);
	}
}
