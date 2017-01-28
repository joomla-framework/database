<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Data;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API References class for the Joomla Framework.
 *
 * @documentation http://developer.github.com/v3/git/refs/
 *
 * @since  1.0
 */
class Refs extends AbstractPackage
{
	/**
	 * Get a Reference.
	 *
	 * @param   string  $user  The name of the owner of the GitHub repository.
	 * @param   string  $repo  The name of the GitHub repository.
	 * @param   string  $ref   The reference to get.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function get($user, $repo, $ref)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/git/refs/' . $ref;

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path)));
	}

	/**
	 * Get all References.
	 *
	 * @param   string   $user       The name of the owner of the GitHub repository.
	 * @param   string   $repo       The name of the GitHub repository.
	 * @param   string   $namespace  Optional sub-namespace to limit the returned references.
	 * @param   integer  $page       Page to request
	 * @param   integer  $limit      Number of results to return per page
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getList($user, $repo, $namespace = '', $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/git/refs';

		if (!empty($namespace))
		{
			$path .= '/' . $namespace;
		}

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path, $page, $limit)));
	}

	/**
	 * Create a Reference.
	 *
	 * @param   string  $user  The name of the owner of the GitHub repository.
	 * @param   string  $repo  The name of the GitHub repository.
	 * @param   string  $ref   The name of the fully qualified reference.
	 * @param   string  $sha   The SHA1 value to set this reference to.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function create($user, $repo, $ref, $sha)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/git/refs';

		// Build the request data.
		$data = json_encode(
			array(
				'ref' => $ref,
				'sha' => $sha
			)
		);

		// Send the request.
		return $this->processResponse($this->client->post($this->fetchUrl($path), $data), 201);
	}

	/**
	 * Update a Reference.
	 *
	 * @param   string   $user   The name of the owner of the GitHub repository.
	 * @param   string   $repo   The name of the GitHub repository.
	 * @param   string   $ref    The reference to update.
	 * @param   string   $sha    The SHA1 value to set the reference to.
	 * @param   boolean  $force  Whether the update should be forced. Default to false.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  DomainException
	 */
	public function edit($user, $repo, $ref, $sha, $force = false)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/git/refs/' . $ref;

		// Create the data object.
		$data = new \stdClass;

		// If a title is set add it to the data object.
		if ($force)
		{
			$data->force = true;
		}

		$data->sha = $sha;

		// Encode the request data.
		$data = json_encode($data);

		// Send the request.
		return $this->processResponse($this->client->patch($this->fetchUrl($path), $data));
	}

	/**
	 * Delete a Reference
	 *
	 * @param   string  $owner  The name of the owner of the GitHub repository.
	 * @param   string  $repo   The name of the GitHub repository.
	 * @param   string  $ref    The reference to update.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function delete($owner, $repo, $ref)
	{
		// Build the request path.
		$path = '/repos/' . $owner . '/' . $repo . '/git/refs/' . $ref;

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}
}
