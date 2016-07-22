<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package\Orgs;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API Orgs Teams class for the Joomla Framework.
 *
 * All actions against teams require at a minimum an authenticated user who is a member
 * of the owner’s team in the :org being managed. Additionally, OAuth users require “user” scope.
 *
 * @documentation http://developer.github.com/v3/orgs/teams/
 *
 * @since  1.0
 */
class Teams extends AbstractPackage
{
	/**
	 * List teams.
	 *
	 * @param   string  $org  The name of the organization.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function getList($org)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/teams';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Get team.
	 *
	 * @param   integer  $id  The team id.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function get($id)
	{
		// Build the request path.
		$path = '/teams/' . (int) $id;

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Create team.
	 *
	 * In order to create a team, the authenticated user must be an owner of the organization.
	 *
	 * @param   string  $org         The name of the organization.
	 * @param   string  $name        The name of the team.
	 * @param   array   $repoNames   Repository names.
	 * @param   string  $permission  The permission. (Deprecated)
	 *                               pull - team members can pull, but not push to or administer these repositories. Default
	 *                               push - team members can pull and push, but not administer these repositories.
	 *                               admin - team members can pull, push and administer these repositories.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException
	 */
	public function create($org, $name, array $repoNames = array(), $permission = '')
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/teams';

		$data = array(
			'name' => $name
		);

		if ($repoNames)
		{
			$data['repo_names'] = $repoNames;
		}

		if ($permission)
		{
			if (false == in_array($permission, array('pull', 'push', 'admin')))
			{
				throw new \UnexpectedValueException('Permissions must be either "pull", "push", or "admin".');
			}

			$data['permission'] = $permission;
		}

		return $this->processResponse(
			$this->client->post($this->fetchUrl($path), $data),
			201
		);
	}

	/**
	 * Edit team.
	 *
	 * In order to edit a team, the authenticated user must be an owner of the org that the team is associated with.
	 *
	 * @param   integer  $id          The team id.
	 * @param   string   $name        The name of the team.
	 * @param   string   $permission  The permission. (Deprecated)
	 *                                pull - team members can pull, but not push to or administer these repositories. Default
	 *                                push - team members can pull and push, but not administer these repositories.
	 *                                admin - team members can pull, push and administer these repositories.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException
	 */
	public function edit($id, $name, $permission = '')
	{
		// Build the request path.
		$path = '/teams/' . (int) $id;

		$data = array(
			'name' => $name
		);

		if ($permission)
		{
			if (false == in_array($permission, array('pull', 'push', 'admin')))
			{
				throw new \UnexpectedValueException('Permissions must be either "pull", "push", or "admin".');
			}

			$data['permission'] = $permission;
		}

		return $this->processResponse(
			$this->client->patch($this->fetchUrl($path), $data)
		);
	}

	/**
	 * Delete team.
	 *
	 * In order to delete a team, the authenticated user must be an owner of the org that the team is associated with.
	 *
	 * @param   integer  $id  The team id.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function delete($id)
	{
		// Build the request path.
		$path = '/teams/' . $id;

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}

	/**
	 * List team members.
	 *
	 * In order to list members in a team, the authenticated user must be a member of the team.
	 *
	 * @param   integer  $id  The team id.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function getListMembers($id)
	{
		// Build the request path.
		$path = '/teams/' . $id . '/members';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Get team member.
	 *
	 * In order to get if a user is a member of a team, the authenticated user must be a member of the team.
	 *
	 * @param   integer  $id    The team id.
	 * @param   string   $user  The name of the user.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException
	 * @deprecated  Use getTeamMembership() instead
	 */
	public function isMember($id, $user)
	{
		// Build the request path.
		$path = '/teams/' . $id . '/members/' . $user;

		$response = $this->client->get($this->fetchUrl($path));

		switch ($response->code)
		{
			case 204 :
				// Response if user is a member
				return true;
				break;

			case 404 :
				// Response if user is not a member
				return false;
				break;

			default :
				throw new \UnexpectedValueException('Unexpected response code: ' . $response->code);
				break;
		}
	}

	/**
	 * Add team member.
	 *
	 * In order to add a user to a team, the authenticated user must have ‘admin’ permissions
	 * to the team or be an owner of the org that the team is associated with.
	 *
	 * @param   integer  $id    The team id.
	 * @param   string   $user  The name of the user.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @deprecated  Use addTeamMembership() instead
	 */
	public function addMember($id, $user)
	{
		// Build the request path.
		$path = '/teams/' . $id . '/members/' . $user;

		return $this->processResponse(
			$this->client->put($this->fetchUrl($path), ''),
			204
		);
	}

	/**
	 * Remove team member.
	 *
	 * In order to remove a user from a team, the authenticated user must have ‘admin’ permissions
	 * to the team or be an owner of the org that the team is associated with.
	 * NOTE: This does not delete the user, it just remove them from the team.
	 *
	 * @param   integer  $id    The team id.
	 * @param   string   $user  The name of the user.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @deprecated  Use removeTeamMembership() instead
	 */
	public function removeMember($id, $user)
	{
		// Build the request path.
		$path = '/teams/' . $id . '/members/' . $user;

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}

	/**
	 * Get team membership
	 *
	 * In order to get a user's membership with a team, the team must be visible to the authenticated user.
	 *
	 * @param   integer  $id    The team id.
	 * @param   string   $user  The name of the user.
	 *
	 * @return  string|boolean  The state the user's membership is in or boolean false if the user is not a member.
	 *
	 * @since   1.4.0
	 * @throws  \UnexpectedValueException
	 */
	public function getTeamMembership($id, $user)
	{
		// Build the request path.
		$path = "/teams/$id/memberships/$user";

		$response = $this->client->get($this->fetchUrl($path));

		switch ($response->code)
		{
			case 200 :
				// Response if user is an active member or pending membership
				$body = json_decode($response->body);

				return $body->state;

			case 404 :
				// Response if user is not a member
				return false;

			default :
				throw new \UnexpectedValueException('Unexpected response code: ' . $response->code);
		}
	}

	/**
	 * Add team membership
	 *
	 * If the user is already a member of the team's organization, this endpoint will add the user to the team.
	 * In order to add a membership between an organization member and a team, the authenticated user must be
	 * an organization owner or a maintainer of the team.
	 *
	 * @param   integer  $id    The team id.
	 * @param   string   $user  The name of the user.
	 * @param   string   $role  The role the user should have on the team. Can be either 'member' or 'maintainer'.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \UnexpectedValueException
	 */
	public function addTeamMembership($id, $user, $role = 'member')
	{
		// Build the request path.
		$path = "/teams/$id/memberships/$user";

		if (false == in_array($role, array('member', 'maintainer')))
		{
			throw new \UnexpectedValueException('Roles must be either "member" or "maintainer".');
		}

		$data = array(
			'role' => $role,
		);

		return $this->processResponse($this->client->put($this->fetchUrl($path), $data));
	}

	/**
	 * Remove team membership
	 *
	 * In order to remove a membership between a user and a team, the authenticated user must have 'admin' permissions to the team
	 * or be an owner of the organization that the team is associated with.
	 * NOTE: This does not delete the user, it just removes their membership from the team.
	 *
	 * @param   integer  $id    The team id.
	 * @param   string   $user  The name of the user.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 * @throws  \UnexpectedValueException
	 */
	public function removeTeamMembership($id, $user)
	{
		// Build the request path.
		$path = "/teams/$id/memberships/$user";

		return $this->processResponse($this->client->delete($this->fetchUrl($path)), 204);
	}

	/**
	 * List team repos.
	 *
	 * @param   integer  $id  The team id.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function getListRepos($id)
	{
		// Build the request path.
		$path = '/teams/' . $id . '/repos';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Check if a team manages a repository.
	 *
	 * @param   integer  $id     The team id.
	 * @param   string   $owner  The owner of the GitHub repository.
	 * @param   string   $repo   The name of the GitHub repository.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException
	 */
	public function checkRepo($id, $owner, $repo)
	{
		// Build the request path.
		$path = '/teams/' . $id . '/repos/' . $owner . '/' . $repo;

		$response = $this->client->get($this->fetchUrl($path));

		switch ($response->code)
		{
			case 204 :
				// Response if repo is managed by this team.
				return true;
				break;

			case 404 :
				// Response if repo is not managed by this team.
				return false;
				break;

			default :
				throw new \UnexpectedValueException('Unexpected response code: ' . $response->code);
				break;
		}
	}

	/**
	 * Add or update team repository.
	 *
	 * In order to add a repo to a team, the authenticated user must be an owner of the
	 * org that the team is associated with. Also, the repo must be owned by the organization,
	 * or a direct form of a repo owned by the organization.
	 *
	 * If you attempt to add a repo to a team that is not owned by the organization, you get:
	 * Status: 422 Unprocessable Entity
	 *
	 * @param   integer  $id    The team id.
	 * @param   string   $org   The name of the organization of the GitHub repository.
	 * @param   string   $repo  The name of the GitHub repository.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function addRepo($id, $org, $repo)
	{
		// Build the request path.
		$path = '/teams/' . $id . '/repos/' . $org . '/' . $repo;

		return $this->processResponse(
			$this->client->put($this->fetchUrl($path), ''),
			204
		);
	}

	/**
	 * Remove team repository.
	 *
	 * In order to remove a repo from a team, the authenticated user must be an owner
	 * of the org that the team is associated with. NOTE: This does not delete the
	 * repo, it just removes it from the team.
	 *
	 * @param   integer  $id     The team id.
	 * @param   string   $owner  The name of the owner of the GitHub repository.
	 * @param   string   $repo   The name of the GitHub repository.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function removeRepo($id, $owner, $repo)
	{
		// Build the request path.
		$path = '/teams/' . (int) $id . '/repos/' . $owner . '/' . $repo;

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}

	/**
	 * List user teams.
	 *
	 * List all of the teams across all of the organizations to which the authenticated user belongs.
	 * This method requires user, repo, or read:org scope when authenticating via OAuth.
	 *
	 * @param   integer  $page   The page number from which to get items.
	 * @param   integer  $limit  The number of items on a page.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getUserTeams($page = 0, $limit = 0)
	{
		// Build the request path.
		$path = '/user/teams';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path, $page, $limit))
		);
	}
}
