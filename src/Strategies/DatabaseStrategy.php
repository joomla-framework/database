<?php
/**
 * Part of the Joomla Framework Authentication Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication\Strategies;

use Joomla\Authentication\AbstractUsernamePasswordAuthenticationStrategy;
use Joomla\Authentication\Authentication;
use Joomla\Database\DatabaseDriver;
use Joomla\Input\Input;

/**
 * Joomla Framework Database Strategy Authentication class
 *
 * @since  __DEPLOY_VERSION__
 */
class DatabaseStrategy extends AbstractUsernamePasswordAuthenticationStrategy
{
	/**
	 * DatabaseDriver object
	 *
	 * @var    DatabaseDriver
	 * @since  __DEPLOY_VERSION__
	 */
	private $db;

	/**
	 * Database connection options
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	private $dbOptions;

	/**
	 * The Input object
	 *
	 * @var    Input
	 * @since  __DEPLOY_VERSION__
	 */
	private $input;

	/**
	 * Strategy Constructor
	 *
	 * @param   Input           $input     The input object from which to retrieve the request credentials.
	 * @param   DatabaseDriver  $database  DatabaseDriver for retrieving user credentials.
	 * @param   array           $options   Optional options array for configuring the credential storage connection.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(Input $input, DatabaseDriver $database, array $options = array())
	{
		$this->input = $input;
		$this->db    = $database;

		$options['table'] = isset($options['db_table']) ? $options['db_table'] : '#__users';
		$options['username_column'] = isset($options['username_column']) ? $options['username_column'] : 'username';
		$options['password_column'] = isset($options['password_column']) ? $options['password_column'] : 'password';

		$this->dbOptions = $options;
	}

	/**
	 * Attempt to authenticate the username and password pair.
	 *
	 * @return  string|boolean  A string containing a username if authentication is successful, false otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
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

	/**
	 * Retrieve the hashed password for the specified user.
	 *
	 * @param   string  $username  Username to lookup.
	 *
	 * @return  string|boolean  Hashed password on success or boolean false on failure.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getHashedPassword($username)
	{
		$password = $this->db->setQuery(
			$this->db->getQuery(true)
				->select($this->dbOptions['password_column'])
				->from($this->dbOptions['table'])
				->where($this->dbOptions['username_column'] . ' = ' . $this->db->quote($username))
		)->loadResult();

		if (!$password)
		{
			return false;
		}

		return $password;
	}
}
