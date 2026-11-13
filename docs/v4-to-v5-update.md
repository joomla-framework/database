## Updating from v4 to v5

The following changes were made to the Database package between v4 and v5.

### `DatabaseIterator` has native return types

`DatabaseIterator::count()`, `current()`, `key()`, `next()`, `rewind()` and `valid()` declare the return types of
`Iterator` and `Countable` (`int`, `mixed`, `mixed`, `void`, `void`, `bool`) instead of relying on
`#[\ReturnTypeWillChange]`. Subclasses that override these methods must declare the same return types.
