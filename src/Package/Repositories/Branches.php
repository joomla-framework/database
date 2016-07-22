<?php
/**
 * Part of the Joomla Framework GitHub Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Repositories;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API Repositories Branches class for the Joomla Framework.
 *
 * @documentation https://developer.github.com/v3/repos/branches
 *
 * @since  1.4.0
 */
class Branches extends AbstractPackage
{
	/**
	 * List Branches.
	 *
	 * @param   string  $owner  Repository owner.
	 * @param   string  $repo   Repository name.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getList($owner, $repo)
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/branches";

		// Send the request.
		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Get Branch.
	 *
	 * @param   string  $owner   Repository owner.
	 * @param   string  $repo    Repository name.
	 * @param   string  $branch  Branch name.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function get($owner, $repo, $branch)
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/branches/$branch";

		// Send the request.
		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}
}
