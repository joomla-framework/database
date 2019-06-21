<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mock;

use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;

/**
 * Class to mock JDatabaseQuery.
 *
 * @since  1.0
 */
class Query extends \Joomla\Database\DatabaseQuery
{
	/**
	 * Holds key / value pair of bound objects.
	 *
	 * @var    mixed
	 * @since  __DEPLOY_VERSION__
	 */
	protected $bounded = [];

	/**
	 * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query execution. Also
	 * removes a variable that has been bounded from the internal bounded array when the passed in value is null.
	 *
	 * @param   array|string|integer  $key            The key that will be used in your SQL query to reference the value. Usually of
	 *                                                the form ':key', but can also be an integer.
	 * @param   mixed                 $value          The value that will be bound. It can be an array, in this case it has to be
	 *                                                same length of $key; The value is passed by reference to support output
	 *                                                parameters such as those possible with stored procedures.
	 * @param   array|string          $dataType       Constant corresponding to a SQL datatype. It can be an array, in this case it
	 *                                                has to be same length of $key
	 * @param   integer               $length         The length of the variable. Usually required for OUTPUT parameters.
	 * @param   array                 $driverOptions  Optional driver options to be used.
	 *
	 * @return  $this
	 *
	 * @since   1.5.0
	 */
	public function bind($key = null, &$value = null, $dataType = ParameterType::STRING, $length = 0, $driverOptions = [])
	{
		// Case 1: Empty Key (reset $bounded array)
		if (empty($key))
		{
			$this->bounded = [];

			return $this;
		}

		$key   = (array) $key;
		$count = \count($key);

		if (\is_array($value) && $count != \count($value))
		{
			throw new \InvalidArgumentException('Array length of $key and $value are not equal');
		}

		if (\is_array($dataType) && $count != \count($dataType))
		{
			throw new \InvalidArgumentException('Array length of $key and $dataType are not equal');
		}

		for ($i = 0; $i < $count; $i++)
		{
			if (\is_array($value))
			{
				$localValue = &$value[$i];
			} else {
				$localValue = &$value;
			}

			if (\is_array($dataType))
			{
				$localDataType = $dataType[$i];
			} else {
				$localDataType = $dataType;
			}

			// Case 2: Key Provided, null value (unset key from $bounded array)
			if ($localValue === null)
			{
				if (isset($this->bounded[$key[$i]]))
				{
					unset($this->bounded[$key[$i]]);
				}

				continue;
			}

			// Validate parameter type
			if (!isset($this->parameterMapping[$localDataType]))
			{
				throw new \InvalidArgumentException(sprintf('Unsupported parameter type `%s`', $localDataType));
			}

			$obj                = new \stdClass;
			$obj->value         = &$localValue;
			$obj->dataType      = $this->parameterMapping[$localDataType];
			$obj->length        = $length;
			$obj->driverOptions = $driverOptions;

			// Case 3: Simply add the Key/Value into the bounded array
			$this->bounded[$key[$i]] = $obj;

			unset($localValue);
		}

		return $this;
	}

	/**
	 * Clear data from the query or a specific clause of the query.
	 *
	 * @param   string  $clause  Optionally, the name of the clause to clear, or nothing to clear the whole query.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function clear($clause = null)
	{
		switch ($clause)
		{
			case null:
				$this->bounded = array();
				break;
		}

		return parent::clear($clause);
	}

	/**
	 * Retrieves the bound parameters array when key is null and returns it by reference. If a key is provided then that item is returned.
	 *
	 * @param   mixed  $key  The bounded variable key to retrieve.
	 *
	 * @return  mixed
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function &getBounded($key = null)
	{
		if (empty($key))
		{
			return $this->bounded;
		}

		if (isset($this->bounded[$key]))
		{
			return $this->bounded[$key];
		}
	}

	/**
	 * Method to modify a query already in string format with the needed additions to make the query limited to a particular number of
	 * results, or start at a particular offset.
	 *
	 * @param   string   $query   The query in string format
	 * @param   integer  $limit   The limit for the result set
	 * @param   integer  $offset  The offset for the result set
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function processLimit($query, $limit, $offset = 0)
	{
		if ($limit > 0)
		{
			$query .= ' LIMIT ' . $limit;
		}

		if ($offset > 0)
		{
			$query .= ' OFFSET ' . $offset;
		}

		return $query;
	}
}
