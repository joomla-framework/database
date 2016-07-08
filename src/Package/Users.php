<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Package;

use Joomla\Github\AbstractPackage;

/**
 * GitHub API References class for the Joomla Framework.
 *
 * @documentation http://developer.github.com/v3/repos/users
 *
 * @since  1.0
 *
 * @property-read  Users\Emails     $emails     GitHub API object for emails.
 * @property-read  Users\Followers  $followers  GitHub API object for followers.
 * @property-read  Users\Keys       $keys       GitHub API object for keys.
 */
class Users extends AbstractPackage
{
	/**
	 * Get a single user.
	 *
	 * @param   string  $user  The users login name.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function get($user)
	{
		// Build the request path.
		$path = '/users/' . $user;

		// Send the request.
		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Get the authenticated user.
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getAuthenticatedUser()
	{
		// Build the request path.
		$path = '/user';

		// Send the request.
		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}

	/**
	 * Update the authenticated user.
	 *
	 * @param   string  $name      The full name
	 * @param   string  $email     The email
	 * @param   string  $blog      The blog
	 * @param   string  $company   The company
	 * @param   string  $location  The location
	 * @param   string  $hireable  If he is unemployed :P
	 * @param   string  $bio       The biometrical DNA fingerprint (or smthng...)
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function edit($name = '', $email = '', $blog = '', $company = '', $location = '', $hireable = '', $bio = '')
	{
		$data = array(
			'name'     => $name,
			'email'    => $email,
			'blog'     => $blog,
			'company'  => $company,
			'location' => $location,
			'hireable' => $hireable,
			'bio'      => $bio
		);

		// Build the request path.
		$path = '/user';

		// Send the request.
		return $this->processResponse(
			$this->client->patch($this->fetchUrl($path), json_encode($data))
		);
	}

	/**
	 * Get all users.
	 *
	 * This provides a dump of every user, in the order that they signed up for GitHub.
	 *
	 * @param   integer  $since  The integer ID of the last User that you’ve seen.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 * @throws  \DomainException
	 */
	public function getList($since = 0)
	{
		// Build the request path.
		$path = '/users';

		$path .= ($since) ? '?since=' . $since : '';

		// Send the request.
		return $this->processResponse(
			$this->client->get($this->fetchUrl($path))
		);
	}
}
