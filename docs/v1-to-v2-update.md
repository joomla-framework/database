## Updating from v1 to v2

The following changes were made to the Database package between v1 and v2.

### Minimum supported PHP version raised

All Framework packages now require PHP 7.0 or newer.

### Minimum supported database versions raised

The following are the minimum supported database versions:

- MySQL: 5.5.3
- PostgreSQL: 9.2.0
- MS SQL: 11.0.2100.60 (SQL Server 2012)

### Removed Driver Support

Support for PDO Oracle and native PostgreSQL has been removed.  PDO PostgreSQL is available for PostgreSQL users.

### `Joomla\Database\DatabaseInterface` populated

`Joomla\Database\DatabaseInterface` has been filled with most of the public methods of `Joomla\Database\DatabaseDatabase`.

### `Joomla\Database\QueryInterface` added

`Joomla\Database\QueryInterface` has been added to the package. All query objects must now implement this interface.

### Query feature interfaces deprecated

As `Joomla\Database\QueryInterface` is now extending `Joomla\Database\Query\LimitableInterface` and `Joomla\Database\Query\PreparableInterface`, these
feature interfaces are no longer required and are deprecated. All query objects must implement `Joomla\Database\QueryInterface` and as of 3.0 the
methods defined in the deprecated interfaces will be moved into `Joomla\Database\QueryInterface`.

### Support for parameterized queries required

`Joomla\Database\QueryInterface` extends `Joomla\Database\Query\PrepareableInterface`, which is the interface defining that
a query supports parameterized queries. This effectively mandates that all query objects support parameterized queries.

### Abstraction layer for parameterized queries

For drivers which support defining an argument's type, the method required passing a parameter specific to the driver's implementation.
As of 2.0, there is now a `Joomla\Database\ParameterType` object defining supported data types in an abstract manner.
`Joomla\Database\Query\PrepareableInterface` implementations should map this argument to the driver specific data type when applicable.

### `Joomla\Database\DatabaseDriver` general changes

The base `Joomla\Database\DatabaseDriver` class has undergone several underlying API changes to create a more flexible platform moving forward.
Significant changes include:

#### Debug mode removed

The database driver's debug mode, and corresponding `setDebug` API and `$debug` property, have been removed

#### Query monitors added

Database drivers now support monitors via a `Joomla\Database\QueryMonitorInterface` implementation. This implementation is loosely modeled on the
Doctrine SQLLogger interface.

#### PSR-3 support removed

Database drivers are no longer logger aware, logging should instead be performed in a query monitor if desired.

#### Connection events added

Database drivers now support dispatching read-only events when a connection to the database is opened or closed.

#### Singleton storage deprecated

`Joomla\Database\DatabaseDriver::getInstance()` has been deprecated and will be removed in 3.0. Applications which require support for singleton object
storage should extend `Joomla\Database\DatabaseFactory::getDriver()` implementing their additional logic.

### `Joomla\Database\DatabaseQuery` general changes

#### Changes in methods `union()`, `unionAll()` and `unionDistinct()`

- Method `unionDistinct()` has been removed. Use `union()` instead.
- Argument `$query` stops accepting the array. Only `DatabaseQuery` object or string.
- Argument `$distinct` has been removed from `unionAll`.
- Argument `$glue` has been removed from both.

Method `union()` by default has `$distinct = true`.
If `$distinct` is `false` then generates `UNION ALL` sql statement.

Class variables `$union` and `$unionAll` have been merged into one variable `$merge`.
The new variable represents an ordered array of individual elements.

Stop supporting `$type = 'union'` in method `__toString()`.

New methods:
- `querySet($query)` changes object type to querySet and set a query in query set.
- `toQuerySet()` from current object creates DatabaseQuery of type querySet.

The DatabaseQuery object of type querySet can be used to generate a union query
where the first SELECT statement has own ORDER BY and LIMIT.

#### Changes in methods `join()`, `innerJoin()`, `outerJoin()`, `leftJoin()`, `rightJoin()`

The last argument `$conditions` has been split into two arguments: $table and $condition.
Instead of `$query->join('INNER', 'b ON b.id = a.id)` use `$query->join('INNER', 'b', 'b.id = a.id)`.

Although the old syntax still works in many cases, it will not work on PostgreSQL update query.
