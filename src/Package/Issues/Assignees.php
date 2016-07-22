<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Issues;

use Joomla\Github\AbstractPackage;
use Joomla\Http\Exception\UnexpectedResponseException;

/**
 * GitHub API Assignees class for the Joomla Framework.
 *
 * @documentation http://developer.github.com/v3/issues/assignees/
 *
 * @since  1.0
 */
class Assignees extends AbstractPackage
{
	/**
	 * List assignees.
	 *
	 * This call lists all the available assignees (owner + collaborators) to which issues may be assigned.
	 *
	 * @param   string  $owner  The name of the owner of the GitHub repository.
	 * @param   string  $repo   The name of the GitHub repository.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function getList($owner, $repo)
	{
		// Build the request path.
		$path = '/repos/' . $owner . '/' . $repo . '/assignees';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Check assignee.
	 *
	 * You may check to see if a particular user is an assignee for a repository.
	 * If the given assignee login belongs to an assignee for the repository, a 204 header
	 * with no content is returned.
	 * Otherwise a 404 status code is returned.
	 *
	 * @param   string  $owner     The name of the owner of the GitHub repository.
	 * @param   string  $repo      The name of the GitHub repository.
	 * @param   string  $assignee  The assignees login name.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function check($owner, $repo, $assignee)
	{
		// Build the request path.
		$path = '/repos/' . $owner . '/' . $repo . '/assignees/' . $assignee;

		try
		{
			$response = $this->client->get($this->fetchUrl($path));

			if (204 == $response->code)
			{
				return true;
			}

			throw new UnexpectedResponseException($response, 'Invalid response: ' . $response->code);
		}
		catch (\DomainException $e)
		{
			if (isset($response->code) && 404 == $response->code)
			{
				return false;
			}

			throw $e;
		}
	}

	/**
	 * Add assignees to an Issue
	 *
	 * This call adds the users passed in the assignees key (as their logins) to the issue.
	 *
	 * @param   string    $owner      The name of the owner of the GitHub repository.
	 * @param   string    $repo       The name of the GitHub repository.
	 * @param   integer   $number     The issue number to add assignees to.
	 * @param   string[]  $assignees  The logins for GitHub users to assign to this issue.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \DomainException
	 */
	public function add($owner, $repo, $number, array $assignees)
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/issues/$number/assignees";

		$data = json_encode(
			array(
				'assignees' => $assignees,
			)
		);

		return $this->processResponse($this->client->post($this->fetchUrl($path), $data), 201);
	}

	/**
	 * Remove assignees from an Issue
	 *
	 * This call removes the users passed in the assignees key (as their logins) from the issue.
	 *
	 * @param   string    $owner      The name of the owner of the GitHub repository.
	 * @param   string    $repo       The name of the GitHub repository.
	 * @param   integer   $number     The issue number to add assignees to.
	 * @param   string[]  $assignees  The logins for GitHub users to assign to this issue.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \DomainException
	 */
	public function remove($owner, $repo, $number, array $assignees)
	{
		// Build the request path.
		$path = "/repos/$owner/$repo/issues/$number/assignees";

		$data = json_encode(
			array(
				'assignees' => $assignees,
			)
		);

		return $this->processResponse($this->client->delete($this->fetchUrl($path), array(), null, $data));
	}
}
