<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Pdo;

use Joomla\Database\ParameterType;
use Joomla\Database\StatementInterface;

/**
 * PDO Database Statement.
 *
 * @since  __DEPLOY_VERSION__
 */
class PdoStatement extends \PDOStatement implements StatementInterface
{
	/**
	 * Mapping array for parameter types.
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $parameterTypeMapping = [
		ParameterType::BOOLEAN      => \PDO::PARAM_BOOL,
		ParameterType::INTEGER      => \PDO::PARAM_INT,
		ParameterType::LARGE_OBJECT => \PDO::PARAM_LOB,
		ParameterType::NULL         => \PDO::PARAM_NULL,
		ParameterType::STRING       => \PDO::PARAM_STR,
	];

	/**
	 * Statement constructor
	 *
	 * This class is not instantiated as part of the public API, the PDO internals handle this condition without issue.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function __construct()
	{
	}

	/**
	 * Binds a parameter to the specified variable name.
	 *
	 * @param   string|integer  $parameter      Parameter identifier. For a prepared statement using named placeholders, this will be a parameter
	 *                                          name of the form `:name`. For a prepared statement using question mark placeholders, this will be
	 *                                          the 1-indexed position of the parameter.
	 * @param   mixed           $variable       Name of the PHP variable to bind to the SQL statement parameter.
	 * @param   integer         $dataType       Constant corresponding to a SQL datatype, this should be the processed type from the QueryInterface.
	 * @param   integer         $length         The length of the variable. Usually required for OUTPUT parameters.
	 * @param   array           $driverOptions  Optional driver options to be used.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function bindParam($parameter, &$variable, $dataType = ParameterType::STRING, $length = null, $driverOptions = null)
	{
		// Validate parameter type
		if (!isset($this->parameterTypeMapping[$dataType]))
		{
			throw new \InvalidArgumentException(sprintf('Unsupported parameter type `%s`', $dataType));
		}

		return parent::bindParam($parameter, $variable, $this->parameterTypeMapping[$dataType], $length, $driverOptions);
	}
}
