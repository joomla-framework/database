<?php
/**
 * Part of the Joomla Framework Model Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Model;

use Joomla\Registry\Registry;

/**
 * Trait representing a model holding a state
 *
 * @since  __DEPLOY_VERSION__
 */
trait StatefulModelTrait
{
	/**
	 * The model state.
	 *
	 * @var    Registry
	 * @since  __DEPLOY_VERSION__
	 */
	protected $state;

	/**
	 * Get the model state.
	 *
	 * @return  Registry  The state object.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \UnexpectedValueException
	 */
	public function getState()
	{
		if ($this->state)
		{
			return $this->state;
		}

		throw new \UnexpectedValueException('State not set in ' . __CLASS__);
	}

	/**
	 * Set the model state.
	 *
	 * @param   Registry  $state  The state object.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setState(Registry $state)
	{
		$this->state = $state;
	}
}
