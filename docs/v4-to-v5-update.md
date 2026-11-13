## Updating from v4 to v5

The following changes were made to the Database package between v4 and v5.

### Removed deprecated API

The following API was deprecated since 1.x/2.x and has been removed:

| Removed | Use instead |
|---|---|
| `DatabaseDriver::getInstance()` and the static `$instances` storage | `DatabaseFactory::getDriver()`; keep the driver instance yourself, e.g. in a DI container |
| Reading `$db->name` (magic `__get()`) | `$db->getName()` |
| The `$offset` and `$limit` arguments of `DatabaseInterface::setQuery()` / `DatabaseDriver::setQuery()` | `$query->setLimit($limit, $offset)` before `setQuery($query)` |
| The `$new` argument of `DatabaseInterface::getQuery()` / `DatabaseDriver::getQuery()` | `createQuery()` for a new query; `getQuery()` returns the current one |
| `DatabaseQuery::dump()` | Cast the query to a string |

Passing an offset or limit to `setQuery()`, or `true` to `getQuery()`, throws an `InvalidArgumentException` instead of
being silently ignored, so code that was not migrated fails loudly. Implementations of `DatabaseInterface` should
drop the removed parameters from their signatures.
