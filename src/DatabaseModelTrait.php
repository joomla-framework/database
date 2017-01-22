<?php
/**
 * Part of the Joomla Framework Model Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model;

use Joomla\Database\DatabaseDriver;

/**
 * Trait representing a model holding a database reference
 *
 * @since  1.3.0
 * @note   As of 2.0 the `Joomla\Database\DatabaseInterface` will be typehinted.
 */
trait DatabaseModelTrait
{
	/**
	 * The database driver.
	 *
	 * @var    DatabaseDriver
	 * @since  1.3.0
	 */
	protected $db;

	/**
	 * Get the database driver.
	 *
	 * @return  DatabaseDriver  The database driver.
	 *
	 * @since   1.3.0
	 * @throws  \UnexpectedValueException
	 */
	public function getDb()
	{
		if ($this->db)
		{
			return $this->db;
		}

		throw new \UnexpectedValueException('Database driver not set in ' . __CLASS__);
	}

	/**
	 * Set the database driver.
	 *
	 * @param   DatabaseDriver  $db  The database driver.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	public function setDb(DatabaseDriver $db)
	{
		$this->db = $db;
	}
}
