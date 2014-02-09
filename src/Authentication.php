<?php
/**
 * Part of the Joomla Framework Authentication Package
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Authentication;

use RuntimeException;

/**
 * Joomla Framework Authentication Class
 *
 * @since  __DEPLOY_VERSION__
 */
class Authentication
{
	const SUCCESS = 1;

	const INVALID_PASSWORD = 2;

	const NO_SUCH_USER = 3;

	const MISSING_CREDENTIALS = 4;

	/**
	 * The array of strategies.
	 *
	 * @var    array
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $strategies = array();

	/**
	 * The array of strategies.
	 *
	 * @var    array
	 *
	 * @since  __DEPLOY_VERSION__
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
	 * @since   __DEPLOY_VERSION__
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
	 * @return  A string containing a username if authentication is successful, false otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
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
					throw new RuntimeException('Authentication Strategy Not Found');
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
	 * @return  An array containing authentication results.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getResults()
	{
		return $this->results;
	}
}