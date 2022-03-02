<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Defines the interface for a DatabaseInterface aware class.
 *
 * @since  2.0.3
 */
interface DatabaseAwareInterface
{
	/**
	 * Get the database.
	 *
	 * @return  DatabaseInterface
	 *
	 * @since   2.0.3
	 * @throws  DatabaseNotFoundException May be thrown if the database has not been set.
	 */
	public function getDatabase(): DatabaseInterface;

	/**
	 * Set the database.
	 *
	 * @param   DatabaseInterface  $db  The database.
	 *
	 * @return  void
	 *
	 * @since   2.0.3
	 */
	public function setDatabase(DatabaseInterface $db): void;
}
