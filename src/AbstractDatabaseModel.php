<?php
/**
 * Part of the Joomla Framework Model Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model;

use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

/**
 * Joomla Framework Database Model Class
 *
 * @since       1.0
 * @deprecated  2.0  Implement the model interfaces directly; the concrete implementations are provided as traits
 */
abstract class AbstractDatabaseModel extends AbstractModel implements DatabaseModelInterface
{
	/**
	 * The database driver.
	 *
	 * @var    DatabaseDriver
	 * @since  1.0
	 */
	protected $db;

	/**
	 * Instantiate the model.
	 *
	 * @param   DatabaseDriver  $db     The database adapter.
	 * @param   Registry        $state  The model state.
	 *
	 * @since   1.0
	 */
	public function __construct(DatabaseDriver $db, Registry $state = null)
	{
		$this->db = $db;

		parent::__construct($state);
	}

	/**
	 * Get the database driver.
	 *
	 * @return  DatabaseDriver  The database driver.
	 *
	 * @since   1.0
	 */
	public function getDb()
	{
		return $this->db;
	}

	/**
	 * Set the database driver.
	 *
	 * @param   DatabaseDriver  $db  The database driver.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setDb(DatabaseDriver $db)
	{
		$this->db = $db;
	}
}
