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
 * GitHub API Repositories Pages class for the Joomla Framework.
 *
 * @documentation https://developer.github.com/v3/repos/pages/
 *
 * @since  1.4.0
 */
class Pages extends AbstractPackage
{
	/**
	 * Get information about a Pages site.
	 *
	 * @param   string  $owner  The name of the owner of the GitHub repository.
	 * @param   string  $repo   The name of the GitHub repository.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getInfo($owner, $repo)
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/pages";

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * List Pages builds.
	 *
	 * @param   string   $owner  The name of the owner of the GitHub repository.
	 * @param   string   $repo   The name of the GitHub repository.
	 * @param   integer  $page   The page number from which to get items.
	 * @param   integer  $limit  The number of items on a page.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getList($owner, $repo, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/pages/builds";

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path, $page, $limit))
		);
	}

	/**
	 * List latest Pages build.
	 *
	 * @param   string  $owner  The name of the owner of the GitHub repository.
	 * @param   string  $repo   The name of the GitHub repository.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getLatest($owner, $repo)
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/pages/builds/latest";

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}
}
