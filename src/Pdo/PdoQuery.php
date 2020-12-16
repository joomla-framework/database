<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Pdo;

use Joomla\Database\DatabaseQuery;

/**
 * PDO Query Building Class.
 *
 * @since  1.0
 */
class PdoQuery extends DatabaseQuery
{
	/**
	 * Casts a value to a char.
	 *
	 * Ensure that the value is properly quoted before passing to the method.
	 *
	 * Usage:
	 * $query->select($query->castAsChar('a'));
	 * $query->select($query->castAsChar('a', 40));
	 *
	 * @param   string  $value  The value to cast as a char.
	 * @param   string  $len    The length of the char.
	 *
	 * @return  string  Returns the cast value.
	 *
	 * @since   1.8.0
	 */
	public function castAsChar($value, $len = null)
	{
		if (!$len)
		{
			return $value;
		}
		else
		{
			return 'CAST(' . $value . ' AS CHAR(' . $len . '))';
		}
	}
}
