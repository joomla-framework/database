<?php
/**
 * Part of the Joomla Framework GitHub Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Repositories;

use Joomla\Github\AbstractPackage;
use Joomla\Http\Exception\UnexpectedResponseException;

/**
 * GitHub API Repositories Commits class for the Joomla Framework.
 *
 * @documentation http://developer.github.com/v3/repos/commits
 *
 * @since  1.0
 */
class Commits extends AbstractPackage
{
	/**
	 * List commits on a repository.
	 *
	 * A special note on pagination: Due to the way Git works, commits are paginated based on SHA
	 * instead of page number.
	 * Please follow the link headers as outlined in the pagination overview instead of constructing
	 * page links yourself.
	 *
	 * @param   string     $user    The name of the owner of the GitHub repository.
	 * @param   string     $repo    The name of the GitHub repository.
	 * @param   string     $sha     Sha or branch to start listing commits from.
	 * @param   string     $path    Only commits containing this file path will be returned.
	 * @param   string     $author  GitHub login, name, or email by which to filter by commit author.
	 * @param   \DateTime  $since   ISO 8601 Date - Only commits after this date will be returned.
	 * @param   \DateTime  $until   ISO 8601 Date - Only commits before this date will be returned.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getList($user, $repo, $sha = '', $path = '', $author = '', \DateTime $since = null, \DateTime $until = null)
	{
		// Build the request path.
		$rPath = '/repos/' . $user . '/' . $repo . '/commits?';

		$rPath .= ($sha) ? '&sha=' . $sha : '';
		$rPath .= ($path) ? '&path=' . $path : '';
		$rPath .= ($author) ? '&author=' . $author : '';
		$rPath .= ($since) ? '&since=' . $since->format(\DateTime::RFC3339) : '';
		$rPath .= ($until) ? '&until=' . $until->format(\DateTime::RFC3339) : '';

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($rPath)));
	}

	/**
	 * Get a single commit.
	 *
	 * @param   string  $user  The name of the owner of the GitHub repository.
	 * @param   string  $repo  The name of the GitHub repository.
	 * @param   string  $sha   The SHA of the commit to retrieve.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function get($user, $repo, $sha)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/commits/' . $sha;

		// Send the request.
		return $this->processResponse($this->client->get($this->fetchUrl($path)));
	}

	/**
	 * Get the SHA-1 of a commit reference.
	 *
	 * @param   string  $user  The name of the owner of the GitHub repository.
	 * @param   string  $repo  The name of the GitHub repository.
	 * @param   string  $ref   The commit reference
	 *
	 * @return  string
	 *
	 * @since   1.4.0
	 * @throws  UnexpectedResponseException
	 */
	public function getSha($user, $repo, $ref)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/commits/' . $ref;

		// Send the request.
		$response = $this->client->get($this->fetchUrl($path));

		// Validate the response code.
		if ($response->code != 200)
		{
			// Decode the error response and throw an exception.
			$error = json_decode($response->body);
			$message = isset($error->message) ? $error->message : 'Invalid response received from GitHub.';
			throw new UnexpectedResponseException($response, $message, $response->code);
		}

		return $response->body;
	}

	/**
	 * Compare two commits.
	 *
	 * @param   string  $user  The name of the owner of the GitHub repository.
	 * @param   string  $repo  The name of the GitHub repository.
	 * @param   string  $base  The base of the diff, either a commit SHA or branch.
	 * @param   string  $head  The head of the diff, either a commit SHA or branch.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function compare($user, $repo, $base, $head)
	{
		// Build the request path.
		$path = '/repos/' . $user . '/' . $repo . '/compare/' . $base . '...' . $head;

		// Send the request.
		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}
}
