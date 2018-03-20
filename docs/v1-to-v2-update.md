## Updating from v1 to v2

The following changes were made to the Database package between v1 and v2.

### Minimum supported PHP version raised

All Framework packages now require PHP 7.0 or newer.

### Minimum supported database versions raised

The following are the minimum supported database versions:

- MySQL: 5.5.3
- PostgreSQL: 9.2.0

### `Joomla\Database\DatabaseInterface` populated

`Joomla\Database\DatabaseInterface` has been filled with most of the public methods of `Joomla\Database\DatabaseDatabase`.

### `Joomla\Database\QueryInterface` added

`Joomla\Database\QueryInterface` has been added to the package. All query objects must now implement this interface.

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
