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
 * GitHub API Orgs Members class for the Joomla Framework.
 *
 * @documentation http://developer.github.com/v3/orgs/members/
 *
 * @since  1.0
 */
class Members extends AbstractPackage
{
	/**
	 * Members list.
	 *
	 * List all users who are members of an organization.
	 * A member is a user that belongs to at least 1 team in the organization.
	 * If the authenticated user is also a member of this organization then
	 * both concealed and public members will be returned.
	 * If the requester is not a member of the organization the query will be
	 * redirected to the public members list.
	 *
	 * @param   string  $org  The name of the organization.
	 *
	 * @throws \UnexpectedValueException
	 * @since  1.0
	 *
	 * @return boolean|mixed
	 */
	public function getList($org)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/members';

		$response = $this->client->get($this->fetchUrl($path));

		switch ($response->code)
		{
			case 302 :
				// Requester is not an organization member.
				return false;
				break;

			case 200 :
				return json_decode($response->body);
				break;

			default :
				throw new \UnexpectedValueException('Unexpected response code: ' . $response->code);
				break;
		}
	}

	/**
	 * Check membership.
	 *
	 * Check if a user is, publicly or privately, a member of the organization.
	 *
	 * @param   string  $org   The name of the organization.
	 * @param   string  $user  The name of the user.
	 *
	 * @throws \UnexpectedValueException
	 * @since  1.0
	 *
	 * @return boolean
	 */
	public function check($org, $user)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/members/' . $user;

		$response = $this->client->get($this->fetchUrl($path));

		switch ($response->code)
		{
			case 204 :
				// Requester is an organization member and user is a member.
				return true;
				break;

			case 404 :
				// Requester is an organization member and user is not a member.
				// Requester is not an organization member and is inquiring about themselves.
				return false;
				break;

			case 302 :
				// Requester is not an organization member.
				return false;
				break;

			default :
				throw new \UnexpectedValueException('Unexpected response code: ' . $response->code);
				break;
		}
	}

	/**
	 * Add a member.
	 *
	 * To add someone as a member to an org, you must add them to a team.
	 */

	/**
	 * Remove a member.
	 *
	 * Removing a user from this list will remove them from all teams and they will no longer have
	 * any access to the organization’s repositories.
	 *
	 * @param   string  $org   The name of the organization.
	 * @param   string  $user  The name of the user.
	 *
	 * @since  1.0
	 *
	 * @return object
	 */
	public function remove($org, $user)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/members/' . $user;

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}

	/**
	 * Public members list.
	 *
	 * Members of an organization can choose to have their membership publicized or not.
	 *
	 * @param   string  $org  The name of the organization.
	 *
	 * @since  1.0
	 *
	 * @return object
	 */
	public function getListPublic($org)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/public_members';

		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Check public membership.
	 *
	 * @param   string  $org   The name of the organization.
	 * @param   string  $user  The name of the user.
	 *
	 * @throws \UnexpectedValueException
	 * @since  1.0
	 *
	 * @return boolean
	 */
	public function checkPublic($org, $user)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/public_members/' . $user;

		$response = $this->client->get($this->fetchUrl($path));

		switch ($response->code)
		{
			case 204 :
				// Response if user is a public member.
				return true;
				break;

			case 404 :
				// Response if user is not a public member.
				return false;
				break;

			default :
				throw new \UnexpectedValueException('Unexpected response code: ' . $response->code);
				break;
		}
	}

	/**
	 * Publicize a user’s membership.
	 *
	 * @param   string  $org   The name of the organization.
	 * @param   string  $user  The name of the user.
	 *
	 * @since  1.0
	 *
	 * @return object
	 */
	public function publicize($org, $user)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/public_members/' . $user;

		return $this->processResponse(
			$this->client->put($this->fetchUrl($path), ''),
			204
		);
	}

	/**
	 * Conceal a user’s membership.
	 *
	 * @param   string  $org   The name of the organization.
	 * @param   string  $user  The name of the user.
	 *
	 * @since  1.0
	 *
	 * @return object
	 */
	public function conceal($org, $user)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/public_members/' . $user;

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}

	/**
	 * Get organization membership
	 *
	 * In order to get a user's membership with an organization, the authenticated user must be an organization owner.
	 *
	 * @param   string  $org   The name of the organization.
	 * @param   string  $user  The name of the user.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function getMembership($org, $user)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/memberships/' . $user;

		return $this->processResponse($this->client->get($this->fetchUrl($path)));
	}

	/**
	 * Add or update organization membership
	 *
	 * In order to create or update a user's membership with an organization, the authenticated user must be an organization owner.
	 *
	 * @param   string  $org   The name of the organization.
	 * @param   string  $user  The name of the user.
	 * @param   string  $role  The role to give the user in the organization. Can be either 'member' or 'admin'.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function updateMembership($org, $user, $role = 'member')
	{
		$allowedRoles = array('member', 'admin');

		if (!in_array($role, $allowedRoles))
		{
			throw new \InvalidArgumentException(sprintf("The user's role must be: %s", implode(', ', $allowedRoles)));
		}

		// Build the request path.
		$path = "/orgs/$org/memberships/$user";

		$data = array(
			'role' => $role,
		);

		return $this->processResponse($this->client->put($this->fetchUrl($path), json_encode($data)));
	}

	/**
	 * Remove organization membership
	 *
	 * In order to remove a user's membership with an organization, the authenticated user must be an organization owner.
	 *
	 * @param   string  $org   The name of the organization.
	 * @param   string  $user  The name of the user.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function removeMembership($org, $user)
	{
		// Build the request path.
		$path = '/orgs/' . $org . '/memberships/' . $user;

		return $this->processResponse(
			$this->client->delete($this->fetchUrl($path)),
			204
		);
	}

	/**
	 * List your organization memberships
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function listMemberships()
	{
		// Build the request path.
		$path = '/user/memberships/orgs';

		return $this->processResponse($this->client->get($this->fetchUrl($path)));
	}

	/**
	 * Get your organization membership
	 *
	 * @param   string  $org  The name of the organization.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function listOrganizationMembership($org)
	{
		// Build the request path.
		$path = '/user/memberships/orgs/' . $org;

		return $this->processResponse($this->client->get($this->fetchUrl($path)));
	}

	/**
	 * Edit your organization membership
	 *
	 * @param   string  $org    The name of the organization.
	 * @param   string  $state  The state that the membership should be in.
	 *
	 * @return  object
	 *
	 * @since   1.4.0
	 */
	public function editOrganizationMembership($org, $state)
	{
		// The API only accepts $state == 'active' at present
		if ($state != 'active')
		{
			throw new \InvalidArgumentException('The state must be "active".');
		}

		// Build the request path.
		$path = '/user/memberships/orgs/' . $org;

		return $this->processResponse($this->client->patch($this->fetchUrl($path), array('state' => $state)));
	}
}
