<?php
/**
 * Part of the Joomla Framework Authentication Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication;

/**
 * Abstract AuthenticationStrategy for username/password based authentication
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractUsernamePasswordAuthenticationStrategy implements AuthenticationStrategyInterface
{
	/**
	 * The credential store.
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	private $credentialStore = array();

	/**
	 * The last authentication status.
	 *
	 * @var    integer
	 * @since  __DEPLOY_VERSION__
	 */
	protected $status;

	/**
	 * Attempt to authenticate the username and password pair.
	 *
	 * @param   string  $username  The username to authenticate.
	 * @param   string  $password  The password to attempt authentication with.
	 *
	 * @return  string|boolean  A string containing a username if authentication is successful, false otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function doAuthenticate($username, $password)
	{
		if (!isset($this->credentialStore[$username]))
		{
			$this->status = Authentication::NO_SUCH_USER;

			return false;
		}

		if (!$this->verifyPassword($username, $password))
		{
			$this->status = Authentication::INVALID_CREDENTIALS;

			return false;
		}

		$this->status = Authentication::SUCCESS;

		return $username;
	}

	/**
	 * Get the credential store.
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getCredentialStore()
	{
		return $this->credentialStore;
	}

	/**
	 * Get the status of the last authentication attempt.
	 *
	 * @return  integer  Authentication class constant result.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getResult()
	{
		return $this->status;
	}

	/**
	 * Set the credential store.
	 *
	 * @param   array  $credentialStore  Associative array with the username as the key and hashed password as the value.
	 *
	 * @return  AbstractAuthenticationStrategy  Method allows chaining
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setCredentialStore(array $credentialStore = array())
	{
		$this->credentialStore = $credentialStore;

		return $this;
	}

	/**
	 * Attempt to verify the username and password pair.
	 *
	 * @param   string  $username  The username to authenticate.
	 * @param   string  $password  The password to attempt authentication with.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function verifyPassword($username, $password)
	{
		$hash = $this->credentialStore[$username];

		return password_verify($password, $hash);
	}
}
