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
 * Joomla Framework Base Model Class
 *
 * @since       1.0
 * @deprecated  2.0  Implement the model interfaces directly; the concrete implementations are provided as traits
 */
class AbstractModel implements ModelInterface
{
	/**
	 * The model state.
	 *
	 * @var    Registry
	 * @since  1.0
	 */
	protected $state;

	/**
	 * Instantiate the model.
	 *
	 * @param   Registry  $state  The model state.
	 *
	 * @since   1.0
	 */
	public function __construct(Registry $state = null)
	{
		$this->state = ($state instanceof Registry) ? $state : new Registry;
	}

	/**
	 * Get the model state.
	 *
	 * @return  Registry  The state object.
	 *
	 * @since   1.0
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Set the model state.
	 *
	 * @param   Registry  $state  The state object.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setState(Registry $state)
	{
		$this->state = $state;
	}
}
