## Updating from v3 to v4

The following changes were made to the Database package between v3 and v4.

### Minimum supported PHP version raised

All Framework packages now require PHP 8.3 or newer.

### Minimum supported database versions raised

The following are the minimum supported database versions:

- MySQL: 5.6
- PostgreSQL: 9.2.0
- MS SQL: 11.0.2100.60 (SQL Server 2012)

### Removed quoteNameStr

The deprecated method `quoteNameStr` has been removed. Use `quoteNameString` instead.

### DatabaseInterface: `createQuery` method

`DatabaseInterface` adds a `createQuery` method for creating query objects. Use `createQuery()` instead of `getQuery(true)`.
If you have a custom query class update your adapter's `createQuery()` method to return your custom query class.

### Removed deprecated `LimitableInterface` and `PreparableInterface`

The interfaces `Joomla\Database\Query\LimitableInterface` and `Joomla\Database\Query\PreparableInterface` have been removed and its signatures added to the `QueryInterface`. All objects implementing one of these interfaces need to implement the `QueryInterface` instead.

### Removed deprecated `DatabaseQuery::castAsChar()`

The deprecated method `DatabaseQuery::castAsChar()` as been removed. Use `$query->castAs('CHAR', $value)` instead.
