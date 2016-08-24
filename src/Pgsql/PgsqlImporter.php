<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Pgsql;

use Joomla\Database\Postgresql\PostgresqlImporter;

/**
 * PDO PostgreSQL Database Importer.
 *
 * @since  __DEPLOY_VERSION__
 */
class PgsqlImporter extends PostgresqlImporter
{
	/**
	 * Checks if all data and options are in order prior to exporting.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception if an error is encountered.
	 */
	public function check()
	{
		// Check if the db connector has been set.
		if (!($this->db instanceof PgsqlDriver))
		{
			throw new \Exception('Database connection wrong type.');
		}

		// Check if the tables have been specified.
		if (empty($this->from))
		{
			throw new \Exception('ERROR: No Tables Specified');
		}

		return $this;
	}
}
