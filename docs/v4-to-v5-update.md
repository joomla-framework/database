## Updating from v4 to v5

The following changes were made to the Database package between v4 and v5.

### `quoteName()` rejects mismatched aliases

`DatabaseDriver::quoteName($name, $as)` with an array of names and an alias array of a different length, or a single
alias string, now throws an `InvalidArgumentException`. In 4.x, arrays of different length returned an empty array
(deprecated since 4.x) and a string alias failed with a `TypeError`. Pass one alias per name, or `null` for no aliases.
