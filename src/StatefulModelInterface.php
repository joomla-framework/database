<?php
/**
 * Part of the Joomla Framework Model Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model;

use Joomla\Registry\Registry;

/**
 * Joomla Framework Stateful Model Interface
 *
 * @since  __DEPLOY_VERSION__
 */
interface StatefulModelInterface
{
	/**
	 * Get the model state.
	 *
	 * @return  Registry  The state object.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getState();
}
