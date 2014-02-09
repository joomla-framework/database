<?php
/**
 * Part of the Joomla Framework Authentication Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Strategies;

use Joomla\Authentication\AuthenticationStrategyInterface;
use Joomla\Authentication\Authentication;
use Joomla\Input\Input;

class LocalStrategy implements AuthenticationStrategyInterface
{
	/**
	 * The Input object
	 *
	 * @var    Joomla\Input\Input  $input  The input object from which to retrieve the username and password.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $input;

	/**
	 * The credential store.
	 *
	 * @var    array  $credentialStore  An array of username/hash pairs.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $credentialStore;

	/**
	 * The last authentication status.
	 *
	 * @var    int  $status  The last status result (use constants from Authentication)
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $status;

	/**
	 * Strategy Constructor
	 *
	 * @param   Joomla\Input\Input  $input            The input object from which to retrieve the request credentials.
	 * @param   array               $credentialStore  Hash of username and hash pairs.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Input $input, $credentialStore)
	{
		$this->input = $input;

		$this->credentialStore = $credentialStore;
	}

	/**
	 * Attempt to authenticate the username and password pair.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function authenticate()
	{
		$username = $this->input->get('username', false);
		$password = $this->input->get('password', false);

		if (!$username || !$password)
		{
			$this->status = Authentication::MISSING_CREDENTIALS;

			return false;
		}

		if (isset($this->credentialStore[$username]))
		{
			$hash = $this->credentialStore[$username];
		}
		else
		{
			$this->status = Authentication::NO_SUCH_USER;

			return false;
		}

		if (\password_verify($password, $hash))
		{
			$this->status = Authentication::SUCCESS;

			return $username;
		}
		else
		{
			$this->status = Authentication::INVALID_PASSWORD;

			return false;
		}
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
}