## Updating from v4 to v5

The following changes were made to the Database package between v4 and v5.

### `getBounded()` no longer returns by reference

`QueryInterface::getBounded()` and `DatabaseQuery::getBounded()` return the bound parameters by value. Implementations
of `QueryInterface` should drop the `&` from their declaration, and callers should not assign the result by reference
(`$bounded =& $query->getBounded()`). The bindings themselves still reference the bound variables, so `bind()` works as
before.
