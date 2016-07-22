<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Activity;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API Activity Feeds class for the Joomla Framework.
 *
 * @documentation https://developer.github.com/v3/activity/feeds/
 *
 * @since  1.4.0
 */
class Feeds extends AbstractPackage
{
	/**
	 * List Feeds.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getFeeds()
	{
		// Build the request path.
		$path = '/feeds';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}
}
