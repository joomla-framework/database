<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

/**
 * Class defining the parameter types for prepared statements
 *
 * @since  2.0.0-beta
 */
final class ParameterType
{
	/**
	 * Defines a boolean parameter
	 *
	 * @var    string
	 * @since  2.0.0-beta
	 */
	public const BOOLEAN = 'boolean';

	/**
	 * Defines an integer parameter
	 *
	 * @var    string
	 * @since  2.0.0-beta
	 */
	public const INTEGER = 'int';

	/**
	 * Defines a large object parameter
	 *
	 * @var    string
	 * @since  2.0.0-beta
	 */
	public const LARGE_OBJECT = 'lob';

	/**
	 * Defines a null parameter
	 *
	 * @var    string
	 * @since  2.0.0-beta
	 */
	public const NULL = 'null';

	/**
	 * Defines a string parameter
	 *
	 * @var    string
	 * @since  2.0.0-beta
	 */
	public const STRING = 'string';

	/**
	 * Private constructor to prevent instantiation of this class
	 *
	 * @since   2.0.0-beta
	 */
	private function __construct()
	{
	}
}
