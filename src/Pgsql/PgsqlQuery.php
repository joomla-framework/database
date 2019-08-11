<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Pgsql;

use Joomla\Database\Pdo\PdoQuery;
use Joomla\Database\Query\PostgresqlQueryBuilder;

/**
 * PDO PostgreSQL Query Building Class.
 *
 * @since  1.0
 */
class PgsqlQuery extends PdoQuery
{
	use PostgresqlQueryBuilder;

	/**
	 * The list of zero or null representation of a datetime.
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $nullDatetimeList = ['1970-01-01 00:00:00'];

	/**
	 * Magic function to convert the query to a string, only for PostgreSQL specific queries
	 *
	 * @return  string	The completed query.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __toString()
	{
		$query = '';

		switch ($this->type)
		{
			case 'select':
				$query .= (string) $this->select;
				$query .= (string) $this->from;

				if ($this->join)
				{
					// Special case for joins
					foreach ($this->join as $join)
					{
						$query .= (string) $join;
					}
				}

				if ($this->where)
				{
					$query .= (string) $this->where;
				}

				if ($this->selectRowNumber)
				{
					if ($this->order)
					{
						$query .= (string) $this->order;
					}

					break;
				}

				if ($this->group)
				{
					$query .= (string) $this->group;
				}

				if ($this->having)
				{
					$query .= (string) $this->having;
				}

				if ($this->merge)
				{
					// Special case for merge
					foreach ($this->merge as $element)
					{
						$query .= (string) $element;
					}
				}

				if ($this->order)
				{
					$query .= (string) $this->order;
				}

				if ($this->forUpdate)
				{
					$query .= (string) $this->forUpdate;
				}
				else
				{
					if ($this->forShare)
					{
						$query .= (string) $this->forShare;
					}
				}

				if ($this->noWait)
				{
					$query .= (string) $this->noWait;
				}

				$query = $this->processLimit($query, $this->limit, $this->offset);

				break;

			case 'update':
				$query .= (string) $this->update;
				$query .= (string) $this->set;

				if ($this->join)
				{
					$tmpFrom     = $this->from;
					$tmpWhere    = $this->where ? clone $this->where : null;
					$this->from  = null;

					// Workaround for special case of JOIN with UPDATE
					foreach ($this->join as $join)
					{
						$joinElem = $join->getElements();

						$this->from($joinElem[0]);

						if (isset($joinElem[1]))
						{
							$this->where($joinElem[1]);
						}
					}

					$query .= (string) $this->from;

					if ($this->where)
					{
						$query .= (string) $this->where;
					}

					$this->from  = $tmpFrom;
					$this->where = $tmpWhere;
				}
				elseif ($this->where)
				{
					$query .= (string) $this->where;
				}

				$query = $this->processLimit($query, $this->limit, $this->offset);

				break;

			case 'insert':
				$query .= (string) $this->insert;

				if ($this->values)
				{
					if ($this->columns)
					{
						$query .= (string) $this->columns;
					}

					$elements = $this->values->getElements();

					if (!($elements[0] instanceof $this))
					{
						$query .= ' VALUES ';
					}

					$query .= (string) $this->values;

					if ($this->returning)
					{
						$query .= (string) $this->returning;
					}
				}

				$query = $this->processLimit($query, $this->limit, $this->offset);

				break;

			default:
				$query = parent::__toString();
		}

		if ($this->type === 'select' && $this->alias !== null)
		{
			$query = '(' . $query . ') AS ' . $this->alias;
		}

		return $query;
	}

	/**
	 * Clear data from the query or a specific clause of the query.
	 *
	 * @param   string  $clause  Optionally, the name of the clause to clear, or nothing to clear the whole query.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function clear($clause = null)
	{
		switch ($clause)
		{
			case 'limit':
				$this->limit = null;

				break;

			case 'offset':
				$this->offset = null;

				break;

			case 'forUpdate':
				$this->forUpdate = null;

				break;

			case 'forShare':
				$this->forShare = null;

				break;

			case 'noWait':
				$this->noWait = null;

				break;

			case 'returning':
				$this->returning = null;

				break;

			case 'select':
			case 'update':
			case 'delete':
			case 'insert':
			case 'querySet':
			case 'from':
			case 'join':
			case 'set':
			case 'where':
			case 'group':
			case 'having':
			case 'merge':
			case 'order':
			case 'columns':
			case 'values':
				parent::clear($clause);

				break;

			default:
				$this->forUpdate = null;
				$this->forShare  = null;
				$this->noWait    = null;
				$this->returning = null;

				parent::clear($clause);

				break;
		}

		return $this;
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
		return 'string_agg(' . $column . '::text, ' . $this->quote($separator) . ')';
	}
}
