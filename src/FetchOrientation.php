<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Class defining the fetch orientation for prepared statements
 *
 * The values of the constants in this class match the `PDO::FETCH_ORI_*` constants.
 *
 * @since  2.0.0-beta
 */
final class FetchOrientation
{
	/**
	 * Fetch the next row in the result set. Valid only for scrollable cursors.
	 *
	 * @var    integer
	 * @since  2.0.0-beta
	 */
	public const NEXT = 0;

	/**
	 * Fetch the previous row in the result set. Valid only for scrollable cursors.
	 *
	 * @var    integer
	 * @since  2.0.0-beta
	 */
	public const PRIOR = 1;

	/**
	 * Fetch the first row in the result set. Valid only for scrollable cursors.
	 *
	 * @var    integer
	 * @since  2.0.0-beta
	 */
	public const FIRST = 2;

	/**
	 * Fetch the last row in the result set. Valid only for scrollable cursors.
	 *
	 * @var    integer
	 * @since  2.0.0-beta
	 */
	public const LAST = 3;

	/**
	 * Fetch the requested row by row number from the result set. Valid only for scrollable cursors.
	 *
	 * @var    integer
	 * @since  2.0.0-beta
	 */
	public const ABS = 4;

	/**
	 * Fetch the requested row by relative position from the current position of the cursor in the result set. Valid only for scrollable cursors.
	 *
	 * @var    integer
	 * @since  2.0.0-beta
	 */
	public const REL = 5;

	/**
	 * Private constructor to prevent instantiation of this class
	 *
	 * @since   2.0.0-beta
	 */
	private function __construct()
	{
	}
}
