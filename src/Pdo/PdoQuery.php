<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Pdo;

use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;

/**
 * PDO Query Building Class.
 *
 * @since  1.0
 */
abstract class PdoQuery extends DatabaseQuery
{
	/**
	 * Mapping array for parameter types.
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $parameterMapping = [
		ParameterType::BOOLEAN      => \PDO::PARAM_BOOL,
		ParameterType::INTEGER      => \PDO::PARAM_INT,
		ParameterType::LARGE_OBJECT => \PDO::PARAM_LOB,
		ParameterType::NULL         => \PDO::PARAM_NULL,
		ParameterType::STRING       => \PDO::PARAM_STR,
	];

	/**
	 * The list of zero or null representation of a datetime.
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $nullDatetimeList = ['0000-00-00 00:00:00'];
}
