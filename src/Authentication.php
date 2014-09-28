<?php
/**
 * Part of the Joomla Framework Authentication Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication;

/**
 * Joomla Framework Authentication Class
 *
 * @since  1.0
 */
class Authentication
{
	/**
	 * Authentication was successful.
	 *
	 * @since  1.0
	 */
	const SUCCESS = 1;

	/**
	 * Credentials were provided but they were invalid.
	 *
	 * @since  1.0
	 */
	const INVALID_CREDENTIALS = 2;

	/**
	 * Credentials were provided but the user did not exist in the credential store.
	 *
	 * @since  1.0
	 */
	const NO_SUCH_USER = 3;

	/**
	 * There were no credentials found.
	 *
	 * @since  1.0
	 */
	const NO_CREDENTIALS = 4;

	/**
	 * There were partial credentials found but they were not complete.
	 *
	 * @since  1.0
	 */
	const INCOMPLETE_CREDENTIALS = 5;

	/**
	 * The array of strategies.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $strategies = array();

	/**
	 * The array of results.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $results = array();

	/**
	 * Register a new strategy
	 *
	 * @param   string                           $strategyName  The name to use for the strategy.
	 * @param   AuthenticationStrategyInterface  $strategy      The authentication strategy object to add.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addStrategy($strategyName, AuthenticationStrategyInterface $strategy)
	{
		$this->strategies[$strategyName] = $strategy;
	}

	/**
	 * Perform authentication
	 *
	 * @param   array  $strategies  Array of strategies to try - empty to try all strategies.
	 *
	 * @return  string|boolean  A string containing a username if authentication is successful, false otherwise.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function authenticate($strategies = array())
	{
		if (empty($strategies))
		{
			$strategyObjects = $this->strategies;
		}
		else
		{
			$strategies = (array) $strategies;

			foreach ($strategies AS $strategy)
			{
				if (isset($this->strategies[$strategy]))
				{
					$strategyObjects[$strategy] = $this->strategies[$strategy];
				}
				else
				{
					throw new \RuntimeException('Authentication Strategy Not Found');
				}
			}
		}

		foreach ($strategyObjects AS $strategy => $strategyObject)
		{
			$username = $strategyObject->authenticate();

			$this->results[$strategy] = $strategyObject->getResult();

			if (is_string($username))
			{
				return $username;
			}
		}

		return false;
	}

	/**
	 * Get authentication results.
	 *
	 * Use this if you want to get more detailed information about the results of an authentication attempts.
	 *
	 * @return  array  An array containing authentication results.
	 *
	 * @since   1.0
	 */
	public function getResults()
	{
		return $this->results;
	}
}
