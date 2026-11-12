# Parameters and escaping

Bind values. Quote identifiers. Reach for `quote()` only when neither of those applies.

## Binding values

```php
use Joomla\Database\ParameterType;

$query = $db->createQuery()
    ->select('*')
    ->from($db->quoteName('#__articles'))
    ->where($db->quoteName('catid') . ' = :catid')
    ->where($db->quoteName('title') . ' LIKE :title');

$query->bind(':catid', $catid, ParameterType::INTEGER);
$query->bind(':title', $search);                        // STRING is the default
```

```php
public function bind($key, &$value, $dataType = ParameterType::STRING, $length = 0, $driverOptions = [])
```

Placeholders are named (`:name`); positional `?` markers are not supported by the builder.
`unbind(':catid')` removes one, and `clear('bounded')` removes all.

## Parameter types

`ParameterType` constants are **strings**, not the PDO integers:

| Constant | Value |
|---|---|
| `ParameterType::BOOLEAN` | `'boolean'` |
| `ParameterType::INTEGER` | `'int'` |
| `ParameterType::LARGE_OBJECT` | `'lob'` |
| `ParameterType::NULL` | `'null'` |
| `ParameterType::STRING` | `'string'` |

Each driver maps them to its own type constants. Passing a `PDO::PARAM_*` constant instead raises
`InvalidArgumentException: Unsupported parameter type` — the mapping table is checked, so at least
the mistake is loud.

Getting the type wrong is quieter and more damaging: an integer column compared against a
`STRING`-bound value can cost you the index on MySQL, and on PostgreSQL it is a type error.

## Binding by reference

`bind()` takes `&$value`. Two consequences follow, and both bite in practice.

**You cannot bind a literal.**

```php
$query->bind(':state', 1);              // Error: cannot pass parameter by reference
```

```php
$state = 1;
$query->bind(':state', $state);         // fine
```

**A loop variable binds once, to one slot.**

```php
// Wrong - every binding points at $id, which ends up holding the last value
foreach ($ids as $i => $id) {
    $query->bind(':id' . $i, $id, ParameterType::INTEGER);
}
```

```php
// Right - each binding gets its own variable
foreach ($ids as $i => $id) {
    ${'id' . $i} = $id;
    $query->bind(':id' . $i, ${'id' . $i}, ParameterType::INTEGER);
}
```

In practice you rarely need either, because two helpers do the work:

```php
// A list of placeholders, bound in one call
$names = $query->bindArray($ids, ParameterType::INTEGER);
$query->where($db->quoteName('id') . ' IN (' . implode(',', $names) . ')');

// Or, for exactly this case
$query->whereIn($db->quoteName('id'), $ids, ParameterType::INTEGER);
```

`bind()` also accepts arrays for `$key`, `$value` and `$dataType` together, which is what
`bindArray()` uses internally. The three arrays must be the same length or it raises
`InvalidArgumentException`.

There is no `bindValue()`. If you need one, assign to a variable first — that is all a `bindValue()`
would do here.

## Quoting identifiers

```php
$db->quoteName('title');                       // `title`
$db->quoteName('a.title');                     // `a`.`title`
$db->quoteName('#__articles', 'a');            // `#__articles` AS `a`
$db->quoteName(['id', 'title', 'state']);      // an array of quoted names
```

The closing quote character is doubled inside the name **before** the dot split, so an identifier
containing a backtick or a bracket cannot break out. Identifier injection through `quoteName()` is
not possible.

What is not possible either: quoting a name that genuinely contains a dot. Every dot is treated as
a separator, so `quoteName('weird.column')` produces two parts. The `$asSinglePart` parameter that
would handle it exists on `quoteNameString()` but is not reachable from `quoteName()`.

## Quoting values

```php
$db->quote($value);                 // escapes, then wraps in single quotes
$db->quote($value, false);          // wraps without escaping - almost never what you want
$db->quote(['a', 'b']);             // an array of quoted values
$db->quoteBinary($blob);            // X'…' hexadecimal literal
$db->escape($text, $extra = false); // escape without the quotes
```

Prefer a bound parameter. Use `quote()` for values you generated yourself — a constant, an
enumerated flag, a value already validated against a whitelist — and keep anything from a request
on the `bind()` path.

### Why the preference is not just style

`PdoDriver::escape()` does not ask the connection how to escape:

```php
$text = str_replace("'", "''", (string) $text);

return addcslashes($text, "\000\n\r\\\032");
```

`PDO::quote()`, which exists precisely to delegate this to the driver, is never called. Two
concrete failure modes follow:

* **Multi-byte connection charsets.** With GBK, Big5, SJIS or CP932, a lead byte can swallow the
  escaping backslash and free the quote that follows. This is the exact attack
  `mysql_real_escape_string()` and `PDO::quote()` were introduced to stop, and the package lets you
  configure the charset without checking it.
* **`NO_BACKSLASH_ESCAPES`.** Under that SQL mode — common in ANSI and strict configurations —
  backslashes are not escape characters, so `addcslashes()` writes **literal backslashes into your
  data**.

`SqlsrvDriver::escape()` has a different problem: it replaces NUL bytes with `' + CHAR(0) + N'`,
that is, it emits SQL syntax. Its output is only correct when dropped directly between two single
quotes; used anywhere else it produces broken or manipulable SQL.

`escape()` also returns an `int` for integer input on every driver, despite the `string` docblock.
`strlen($db->escape($n))` will surprise you.

Bound parameters bypass all of this: the value never enters the SQL string.

## The table prefix

`#__` in any statement is replaced with the configured `prefix` just before execution:

```php
$db = $factory->getDriver('mysqli', ['prefix' => 'app_', /* … */]);

$db->setQuery('SELECT * FROM #__articles');    // SELECT * FROM app_articles
```

`replacePrefix()` does this with a hand-written scanner that walks the statement character by
character, tracking quote and escape state so that `#__` inside a string literal is left alone.

It is worth knowing where that scanner disagrees with the server:

* PostgreSQL dollar-quoting (`$$ … $$`) is not recognised as a string context
* `#` inside a backtick-quoted MySQL identifier
* comments — `-- #__x` and `/* #__x */`

and it runs on **every** statement, at a cost proportional to the length. For a query with a large
`VALUES` list that is measurable.

If a literal `#__` has to survive into the data, bind it as a parameter — bound values are not part
of the string the scanner sees.

## Checklist

| Situation | Use |
|---|---|
| A value from a request, a form, an API | `bind()` |
| A list of values for `IN (…)` | `whereIn()` / `bindArray()` |
| A column or table name | `quoteName()` |
| A table name from configuration | `quoteName()` — the DDL helpers do not quote for you |
| A constant you wrote in the source | `quote()`, or bind it anyway |
| Binary data | `quoteBinary()` or `bind()` with `LARGE_OBJECT` |
| An `ORDER BY` direction from user input | Neither — map it through an allowlist to `ASC`/`DESC` |
