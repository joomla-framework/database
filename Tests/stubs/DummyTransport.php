<?php
/**
 * Part of the Joomla Framework Http Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Transport;

/**
 * HTTP transport class for testing purpose only.
 *
 * @since  __DEPLOY_VERSION__
 */
class DummyTransport
{
	/**
	 * Method to check if HTTP transport DummyTransport is available for use
	 *
	 * @return  boolean  True if available, else false
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function isSupported()
	{
		return false;
	}
}
