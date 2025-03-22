## Updating from v3 to v4

The following changes were made to the Database package between v3 and v4.

### Minimum supported PHP version raised

All Framework packages now require PHP 8.1 or newer.

### Minimum supported database versions raised

The following are the minimum supported database versions:

- MySQL: 5.6
- PostgreSQL: 9.2.0
- MS SQL: 11.0.2100.60 (SQL Server 2012)

### Removed quoteNameStr

The deprecated method `quoteNameStr` has been removed. Use `quoteNameString` instead.
