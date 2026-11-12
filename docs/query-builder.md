# Query builder

`DatabaseDriver::createQuery()` returns a driver-specific `QueryInterface` implementation — a
fluent builder that renders to SQL when cast to a string.

```php
$query = $db->createQuery();
```

Every method returns the query, so calls chain. A query is bound to one driver and cannot be moved
to another.

> Do not use `getQuery(true)` any more. It still works, but it emits a deprecation and will lose
> the parameter in 5.0. `getQuery()` without an argument returns the *last query set on the
> driver*, which is a different thing entirely.

## SELECT

```php
$query->select(['a.id', 'a.title', 'u.name'])
    ->from($db->quoteName('#__articles', 'a'))
    ->join('LEFT', $db->quoteName('#__users', 'u'), 'u.id = a.created_by')
    ->where($db->quoteName('a.state') . ' = :state')
    ->order($db->quoteName('a.created') . ' DESC')
    ->setLimit(20, 40);

$query->bind(':state', $state, ParameterType::INTEGER);
```

`select()` accepts a string or an array; repeated calls append. `quoteName()` takes an optional
alias as its second argument, which is how `#__articles AS a` above is produced.

### Joins

```php
public function join($type, $table, $condition = null)
public function innerJoin($table, $condition = null)
public function leftJoin($table, $condition = null)
public function rightJoin($table, $condition = null)
public function outerJoin($table, $condition = null)
```

Table and condition are **separate arguments**:

```php
$query->join('INNER', $db->quoteName('#__users', 'u'), 'u.id = a.created_by');
```

Passing them as one string (`join('INNER', 'u ON u.id = a.created_by')`) still parses on some
drivers but breaks on PostgreSQL UPDATE queries. Keep them apart.

### WHERE

```php
$query->where($db->quoteName('state') . ' = :state')          // AND by default
      ->where($db->quoteName('access') . ' IN (1, 2)', 'AND');

$query->whereIn($db->quoteName('id'), $ids, ParameterType::INTEGER);
$query->whereNotIn($db->quoteName('catid'), $excluded, ParameterType::INTEGER);
```

`whereIn()` and `whereNotIn()` generate the placeholders and bind the values for you — this is the
one place where you do not have to call `bind()` yourself.

For mixed glue, `extendWhere()` opens a nested group:

```php
$query->where($db->quoteName('state') . ' = 1')
      ->extendWhere('AND', [
          $db->quoteName('publish_up') . ' IS NULL',
          $db->quoteName('publish_up') . ' <= :now',
      ], 'OR');
// … WHERE state = 1 AND (publish_up IS NULL OR publish_up <= :now)
```

`orWhere($conditions, $glue = 'AND')` and `andWhere($conditions, $glue = 'OR')` are shorthands for
the two common shapes of the same thing.

### Grouping and ordering

```php
$query->group($db->quoteName('a.catid'))
      ->having('COUNT(*) > :min')
      ->order([$db->quoteName('total') . ' DESC', $db->quoteName('a.title') . ' ASC']);
```

### Limits

```php
$query->setLimit(20, 40);           // LIMIT 20 OFFSET 40
$db->setQuery($query, 40, 20);      // same thing, offset first
```

`setLimit()` takes limit-then-offset; `DatabaseDriver::setQuery()` takes offset-then-limit. Mixing
the two up is a classic source of off-by-a-page bugs. Prefer `setLimit()` and keep `setQuery()` to
one argument.

For SQL Server, `selectRowNumber($orderBy, $orderColumnAlias)` produces the windowed row number
that pagination needs there.

## INSERT

```php
$query->insert($db->quoteName('#__articles'))
    ->columns($db->quoteName(['title', 'state', 'created']))
    ->values(':title, :state, :created')
    ->bind(':title', $title)
    ->bind(':state', $state, ParameterType::INTEGER)
    ->bind(':created', $created);

$db->setQuery($query)->execute();
$id = $db->insertid();
```

`values()` accepts several rows at once when passed an array of comma-joined strings. With bound
parameters that means one placeholder set per row — for bulk inserts, `bindArray()` generates the
names:

```php
$names = $query->bindArray($rowValues, ParameterType::STRING);
$query->values(implode(',', $names));
```

`insert()` takes an `$incrementField` as its second argument for drivers (PostgreSQL, SQL Server)
that need the sequence named explicitly.

## UPDATE and DELETE

```php
$query->update($db->quoteName('#__articles'))
    ->set($db->quoteName('state') . ' = :state')
    ->where($db->quoteName('id') . ' = :id')
    ->bind(':state', $state, ParameterType::INTEGER)
    ->bind(':id', $id, ParameterType::INTEGER);
```

```php
$query->delete($db->quoteName('#__articles'))
    ->where($db->quoteName('id') . ' = :id')
    ->bind(':id', $id, ParameterType::INTEGER);
```

`set()` glues with `,` by default, which is what UPDATE needs; `where()` glues with `AND`.

## The query type cannot change

Calling `select()` on a query that already has an `insert()` raises
`Exception\QueryTypeAlreadyDefinedException`. Reset or start over:

```php
$query->clear();                // everything
$query->clear('where');         // one clause
```

`clear()` understands the clause names `alias`, `select`, `delete`, `update`, `insert`, `querySet`,
`from`, `join`, `set`, `where`, `group`, `having`, `merge`, `order`, `columns`, `values`, `exec`,
`call`, `limit`, `offset` and `bounded`. Anything else is ignored silently, so check the spelling.

Note that `clear('bounded')` is what drops the bound parameters — clearing `where` leaves them in
place.

## Unions and query sets

```php
$first  = $db->createQuery()->select('id')->from($db->quoteName('#__articles'));
$second = $db->createQuery()->select('id')->from($db->quoteName('#__contacts'));

$first->union($second);              // UNION (distinct)
$first->union($second, false);       // UNION ALL
$first->unionAll($second);           // the same, explicitly
```

`union()` takes one query or SQL string — not an array — and defaults to `$distinct = true`.

When the first SELECT needs its own `ORDER BY` and `LIMIT`, wrap the whole thing in a query set:

```php
$set = $first->toQuerySet()->order('id DESC')->setLimit(10);
$set->querySet($second);
```

## Portable SQL fragments

The builder abstracts the differences that bite most often:

| Method | Produces |
|---|---|
| `castAs($type, $value, $length = null)` | `CAST`/`CONVERT` in the driver's dialect |
| `charLength($field, $operator = null, $condition = null)` | `CHAR_LENGTH()` / `LENGTH()` |
| `length($value)` | The byte-length function |
| `concatenate($values, $separator = null)` | `CONCAT` / `\|\|` |
| `groupConcat($expression, $separator = ',')` | `GROUP_CONCAT` / `string_agg` |
| `currentTimestamp()` | `NOW()` / `CURRENT_TIMESTAMP` |
| `dateAdd($date, $interval, $datePart)` | Date arithmetic |
| `year()`, `month()`, `day()`, `hour()`, `minute()`, `second()` | Date part extraction |
| `nullDate($quoted = true)` | The driver's null-date literal |
| `isNullDatetime($column)` | `column IS NULL OR column = <null date>` |
| `findInSet($value, $set)` | `FIND_IN_SET()` and its equivalents |
| `regexp($value)` | `REGEXP` / `~` |
| `rand()` | `RAND()` / `RANDOM()` |

```php
$query->select($query->castAs('CHAR', 'a.id', 10));
$query->where($query->isNullDatetime('a.publish_down'));
$query->order($query->rand());
```

Use these rather than writing the MySQL form and hoping — they are the reason a query built here
runs on PostgreSQL too.

The MySQL and PostgreSQL dialects come from the shared traits `Query\MysqlQueryBuilder` and
`Query\PostgresqlQueryBuilder`; SQLite and SQL Server implement their own. Coverage is not
identical everywhere — `groupConcat()`, for example, exists on all four but `dateAdd()` and
`selectRowNumber()` are implemented per driver. If you target more than one server, run the query
against each of them rather than assuming.

## Inspecting the query

```php
echo (string) $query;        // the SQL, with #__ still in place
```

Casting to a string is what `setQuery()` does internally. Prefix replacement happens later, in the
driver, so what you see here still contains `#__`.

`dump()` returns the same SQL wrapped in a `<pre>` element with the prefix substituted. It has been
deprecated since 2.0 — use a [query monitor](connections.md#observing-queries) instead, which sees
the final SQL along with the bound parameters.

## Shorthands

`q()`, `qn()` and `e()` are aliases of `quote()`, `quoteName()` and `escape()` on the query object,
which in turn delegate to the driver. They exist for brevity in long expressions:

```php
$query->where($query->qn('state') . ' = ' . $query->q($state));
```

Prefer bound parameters over `q()` — see [Parameters and escaping](parameters-and-escaping.md).
