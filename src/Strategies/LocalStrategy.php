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

/**
 * Joomla Framework Local Strategy Authentication class
 *
 * @since  1.0
 */
class LocalStrategy implements AuthenticationStrategyInterface
{
	/**
	 * The Input object
	 *
	 * @var    Input
	 * @since  1.0
	 */
	private $input;

	/**
	 * The credential store.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $credentialStore;

	/**
	 * The last authentication status.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	private $status;

	/**
	 * Strategy Constructor
	 *
	 * @param   Input  $input            The input object from which to retrieve the request credentials.
	 * @param   array  $credentialStore  Hash of username and hash pairs.
	 *
	 * @since   1.0
	 */
	public function __construct(Input $input, $credentialStore)
	{
		$this->input = $input;
		$this->credentialStore = $credentialStore;
	}

	/**
	 * Attempt to authenticate the username and password pair.
	 *
	 * @return  string|boolean  A string containing a username if authentication is successful, false otherwise.
	 *
	 * @since   1.0
	 */
	public function authenticate()
	{
		$username = $this->input->get('username', false);
		$password = $this->input->get('password', false);

		if (!$username || !$password)
		{
			$this->status = Authentication::NO_CREDENTIALS;

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

		if (password_verify($password, $hash))
		{
			$this->status = Authentication::SUCCESS;

			return $username;
		}
		else
		{
			$this->status = Authentication::INVALID_CREDENTIALS;

			return false;
		}
	}

	/**
	 * Get the status of the last authentication attempt.
	 *
	 * @return  integer  Authentication class constant result.
	 *
	 * @since   1.0
	 */
	public function getResult()
	{
		return $this->status;
	}
}
