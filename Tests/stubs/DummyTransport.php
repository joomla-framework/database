<?php
/**
 * Part of the Joomla Framework Http Package
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Transport;

/**
 * HTTP transport class for testing purpose only.
 *
 * @since  1.1.4
 */
class DummyTransport
{
	/**
	 * Method to check if HTTP transport DummyTransport is available for use
	 *
	 * @return  boolean  True if available, else false
	 *
	 * @since   1.1.4
	 */
	public static function isSupported()
	{
		return false;
	}
}
