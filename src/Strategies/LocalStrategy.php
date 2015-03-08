<?php
/**
 * Part of the Joomla Framework Authentication Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Strategies;

use Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy;
use Joomla\Authentication\Authentication;
use Joomla\Input\Input;

/**
 * Joomla Framework Local Strategy Authentication class
 *
 * @since  1.0
 */
class LocalStrategy extends AbstractUsernamePasswordAuthenticationStrategy
{
	/**
	 * The Input object
	 *
	 * @var    Input
	 * @since  1.0
	 */
	private $input;

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
		$this->setCredentialStore($credentialStore);
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

		return $this->doAuthenticate($username, $password);
	}
}
