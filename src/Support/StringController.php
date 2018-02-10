<?php
/**
 * Part of the Joomla Framework Filesystem Package
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
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
	/**
	 * Defines a variable as an array
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @deprecated  2.0  Use `getArray` instead.
	 */
	public function _getArray()
	{
		return $this->getArray();
	}

	/**
	 * Defines a variable as an array
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getArray()
	{
		static $strings = array();

		return $strings;
	}

	/**
	 * Create a reference
	 *
	 * @param   string  $reference  The key
	 * @param   string  $string     The value
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function createRef($reference, &$string)
	{
		$ref = &$this->getArray();
		$ref[$reference] = & $string;
	}

	/**
	 * Get reference
	 *
	 * @param   string  $reference  The key for the reference.
	 *
	 * @return  mixed  False if not set, reference if it exists
	 *
	 * @since   1.0
	 */
	public function getRef($reference)
	{
		$ref = &$this->getArray();

		if (isset($ref[$reference]))
		{
			return $ref[$reference];
		}
		else
		{
			return false;
		}
	}
}
