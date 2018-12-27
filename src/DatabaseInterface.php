<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Joomla Framework Database Interface
 *
 * @since  1.0
 */
interface DatabaseInterface
{
	/**
	 * Test to see if the connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   1.0
	 */
	public static function isSupported();

	/**
	 * Quotes a binary string to database requirements for use in database queries.
	 *
	 * @param   string  $data  A binary string to quote.
	 *
	 * @return  string  The binary quoted input string.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function quoteBinary($data);

	/**
	 * Replace special placeholder representing binary field with the original string.
	 *
	 * @param   string|resource  $data  Encoded string or resource.
	 *
	 * @return  string  The original string.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function decodeBinary($data);
}
