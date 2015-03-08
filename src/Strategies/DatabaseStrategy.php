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
class LocalStrategy extends AbstractUsernamePasswordAuthenticationStrategy
{
	/**
	 * DatabaseDriver object
	 *
	 * @var    DatabaseDriver
	 * @since  __DEPLOY_VERSION__
	 */
	private $db;

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

		$store = $this->createCredentialStore($options);

		if (empty($store))
		{
			throw new \RuntimeException('The credential store is empty.');
		}

		$this->setCredentialStore($store);
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
	 * Creates the credential store.
	 *
	 * @param   array  $options  Options for the database connection
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function createCredentialStore(array $options)
	{
		return $this->db->setQuery(
			$this->db->getQuery(true)
				->select(array($options['username_column'], $options['password_column']))
				->from($options['table'])
		)->loadAssocList($options['username_column'], $options['password_column']);
	}
}
