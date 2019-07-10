<?php
/**
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Tests\Mock;

/**
 * Class to mock JDatabaseQuery.
 *
 * @since  1.0
 */
class Query extends \Joomla\Database\DatabaseQuery
{
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
		return '';
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
