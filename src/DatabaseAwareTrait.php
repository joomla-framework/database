<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

use Joomla\Database\Exception\DatabaseNotFoundException;

/**
 * Defines the trait for a Database Aware Class.
 *
 * @since  2.0.3
 */
trait DatabaseAwareTrait
{
	/**
	 * Database
	 *
	 * @var	   DatabaseInterface
	 * @since  2.0.3
	 */
	private $_db;

	/**
	 * Get the database.
	 *
	 * @return  DatabaseInterface
	 *
	 * @since   2.0.3
	 * @throws  DatabaseNotFoundException May be thrown if the database has not been set.
	 */
	public function getDatabase()
	{
		if ($this->_db) {
			return $this->_db;
		}

		throw new DatabaseNotFoundException('Database not set in ' . \get_class($this));
	}

	/**
	 * Set the database.
	 *
	 * @param   DatabaseInterface  $db  The database.
	 *
	 * @return  void
	 *
	 * @since   2.0.3
	 */
	public function setDatabase(DatabaseInterface $db): void
	{
		$this->_db = $db;
	}
}
