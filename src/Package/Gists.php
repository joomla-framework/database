<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package;

use Joomla\Github\AbstractPackage;
use Joomla\Http\Exception\UnexpectedResponseException;

/**
 * GitHub API Gists class for the Joomla Framework.
 *
 * @documentation http://developer.github.com/v3/gists
 *
 * @since  1.0
 *
 * @property-read  Gists\Comments  $comments  GitHub API object for gist comments.
 */
class Gists extends AbstractPackage
{
	/**
	 * Create a gist.
	 *
	 * @param   mixed    $files        Either an array of file paths or a single file path as a string.
	 * @param   boolean  $public       True if the gist should be public.
	 * @param   string   $description  The optional description of the gist.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function create($files, $public = false, $description = null)
	{
		// Build the request path.
		$path = '/gists';

		// Build the request data.
		$data = json_encode(
			array(
				'files'       => $this->buildFileData((array) $files),
				'public'      => (bool) $public,
				'description' => $description
			)
		);

		// Send the request.
		return $this->processResponse($this->client->post($this->fetchUrl($path), $data), 201);
	}

	/**
	 * Delete a gist.
	 *
	 * @param   integer  $gistId  The gist number.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function delete($gistId)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId;

		// Send the request.
		$this->processResponse($this->client->delete($this->fetchUrl($path)), 204);
	}

	/**
	 * Edit a gist.
	 *
	 * @param   integer  $gistId       The gist number.
	 * @param   mixed    $files        Either an array of file paths or a single file path as a string.
	 * @param   boolean  $public       True if the gist should be public.
	 * @param   string   $description  The description of the gist.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function edit($gistId, $files = null, $public = null, $description = null)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId;

		// Create the data object.
		$data = new \stdClass;

		// If a description is set add it to the data object.
		if (isset($description))
		{
			$data->description = $description;
		}

		// If the public flag is set add it to the data object.
		if (isset($public))
		{
			$data->public = $public;
		}

		// If a state is set add it to the data object.
		if (isset($files))
		{
			$data->files = $this->buildFileData((array) $files);
		}

		// Encode the request data.
		$data = json_encode($data);

		// Send the request.
		return $this->processResponse($this->client->patch($this->fetchUrl($path), $data));
	}

	/**
	 * Fork a gist.
	 *
	 * @param   integer  $gistId  The gist number.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function fork($gistId)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId . '/forks';

		// Send the request.
		return $this->processResponse($this->client->post($this->fetchUrl($path), ''), 201);
	}

	/**
	 * Get a single gist.
	 *
	 * @param   integer  $gistId  The gist number.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function get($gistId)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId;

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path)));
	}

	/**
	 * List gist commits.
	 *
	 * @param   integer  $gistId  The gist number.
	 * @param   integer  $page    The page number from which to get items.
	 * @param   integer  $limit   The number of items on a page.
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 * @throws  \DomainException
	 */
	public function getCommitList($gistId, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId . '/commits';

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path, $page, $limit)));
	}

	/**
	 * List gist forks.
	 *
	 * @param   integer  $gistId  The gist number.
	 * @param   integer  $page    The page number from which to get items.
	 * @param   integer  $limit   The number of items on a page.
	 *
	 * @return  array
	 *
	 * @since   1.4.0
	 * @throws  \DomainException
	 */
	public function getForkList($gistId, $page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId . '/forks';

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path, $page, $limit)));
	}

	/**
	 * List gists.
	 *
	 * If a user is authenticated it will return the user's gists, otherwise
	 * it will return all public gists.
	 *
	 * @param   integer  $page   The page number from which to get items.
	 * @param   integer  $limit  The number of items on a page.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getList($page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/gists';

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path, $page, $limit)));
	}

	/**
	 * List a user’s gists.
	 *
	 * @param   string     $user   The name of the GitHub user from which to list gists.
	 * @param   integer    $page   The page number from which to get items.
	 * @param   integer    $limit  The number of items on a page.
	 * @param   \DateTime  $since  Only gists updated at or after this time are returned.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getListByUser($user, $page = 0, $limit = 0, \DateTime $since = null)
	{
		// Build the request path.
		$path = '/users/' . $user . '/gists';
		$path .= ($since) ? '?since=' . $since->format(\DateTime::RFC3339) : '';

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path, $page, $limit)));
	}

	/**
	 * List all public gists.
	 *
	 * @param   integer    $page   The page number from which to get items.
	 * @param   integer    $limit  The number of items on a page.
	 * @param   \DateTime  $since  Only gists updated at or after this time are returned.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getListPublic($page = 0, $limit = 0, \DateTime $since = null)
	{
		// Build the request path.
		$path = '/gists/public';
		$path .= ($since) ? '?since=' . $since->format(\DateTime::RFC3339) : '';

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path, $page, $limit)));
	}

	/**
	 * List starred gists.
	 *
	 * @param   integer    $page   The page number from which to get items.
	 * @param   integer    $limit  The number of items on a page.
	 * @param   \DateTime  $since  Only gists updated at or after this time are returned.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getListStarred($page = 0, $limit = 0, \DateTime $since = null)
	{
		// Build the request path.
		$path = '/gists/starred';
		$path .= ($since) ? '?since=' . $since->format(\DateTime::RFC3339) : '';

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path, $page, $limit)));
	}

	/**
	 * Get a specific revision of a gist.
	 *
	 * @param   integer  $gistId  The gist number.
	 * @param   string   $sha     The SHA for the revision to get.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \DomainException
	 */
	public function getRevision($gistId, $sha)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId . '/' . $sha;

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path)));
	}

	/**
	 * Check if a gist is starred.
	 *
	 * @param   integer  $gistId  The gist number.
	 *
	 * @return  boolean  True if gist is starred
	 *
	 * @since   1.0
	 * @throws  UnexpectedResponseException
	 */
	public function isStarred($gistId)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId . '/star';

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		// Validate the response code.
		if ($response->code == 204)
		{
			return true;
		}

		if ($response->code == 404)
		{
			return false;
		}

		// Decode the error response and throw an exception.
		$error = json_decode($response->body);
		$message = isset($error->message) ? $error->message : 'Invalid response received from GitHub.';
		throw new UnexpectedResponseException($response, $message, $response->code);
	}

	/**
	 * Star a gist.
	 *
	 * @param   integer  $gistId  The gist number.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function star($gistId)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId . '/star';

		// Send the request.
		$this->processResponse($this->client->put($this->fetchUrl($path), ''), 204);
	}

	/**
	 * Unstar a gist.
	 *
	 * @param   integer  $gistId  The gist number.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function unstar($gistId)
	{
		// Build the request path.
		$path = '/gists/' . (int) $gistId . '/star';

		// Send the request.
		$this->processResponse($this->client->delete($this->fetchUrl($path)), 204);
	}

	/**
	 * Method to fetch a data array for transmitting to the GitHub API for a list of files based on
	 * an input array of file paths or filename and content pairs.
	 *
	 * @param   array  $files  The list of file paths or filenames and content.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	protected function buildFileData(array $files)
	{
		$data = array();

		foreach ($files as $key => $file)
		{
			if (!is_numeric($key))
			{
				// If the key isn't numeric, then we are dealing with a file whose content has been supplied
				$data[$key] = array('content' => $file);
			}
			elseif (!file_exists($file))
			{
				// Otherwise, we have been given a path and we have to load the content
				// Verify that the each file exists.
				throw new \InvalidArgumentException('The file ' . $file . ' does not exist.');
			}
			else
			{
				$data[basename($file)] = array('content' => file_get_contents($file));
			}
		}

		return $data;
	}
}
