<?php
/**
 * Part of the Joomla Framework Authentication Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication;

/**
 * Joomla Framework AuthenticationStrategy Interface
 *
 * @since  __DEPLOY_VERSION__
 */
interface AuthenticationStrategyInterface
{
	/**
	 * Attempt authentication.
	 *
	 * @return  mixed  A string containing a username if authentication is successful, false otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function authenticate();

	/**
	 * Get strategy name
	 *
	 * @return  string  A string containing the strategy name.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getName();
}