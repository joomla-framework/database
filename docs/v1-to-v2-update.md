# Updating from v1 to v2

Release 2.0.0 is the largest break in this package's history. Prepared statements became mandatory,
the driver lost its debug and logging features to query monitors, and several query builder methods
changed shape.

## At a glance

| | v1 (1.8.x) | v2 (2.0.0) |
|---|---|---|
| PHP | `^5.3.10 \| ~7.0` | `^7.2.5 \| ^8.0` |
| PDO Oracle, native PostgreSQL | supported | **removed** |
| Prepared statements | optional | required of every query object |
| Parameter types | driver-specific constants | `ParameterType` |
| Driver debug mode (`setDebug()`, `$debug`) | present | **removed** |
| PSR-3 logging on the driver | present | **removed**, use a query monitor |
| `DatabaseDriver::getInstance()` | supported | deprecated |
| `unionDistinct()` | present | **removed** |
| `join()` argument shape | `('INNER', 'b ON b.id = a.id')` | `('INNER', 'b', 'b.id = a.id')` |
| Changing a query's type | silently allowed | throws |
| `castAsChar()` | present | deprecated |

## Requirements

All Framework packages now require **PHP 7.2** or newer.

Minimum database versions:

| Server | Minimum |
|---|---|
| MySQL | 5.6 |
| PostgreSQL | 9.2.0 |
| MS SQL | 11.0.2100.60 (SQL Server 2012) |

### Removed driver support

PDO Oracle and the native PostgreSQL driver are gone. PostgreSQL users move to the PDO driver:

```php
// v1
$db = DatabaseDriver::getInstance(['driver' => 'postgresql', /* … */]);

// v2
$db = (new DatabaseFactory())->getDriver('pgsql', [/* … */]);
```

## Interfaces

### `DatabaseInterface` populated

`Joomla\Database\DatabaseInterface` was close to empty in 1.x. It now carries most of the public
methods of `DatabaseDriver`, so you can type-hint the interface and still call `loadObjectList()`,
`quote()`, `transactionStart()` and the rest.

### `QueryInterface` added

Every query object must implement `Joomla\Database\QueryInterface`. It extends the feature
interfaces `Query\LimitableInterface` and `Query\PreparableInterface`, which are **deprecated as of
2.0** — their methods move onto `QueryInterface` itself in a later release (this happened in 4.0).

```php
// v2 - implement the one interface
class MyQuery extends DatabaseQuery implements QueryInterface
```

### Prepared statements are mandatory

Because `QueryInterface` extends `PreparableInterface`, every query object supports bound
parameters. There is no longer a query implementation that only concatenates strings.

### `ParameterType` replaces driver constants

Binding used to require a constant specific to the driver. 2.0 introduces an abstract set:

```php
use Joomla\Database\ParameterType;

$query->bind(':id', $id, ParameterType::INTEGER);
```

| Constant | Value |
|---|---|
| `BOOLEAN` | `'boolean'` |
| `INTEGER` | `'int'` |
| `LARGE_OBJECT` | `'lob'` |
| `NULL` | `'null'` |
| `STRING` | `'string'` |

They are **strings**, not the PDO integers. Each `PreparableInterface` implementation maps them to
its driver's own type where that applies.

## `DatabaseDriver` changes

### Debug mode removed

`setDebug()` and the `$debug` property are gone. What debug mode used to give you — the SQL, the
timing, the call stack — is what a query monitor gives you now.

### Query monitors added

Drivers accept a `QueryMonitorInterface` implementation, loosely modelled on Doctrine's SQLLogger:

```php
use Joomla\Database\Monitor\DebugMonitor;

$monitor = new DebugMonitor();
$db->setMonitor($monitor);

// … run queries …

$monitor->getLogs();        // the SQL statements
$monitor->getTimings();     // start and stop, two entries per query
$monitor->getCallStacks();
```

See [Connections and drivers](connections.md#observing-queries).

### PSR-3 support removed

Drivers are no longer logger-aware. Replace `$db->setLogger($logger)` with a
`Monitor\LoggingMonitor`:

```php
use Joomla\Database\Monitor\LoggingMonitor;

$monitor = new LoggingMonitor();
$monitor->setLogger($logger);

$db->setMonitor($monitor);
```

### Connection events added

Drivers dispatch read-only events when a connection opens or closes —
`DatabaseEvents::POST_CONNECT` and `DatabaseEvents::POST_DISCONNECT`, both carrying an
`Event\ConnectionEvent`.

### Singleton storage deprecated

`DatabaseDriver::getInstance()` is deprecated and removed in 3.0.

```php
// v1
$db = DatabaseDriver::getInstance($options);

// v2
$db = (new DatabaseFactory())->getDriver($options['driver'], $options);
```

If you need singleton semantics, share the driver through a DI container — `Service\DatabaseProvider`
does exactly that — or extend `DatabaseFactory::getDriver()` with your own caching.

## `DatabaseQuery` changes

### `union()`, `unionAll()`, `unionDistinct()`

```php
// v1
$query->unionDistinct($other);
$query->union([$a, $b]);
$query->unionAll($other, $distinct, $glue);

// v2
$query->union($other);              // distinct by default
$query->union($other, false);       // UNION ALL
$query->unionAll($other);
```

* `unionDistinct()` was removed — `union()` is distinct by default.
* `$query` no longer accepts an array. Pass a `DatabaseQuery` or a string, once per call.
* `$distinct` was removed from `unionAll()`, and `$glue` from both.
* The `$union` and `$unionAll` properties merged into a single ordered `$merge`.
* `__toString()` no longer supports `$type = 'union'`.

Two methods arrived with them:

```php
$query->querySet($other);      // turn this object into a query set and add a query
$set = $query->toQuerySet();   // build a query set from the current query
```

A query set lets the first `SELECT` keep its own `ORDER BY` and `LIMIT` inside a union.

### `join()` and friends

The trailing `$conditions` argument was split into `$table` and `$condition`:

```php
// v1
$query->join('INNER', 'b ON b.id = a.id');

// v2
$query->join('INNER', 'b', 'b.id = a.id');
```

The old single-string form still parses in many cases, but **not** for a PostgreSQL UPDATE query.
Split it.

### `from()`

```php
// v1
$query->from($subquery, 'alias');
$query->from([$a, $b]);

// v2
$query->from($subquery->alias('alias'));
$query->from($a)->from($b);
```

`$table` no longer accepts an array, and `$subQueryAlias` was removed in favour of the new
`alias()` method on the query.

### Query type immutability enforced

Methods that define a query type — `select()`, `insert()`, `update()`, `delete()` — documented that
the type must not change afterwards, but nothing enforced it. 2.0 throws
`Exception\QueryTypeAlreadyDefinedException`.

```php
$query->clear();                // start over
$query->clear('select');        // or clear just the type clause
```

### `castAsChar()` deprecated

```php
// v1
$query->castAsChar($value);

// v2
$query->castAs('CHAR', $value);
$query->castAs('CHAR', $value, 10);     // with a length
```

`castAs()` is the generic form and supports other target types. `castAsChar()` was removed in 4.0.

## Dependency changes

| Package | v1 (1.8.x) | v2 (2.0.0) |
|---|---|---|
| `php` | `^5.3.10 \| ~7.0` | `^7.2.5 \| ^8.0` |
| `psr/log` | `~1.0` (required) | moved to dev/suggest — the driver is no longer logger-aware |
| `joomla/event` | — | `^2.0` (connection events) |
| `symfony/deprecation-contracts` | — | `^2.1` |
