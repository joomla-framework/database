<?php
/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Query;

/**
 * Trait for PostgreSQL Query Building.
 *
 * @since  __DEPLOY_VERSION__
 */
trait PostgresqlQueryBuilder
{
	/**
	 * The FOR UPDATE element used in "FOR UPDATE" lock
	 *
	 * @var    QueryElement
	 * @since  __DEPLOY_VERSION__
	 */
	protected $forUpdate;

	/**
	 * The FOR SHARE element used in "FOR SHARE" lock
	 *
	 * @var    QueryElement
	 * @since  __DEPLOY_VERSION__
	 */
	protected $forShare;

	/**
	 * The NOWAIT element used in "FOR SHARE" and "FOR UPDATE" lock
	 *
	 * @var    QueryElement
	 * @since  __DEPLOY_VERSION__
	 */
	protected $noWait;

	/**
	 * The LIMIT element
	 *
	 * @var    QueryElement
	 * @since  __DEPLOY_VERSION__
	 */
	protected $limit;

	/**
	 * The OFFSET element
	 *
	 * @var    QueryElement
	 * @since  __DEPLOY_VERSION__
	 */
	protected $offset;

	/**
	 * The RETURNING element of INSERT INTO
	 *
	 * @var    QueryElement
	 * @since  __DEPLOY_VERSION__
	 */
	protected $returning;

	/**
	 * Magic function to convert the query to a string, only for PostgreSQL specific queries
	 *
	 * @return  string	The completed query.
	 *
	 * @since   1.0
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

				if ($this->group)
				{
					$query .= (string) $this->group;
				}

				if ($this->having)
				{
					$query .= (string) $this->having;
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

						$joinArray = preg_split('/\sON\s/i', $joinElem[0], 2);

						$this->from($joinArray[0]);

						if (isset($joinArray[1]))
						{
							$this->where($joinArray[1]);
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

				break;
		}

		return $query;
	}

	/**
	 * Clear data from the query or a specific clause of the query.
	 *
	 * @param   string  $clause  Optionally, the name of the clause to clear, or nothing to clear the whole query.
	 *
	 * @return  PostgresqlQuery  Returns this object to allow chaining.
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
			case 'from':
			case 'join':
			case 'set':
			case 'where':
			case 'group':
			case 'having':
			case 'order':
			case 'columns':
			case 'values':
				parent::clear($clause);

				break;

			default:
				$this->bounded   = [];
				$this->type      = null;
				$this->limit     = null;
				$this->offset    = null;
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
	 * Casts a value to a char.
	 *
	 * Ensure that the value is properly quoted before passing to the method.
	 *
	 * Usage:
	 * $query->select($query->castAsChar('a'));
	 *
	 * @param   string  $value  The value to cast as a char.
	 *
	 * @return  string  Returns the cast value.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function castAsChar($value)
	{
		return $value . '::text';
	}

	/**
	 * Concatenates an array of column names or values.
	 *
	 * Usage:
	 * $query->select($query->concatenate(array('a', 'b')));
	 *
	 * @param   array   $values     An array of values to concatenate.
	 * @param   string  $separator  As separator to place between each value.
	 *
	 * @return  string  The concatenated values.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function concatenate($values, $separator = null)
	{
		if ($separator)
		{
			return implode(' || ' . $this->quote($separator) . ' || ', $values);
		}

		return implode(' || ', $values);
	}

	/**
	 * Gets the current date and time.
	 *
	 * @return  string  Return string used in query to obtain
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function currentTimestamp()
	{
		return 'NOW()';
	}

	/**
	 * Sets the FOR UPDATE lock on select's output row
	 *
	 * @param   string  $table_name  The table to lock
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to ',' .
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function forUpdate($table_name, $glue = ',')
	{
		$this->type = 'forUpdate';

		if ($this->forUpdate === null)
		{
			$glue            = strtoupper($glue);
			$this->forUpdate = new QueryElement('FOR UPDATE', 'OF ' . $table_name, "$glue ");
		}
		else
		{
			$this->forUpdate->append($table_name);
		}

		return $this;
	}

	/**
	 * Sets the FOR SHARE lock on select's output row
	 *
	 * @param   string  $table_name  The table to lock
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to ',' .
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function forShare($table_name, $glue = ',')
	{
		$this->type = 'forShare';

		if ($this->forShare === null)
		{
			$glue           = strtoupper($glue);
			$this->forShare = new QueryElement('FOR SHARE', 'OF ' . $table_name, "$glue ");
		}
		else
		{
			$this->forShare->append($table_name);
		}

		return $this;
	}

	/**
	 * Used to get a string to extract year from date column.
	 *
	 * Usage:
	 * $query->select($query->year($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing year to be extracted.
	 *
	 * @return  string  Returns string to extract year from a date.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function year($date)
	{
		return 'EXTRACT (YEAR FROM ' . $date . ')';
	}

	/**
	 * Used to get a string to extract month from date column.
	 *
	 * Usage:
	 * $query->select($query->month($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing month to be extracted.
	 *
	 * @return  string  Returns string to extract month from a date.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function month($date)
	{
		return 'EXTRACT (MONTH FROM ' . $date . ')';
	}

	/**
	 * Used to get a string to extract day from date column.
	 *
	 * Usage:
	 * $query->select($query->day($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing day to be extracted.
	 *
	 * @return  string  Returns string to extract day from a date.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function day($date)
	{
		return 'EXTRACT (DAY FROM ' . $date . ')';
	}

	/**
	 * Used to get a string to extract hour from date column.
	 *
	 * Usage:
	 * $query->select($query->hour($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing hour to be extracted.
	 *
	 * @return  string  Returns string to extract hour from a date.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function hour($date)
	{
		return 'EXTRACT (HOUR FROM ' . $date . ')';
	}

	/**
	 * Used to get a string to extract minute from date column.
	 *
	 * Usage:
	 * $query->select($query->minute($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing minute to be extracted.
	 *
	 * @return  string  Returns string to extract minute from a date.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function minute($date)
	{
		return 'EXTRACT (MINUTE FROM ' . $date . ')';
	}

	/**
	 * Used to get a string to extract seconds from date column.
	 *
	 * Usage:
	 * $query->select($query->second($query->quoteName('dateColumn')));
	 *
	 * @param   string  $date  Date column containing second to be extracted.
	 *
	 * @return  string  Returns string to extract second from a date.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function second($date)
	{
		return 'EXTRACT (SECOND FROM ' . $date . ')';
	}

	/**
	 * Sets the NOWAIT lock on select's output row
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function noWait()
	{
		$this->type = 'noWait';

		if ($this->noWait === null)
		{
			$this->noWait = new QueryElement('NOWAIT', null);
		}

		return $this;
	}

	/**
	 * Set the LIMIT clause to the query
	 *
	 * @param   integer  $limit  Number of rows to return
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function limit($limit = 0)
	{
		if ($this->limit === null)
		{
			$this->limit = new QueryElement('LIMIT', (int) $limit);
		}

		return $this;
	}

	/**
	 * Set the OFFSET clause to the query
	 *
	 * @param   integer  $offset  An integer for skipping rows
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function offset($offset = 0)
	{
		if ($this->offset === null)
		{
			$this->offset = new QueryElement('OFFSET', (int) $offset);
		}

		return $this;
	}

	/**
	 * Add the RETURNING element to INSERT INTO statement.
	 *
	 * @param   mixed  $pkCol  The name of the primary key column.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function returning($pkCol)
	{
		if ($this->returning === null)
		{
			$this->returning = new QueryElement('RETURNING', $pkCol);
		}

		return $this;
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
	 * @since   __DEPLOY_VERSION__
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

	/**
	 * Add to the current date and time.
	 *
	 * Usage:
	 * $query->select($query->dateAdd());
	 *
	 * Prefixing the interval with a - (negative sign) will cause subtraction to be used.
	 *
	 * @param   string  $date      The db quoted string representation of the date to add to
	 * @param   string  $interval  The string representation of the appropriate number of units
	 * @param   string  $datePart  The part of the date to perform the addition on
	 *
	 * @return  string  The string with the appropriate sql for addition of dates
	 *
	 * @since   __DEPLOY_VERSION__
	 * @link    http://www.postgresql.org/docs/9.0/static/functions-datetime.html.
	 */
	public function dateAdd($date, $interval, $datePart)
	{
		if (substr($interval, 0, 1) !== '-')
		{
			return 'timestamp ' . $date . " + interval '" . $interval . ' ' . $datePart . "'";
		}

		return 'timestamp ' . $date . " - interval '" . ltrim($interval, '-') . ' ' . $datePart . "'";
	}

	/**
	 * Get the regular expression operator
	 *
	 * Usage:
	 * $query->where('field ' . $query->regexp($search));
	 *
	 * @param   string  $value  The regex pattern.
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function regexp($value)
	{
		return ' ~* ' . $value;
	}

	/**
	 * Get the function to return a random floating-point value
	 *
	 * Usage:
	 * $query->rand();
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function rand()
	{
		return ' RANDOM() ';
	}

	/**
	 * Find a value in a varchar used like a set.
	 *
	 * Ensure that the value is an integer before passing to the method.
	 *
	 * Usage:
	 * $query->findInSet((int) $parent->id, 'a.assigned_cat_ids')
	 *
	 * @param   string  $value  The value to search for.
	 * @param   string  $set    The set of values.
	 *
	 * @return  string  A representation of the MySQL find_in_set() function for the driver.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function findInSet($value, $set)
	{
		return " $value = ANY (string_to_array($set, ',')::integer[]) ";
	}
}
