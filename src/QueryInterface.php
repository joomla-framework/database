<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database;

use Joomla\Database\Exception\QueryTypeAlreadyDefinedException;
use Joomla\Database\Query\LimitableInterface;
use Joomla\Database\Query\PreparableInterface;
use Joomla\Database\Exception\UnknownTypeException;

/**
 * Joomla Framework Query Building Interface.
 *
 * @since  2.0.0
 */
interface QueryInterface extends PreparableInterface, LimitableInterface
{
    /**
     * Convert the query object to a string.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function __toString();

    /**
     * Add a single column, or array of columns to the CALL clause of the query.
     *
     * Usage:
     * $query->call('a.*')->call('b.id');
     * $query->call(array('a.*', 'b.id'));
     *
     * @param   array|string  $columns  A string or an array of field names.
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  QueryTypeAlreadyDefinedException if the query type has already been defined
     */
    public function call($columns);

    /**
     * Casts a value to a specified type.
     *
     * Ensure that the value is properly quoted before passing to the method.
     *
     * Usage:
     * $query->select($query->castAs('CHAR', 'a'));
     *
     * @param   string  $type    The type of string to cast as.
     * @param   string  $value   The value to cast as a char.
     * @param   string  $length  Optionally specify the length of the field (if the type supports it otherwise
     *                           ignored).
     *
     * @return  string  SQL statement to cast the value as a char type.
     *
     * @since   2.0.0
     * @throws  UnknownTypeException  When unsupported cast for a database driver
     */
    public function castAs(string $type, string $value, ?string $length = null);

    /**
     * Gets the number of characters in a string.
     *
     * Note, use 'length' to find the number of bytes in a string.
     *
     * Usage:
     * $query->select($query->charLength('a'));
     *
     * @param   string       $field      A value.
     * @param   string|null  $operator   Comparison operator between charLength integer value and $condition
     * @param   string|null  $condition  Integer value to compare charLength with.
     *
     * @return  string  SQL statement to get the length of a character.
     *
     * @since   2.0.0
     */
    public function charLength($field, $operator = null, $condition = null);

    /**
     * Clear data from the query or a specific clause of the query.
     *
     * @param   string  $clause  Optionally, the name of the clause to clear, or nothing to clear the whole query.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function clear($clause = null);

    /**
     * Adds a column, or array of column names that would be used for an INSERT INTO statement.
     *
     * @param   array|string  $columns  A column name, or array of column names.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function columns($columns);

    /**
     * Concatenates an array of column names or values.
     *
     * Usage:
     * $query->select($query->concatenate(array('a', 'b')));
     *
     * @param   string[]     $values     An array of values to concatenate.
     * @param   string|null  $separator  As separator to place between each value.
     *
     * @return  string  SQL statement representing the concatenated values.
     *
     * @since   2.0.0
     */
    public function concatenate($values, $separator = null);

    /**
     * Gets the current date and time.
     *
     * Usage:
     * $query->where('published_up < '.$query->currentTimestamp());
     *
     * @return  string  SQL statement to get the current timestamp.
     *
     * @since   2.0.0
     */
    public function currentTimestamp();

    /**
     * Add a table name to the DELETE clause of the query.
     *
     * Usage:
     * $query->delete('#__a')->where('id = 1');
     *
     * @param   string  $table  The name of the table to delete from.
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  QueryTypeAlreadyDefinedException if the query type has already been defined
     */
    public function delete($table = null);

    /**
     * Add a single column, or array of columns to the EXEC clause of the query.
     *
     * Usage:
     * $query->exec('a.*')->exec('b.id');
     * $query->exec(array('a.*', 'b.id'));
     *
     * @param   array|string  $columns  A string or an array of field names.
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  QueryTypeAlreadyDefinedException if the query type has already been defined
     */
    public function exec($columns);

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
     * @since   2.0.0
     */
    public function findInSet($value, $set);

    /**
     * Add a table to the FROM clause of the query.
     *
     * Usage:
     * $query->select('*')->from('#__a');
     * $query->select('*')->from($subquery->alias('a'));
     *
     * @param   string|QueryInterface  $table  The name of the table or a QueryInterface object (or a child of it) with alias set.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function from($table);

    /**
     * Add alias for current query.
     *
     * Usage:
     * $query->select('*')->from('#__a')->alias('subquery');
     *
     * @param   string  $alias  Alias used for a JDatabaseQuery.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function alias($alias);

    /**
     * Used to get a string to extract year from date column.
     *
     * Usage:
     * $query->select($query->year($query->quoteName('dateColumn')));
     *
     * @param   string  $date  Date column containing year to be extracted.
     *
     * @return  string  SQL statement to get the year from a date value.
     *
     * @since   2.0.0
     */
    public function year($date);

    /**
     * Used to get a string to extract month from date column.
     *
     * Usage:
     * $query->select($query->month($query->quoteName('dateColumn')));
     *
     * @param   string  $date  Date column containing month to be extracted.
     *
     * @return  string  SQL statement to get the month from a date value.
     *
     * @since   2.0.0
     */
    public function month($date);

    /**
     * Used to get a string to extract day from date column.
     *
     * Usage:
     * $query->select($query->day($query->quoteName('dateColumn')));
     *
     * @param   string  $date  Date column containing day to be extracted.
     *
     * @return  string  SQL statement to get the day from a date value.
     *
     * @since   2.0.0
     */
    public function day($date);

    /**
     * Used to get a string to extract hour from date column.
     *
     * Usage:
     * $query->select($query->hour($query->quoteName('dateColumn')));
     *
     * @param   string  $date  Date column containing hour to be extracted.
     *
     * @return  string  SQL statement to get the hour from a date/time value.
     *
     * @since   2.0.0
     */
    public function hour($date);

    /**
     * Used to get a string to extract minute from date column.
     *
     * Usage:
     * $query->select($query->minute($query->quoteName('dateColumn')));
     *
     * @param   string  $date  Date column containing minute to be extracted.
     *
     * @return  string  SQL statement to get the minute from a date/time value.
     *
     * @since   2.0.0
     */
    public function minute($date);

    /**
     * Used to get a string to extract seconds from date column.
     *
     * Usage:
     * $query->select($query->second($query->quoteName('dateColumn')));
     *
     * @param   string  $date  Date column containing second to be extracted.
     *
     * @return  string  SQL statement to get the second from a date/time value.
     *
     * @since   2.0.0
     */
    public function second($date);

    /**
     * Add a grouping column to the GROUP clause of the query.
     *
     * Usage:
     * $query->group('id');
     *
     * @param   array|string  $columns  A string or array of ordering columns.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function group($columns);

    /**
     * Aggregate function to get input values concatenated into a string, separated by delimiter
     *
     * Usage:
     * $query->groupConcat('id', ',');
     *
     * @param   string  $expression  The expression to apply concatenation to, this may be a column name or complex SQL statement.
     * @param   string  $separator   The delimiter of each concatenated value
     *
     * @return  string  Input values concatenated into a string, separated by delimiter
     *
     * @since   2.0.0
     */
    public function groupConcat($expression, $separator = ',');

    /**
     * A conditions to the HAVING clause of the query.
     *
     * Usage:
     * $query->group('id')->having('COUNT(id) > 5');
     *
     * @param   array|string  $conditions  A string or array of columns.
     * @param   string        $glue        The glue by which to join the conditions. Defaults to AND.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function having($conditions, $glue = 'AND');

    /**
     * Add a table name to the INSERT clause of the query.
     *
     * Usage:
     * $query->insert('#__a')->set('id = 1');
     * $query->insert('#__a')->columns('id, title')->values('1,2')->values('3,4');
     * $query->insert('#__a')->columns('id, title')->values(array('1,2', '3,4'));
     *
     * @param   string   $table           The name of the table to insert data into.
     * @param   boolean  $incrementField  The name of the field to auto increment.
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  QueryTypeAlreadyDefinedException if the query type has already been defined
     */
    public function insert($table, $incrementField = false);

    /**
     * Add a JOIN clause to the query.
     *
     * Usage:
     * $query->join('INNER', 'b', 'b.id = a.id);
     *
     * @param   string  $type       The type of join. This string is prepended to the JOIN keyword.
     * @param   string  $table      The name of table.
     * @param   string  $condition  The join condition.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function join($type, $table, $condition = null);

    /**
     * Get the length of a string in bytes.
     *
     * Note, use 'charLength' to find the number of characters in a string.
     *
     * Usage:
     * query->where($query->length('a').' > 3');
     *
     * @param   string  $value  The string to measure.
     *
     * @return  integer
     *
     * @since   2.0.0
     */
    public function length($value);

    /**
     * Get the null or zero representation of a timestamp for the database driver.
     *
     * This method is provided for use where the query object is passed to a function for modification.
     * If you have direct access to the database object, it is recommended you use the nullDate method directly.
     *
     * Usage:
     * $query->where('modified_date <> '.$query->nullDate());
     *
     * @param   boolean  $quoted  Optionally wraps the null date in database quotes (true by default).
     *
     * @return  string  Null or zero representation of a timestamp.
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function nullDate($quoted = true);

    /**
     * Generate a SQL statement to check if column represents a zero or null datetime.
     *
     * Usage:
     * $query->where($query->isNullDatetime('modified_date'));
     *
     * @param   string  $column  A column name.
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function isNullDatetime($column);

    /**
     * Add an ordering column to the ORDER clause of the query.
     *
     * Usage:
     * $query->order('foo')->order('bar');
     * $query->order(array('foo','bar'));
     *
     * @param   array|string  $columns  A string or array of ordering columns.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function order($columns);

    /**
     * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
     * risks and reserved word conflicts.
     *
     * This method is provided for use where the query object is passed to a function for modification.
     * If you have direct access to the database object, it is recommended you use the quoteName method directly.
     *
     * Note that 'qn' is an alias for this method as it is in DatabaseDriver.
     *
     * Usage:
     * $query->quoteName('#__a');
     * $query->qn('#__a');
     *
     * @param   array|string  $name  The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
     *                               Each type supports dot-notation name.
     * @param   array|string  $as    The AS query part associated to $name. It can be string or array, in latter case it has to be
     *                               same length of $name; if is null there will not be any AS part for string or array element.
     *
     * @return  array|string  The quote wrapped name, same type of $name.
     *
     * @since   1.0
     * @throws  \RuntimeException if the internal db property is not a valid object.
     */
    public function quoteName($name, $as = null);

    /**
     * Get the function to return a random floating-point value
     *
     * Usage:
     * $query->rand();
     *
     * @return  string
     *
     * @since   2.0.0
     */
    public function rand();

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
     * @since   2.0.0
     */
    public function regexp($value);

    /**
     * Add a single column, or array of columns to the SELECT clause of the query.
     *
     * Usage:
     * $query->select('a.*')->select('b.id');
     * $query->select(array('a.*', 'b.id'));
     *
     * @param   array|string  $columns  A string or an array of field names.
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  QueryTypeAlreadyDefinedException if the query type has already been defined
     */
    public function select($columns);

    /**
     * Return the number of the current row.
     *
     * Usage:
     * $query->select('id');
     * $query->selectRowNumber('ordering,publish_up DESC', 'new_ordering');
     * $query->from('#__content');
     *
     * @param   string  $orderBy           An expression of ordering for window function.
     * @param   string  $orderColumnAlias  An alias for new ordering column.
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  \RuntimeException
     */
    public function selectRowNumber($orderBy, $orderColumnAlias);

    /**
     * Add a single condition string, or an array of strings to the SET clause of the query.
     *
     * Usage:
     * $query->set('a = 1')->set('b = 2');
     * $query->set(array('a = 1', 'b = 2');
     *
     * @param   array|string  $conditions  A string or array of string conditions.
     * @param   string        $glue        The glue by which to join the condition strings. Defaults to `,`.
     *                                     Note that the glue is set on first use and cannot be changed.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function set($conditions, $glue = ',');

    /**
     * Add a table name to the UPDATE clause of the query.
     *
     * Usage:
     * $query->update('#__foo')->set(...);
     *
     * @param   string  $table  A table to update.
     *
     * @return  $this
     *
     * @since   2.0.0
     * @throws  QueryTypeAlreadyDefinedException if the query type has already been defined
     */
    public function update($table);

    /**
     * Adds a tuple, or array of tuples that would be used as values for an INSERT INTO statement.
     *
     * Usage:
     * $query->values('1,2,3')->values('4,5,6');
     * $query->values(array('1,2,3', '4,5,6'));
     *
     * @param   array|string  $values  A single tuple, or array of tuples.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function values($values);

    /**
     * Add a single condition, or an array of conditions to the WHERE clause of the query.
     *
     * Usage:
     * $query->where('a = 1')->where('b = 2');
     * $query->where(array('a = 1', 'b = 2'));
     *
     * @param   array|string  $conditions  A string or array of where conditions.
     * @param   string        $glue        The glue by which to join the conditions. Defaults to AND.
     *                                     Note that the glue is set on first use and cannot be changed.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function where($conditions, $glue = 'AND');

    /**
     * Add a WHERE IN statement to the query.
     *
     * Note that all values must be the same data type.
     *
     * Usage
     * $query->whereIn('id', [1, 2, 3]);
     *
     * @param   string        $keyName    Key name for the where clause
     * @param   array         $keyValues  Array of values to be matched
     * @param   array|string  $dataType   Constant corresponding to a SQL datatype. It can be an array, in this case it
     *                                    has to be same length of $keyValues
     *
     * @return  $this
     *
     * @since 2.0.0
     */
    public function whereIn(string $keyName, array $keyValues, $dataType = ParameterType::INTEGER);

    /**
     * Add a WHERE NOT IN statement to the query.
     *
     * Note that all values must be the same data type.
     *
     * Usage
     * $query->whereNotIn('id', [1, 2, 3]);
     *
     * @param   string        $keyName    Key name for the where clause
     * @param   array         $keyValues  Array of values to be matched
     * @param   array|string  $dataType   Constant corresponding to a SQL datatype. It can be an array, in this case it
     *                                    has to be same length of $keyValues
     *
     * @return  $this
     *
     * @since 2.0.0
     */
    public function whereNotIn(string $keyName, array $keyValues, $dataType = ParameterType::INTEGER);

    /**
     * Extend the WHERE clause with a single condition or an array of conditions, with a potentially different logical operator from the one in the
     * current WHERE clause.
     *
     * Usage:
     * $query->where(array('a = 1', 'b = 2'))->extendWhere('XOR', array('c = 3', 'd = 4'));
     * will produce: WHERE ((a = 1 AND b = 2) XOR (c = 3 AND d = 4)
     *
     * @param   string  $outerGlue   The glue by which to join the conditions to the current WHERE conditions.
     * @param   mixed   $conditions  A string or array of WHERE conditions.
     * @param   string  $innerGlue   The glue by which to join the conditions. Defaults to AND.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function extendWhere($outerGlue, $conditions, $innerGlue = 'AND');

    /**
     * Binds an array of values and returns an array of prepared parameter names.
     *
     * Note that all values must be the same data type.
     *
     * Usage:
     * $query->whereIn('column in (' . implode(',', $query->bindArray($keyValues, $dataType)) . ')');
     *
     * @param   array         $values    Values to bind
     * @param   array|string  $dataType  Constant corresponding to a SQL datatype. It can be an array, in this case it
     *                                   has to be same length of $key
     *
     * @return  array   An array with parameter names
     *
     * @since 2.0.0
     */
    public function bindArray(array $values, $dataType = ParameterType::INTEGER);

    /**
     * Add a query to UNION with the current query.
     *
     * Usage:
     * $query->union('SELECT name FROM  #__foo')
     * $query->union('SELECT name FROM  #__foo', true)
     *
     * @param   DatabaseQuery|string  $query     The DatabaseQuery object or string to union.
     * @param   boolean               $distinct  True to only return distinct rows from the union.
     *
     * @return  $this
     *
     * @since   1.0
     */
    public function union($query, $distinct = true);

    /**
     * Add a query to UNION ALL with the current query.
     *
     * Usage:
     * $query->unionAll('SELECT name FROM  #__foo')
     *
     * @param   DatabaseQuery|string  $query  The DatabaseQuery object or string to union.
     *
     * @return  $this
     *
     * @see     union
     * @since   1.5.0
     */
    public function unionAll($query);

    /**
     * Set a single query to the query set.
     * On this type of DatabaseQuery you can use union(), unionAll(), order() and setLimit()
     *
     * Usage:
     * $query->querySet($query2->select('name')->from('#__foo')->order('id DESC')->setLimit(1))
     *       ->unionAll($query3->select('name')->from('#__foo')->order('id')->setLimit(1))
     *       ->order('name')
     *       ->setLimit(1)
     *
     * @param   DatabaseQuery|string  $query  The DatabaseQuery object or string.
     *
     * @return  $this
     *
     * @since   2.0.0
     */
    public function querySet($query);

    /**
     * Create a DatabaseQuery object of type querySet from current query.
     *
     * Usage:
     * $query->select('name')->from('#__foo')->order('id DESC')->setLimit(1)
     *       ->toQuerySet()
     *       ->unionAll($query2->select('name')->from('#__foo')->order('id')->setLimit(1))
     *       ->order('name')
     *       ->setLimit(1)
     *
     * @return  DatabaseQuery  A new object of the DatabaseQuery.
     *
     * @since   2.0.0
     */
    public function toQuerySet();
}
