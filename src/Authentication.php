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
 * @since  __DEPLOY_VERSION__
 */
class Authentication
{
	/**
	 * The array of strategies.
	 *
	 * @var    array
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $strategies = array();

	/**
	 * Register a new strategy
	 *
	 * @param   AuthenticationStrategyInterface  $strategy  The authentication strategy object to add.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addStrategy(AuthenticationStrategyInterface $strategy)
	{
		$this->strategies[$strategy->getName()] = $strategy;
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
					$strategyObjects[] = $this->strategies[$strategy];
				}
				else
				{
					throw new RuntimeException('Authentication Strategy Not Found');
				}
			}
		}

		foreach ($strategyObjects AS $strategyObject)
		{
			$username = $strategyObject->authenticate();

			if ($username)
			{
				return $username;
			}
		}

		return false;
	}
}