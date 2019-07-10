<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Mysqli;

use Joomla\Database\DatabaseQuery;
use Joomla\Database\Query\MysqlQueryBuilder;

/**
 * MySQLi Query Building Class.
 *
 * @since  1.0
 */
class MysqliQuery extends DatabaseQuery
{
	use MysqlQueryBuilder;

	/**
	 * The list of zero or null representation of a datetime.
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $nullDatetimeList = ['0000-00-00 00:00:00', '1000-01-01 00:00:00'];

	/**
	 * Magic function to convert the query to a string.
	 *
	 * @return  string  The completed query.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __toString()
	{
		switch ($this->type)
		{
			case 'select':
				if ($this->selectRowNumber)
				{
					$orderBy      = $this->selectRowNumber['orderBy'];
					$tmpOffset    = $this->offset;
					$tmpLimit     = $this->limit;
					$this->offset = 0;
					$this->limit  = 0;
					$tmpOrder     = $this->order;
					$this->order  = null;
					$query        = parent::__toString();
					$this->order  = $tmpOrder;
					$this->offset = $tmpOffset;
					$this->limit  = $tmpLimit;

					// Add support for second order by, offset and limit
					$query = PHP_EOL . 'SELECT * FROM (' . $query . PHP_EOL . "ORDER BY $orderBy" . PHP_EOL . ') w';

					if ($this->order)
					{
						$query .= (string) $this->order;
					}

					return $this->processLimit($query, $this->limit, $this->offset);
				}
		}

		return parent::__toString();
	}

	/**
	 * Aggregate function to get input values concatenated into a string, separated by delimiter
	 *
	 * Usage:
	 * $query->groupConcat('id', ',');
	 *
	 * @param   string  $column      The name of the column to be concatenated.
	 * @param   string  $separator   The delimiter of each concatenated value
	 *
	 * @return  string  Input values concatenated into a string, separated by delimiter
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function groupConcat($column, $separator = ',')
	{
		return 'GROUP_CONCAT(' . $column . 'SEPARATOR' . $this->quote($separator) . ')';
	}
}
