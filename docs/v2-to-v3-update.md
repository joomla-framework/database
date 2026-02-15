## Updating from v2 to v3

The following changes were made to the Database package between v2 and v3.

### Minimum supported PHP version raised

All Framework packages now require PHP 8.1 or newer.

### `DatabaseDriver::getQuery(true)` has been deprecated

`DatabaseDriver::getQuery()` with the parameter set to `true` returns a new `DatabaseQuery` object, while the unset parameter or set to `false` returns the last query set.
This parameter has been deprecated and will be removed in 5.0. `DatabaseDriver::getQuery()` will only return the last set query in the future and instead you should use `DatabaseDriver::createQuery()`.
