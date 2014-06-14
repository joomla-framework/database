<?php
/**
 * Part of the Joomla Framework Filesystem Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Filesystem\Support;

/**
 * String Controller
 *
 * @since  1.0
 */
class StringController
{
	private static $strings = array();

	/**
	 * Defines a variable as an array
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public static function _getArray()
	{
		return self::$strings;
	}

	/**
	 * Create a reference
	 *
	 * @param   string  $reference  The key
	 * @param   string  &$string    The value
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function createRef($reference, &$string)
	{
		self::$strings[$reference] = & $string;
	}

	/**
	 * Get reference
	 *
	 * @param   string  $reference  The key for the reference.
	 *
	 * @return  mixed  False if not set, reference if it it exists
	 *
	 * @since   1.0
	 */
	public static function getRef($reference)
	{
		if (isset(self::$strings[$reference]))
		{
			return self::$strings[$reference];
		}
		else
		{
			return false;
		}
	}
}
