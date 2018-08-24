<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Expression represents a database expression that does not need quoting.
 *
 * @since  __DEPLOY_VERSION__
 */
class Expression
{
	/**
	 * The value of the expression.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $value;

	/**
	 * Constructor.
	 *
	 * @param   string  $value  The database expression.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(string $value)
	{
		$this->value = $value;
	}

	/**
	 * String magic method.
	 *
	 * @return  string  The database expression.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __toString()
	{
		return $this->value;
	}
}
