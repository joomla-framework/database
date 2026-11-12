# Reading results

Set the query on the driver, then pick the `load*` method whose shape matches what you need. Each
one executes the statement, reads the result and frees it.

```php
$db->setQuery($query);
```

## The load methods

| Method | Returns |
|---|---|
| `loadResult()` | The first column of the first row, or `null` |
| `loadRow()` | The first row as a numerically indexed array, or `null` |
| `loadAssoc()` | The first row as an associative array, or `null` |
| `loadObject($class = stdClass::class)` | The first row as an object, or `null` |
| `loadColumn($offset = 0)` | One column from every row, as a flat array |
| `loadRowList($key = null)` | Every row as a numeric array |
| `loadAssocList($key = null, $column = null)` | Every row as an associative array |
| `loadObjectList($key = '', $class = stdClass::class)` | Every row as an object |

```php
$count = $db->setQuery('SELECT COUNT(*) FROM #__articles')->loadResult();

$titles = $db->setQuery(
    $db->createQuery()->select('title')->from($db->quoteName('#__articles'))
)->loadColumn();

$article = $db->setQuery($query)->loadObject();
```

An empty result set gives `null` from the single-row methods and `[]` from the list methods.

### Keying a list

The `$key` argument of the list methods names a column to index the result by:

```php
$articles = $db->setQuery($query)->loadObjectList('id');

echo $articles[42]->title;
```

Rows sharing a key value overwrite each other — the last one wins — so use a column that is
actually unique.

`loadAssocList()` takes a second argument that reduces each row to a single column, which turns the
pair into a lookup map:

```php
$titlesById = $db->setQuery($query)->loadAssocList('id', 'title');
// [42 => 'Hello', 43 => 'World']
```

### Hydrating into your own class

```php
$articles = $db->setQuery($query)->loadObjectList('id', Article::class);
```

The class is instantiated by the underlying fetch, so properties are assigned **before** the
constructor runs. A constructor with required arguments will not work; give the class a
no-argument constructor, or map from `stdClass` yourself.

## Row counts

```php
$db->getAffectedRows();     // rows changed by the last INSERT/UPDATE/DELETE
$db->getNumRows();          // rows in the last result set
$db->insertid();            // last auto-increment value
```

`getNumRows()` is not reliable on every driver for buffered vs. unbuffered result sets; if you need
a count, ask the server for one with `SELECT COUNT(*)`.

`getCount()` returns the number of queries the driver has executed since it was created — a
diagnostic counter, not a row count. It is easy to reach for by mistake.

## Iterating instead of loading

`load*List()` builds the whole result set in memory. For a large table, use the iterator:

```php
$db->setQuery($query);

foreach ($db->getIterator() as $row) {
    // $row is a stdClass, one at a time
}
```

```php
public function getIterator($column = null, $class = \stdClass::class)
```

`$column` names a property to use as the iteration key; `$class` is the row class, with the same
constructor caveat as above.

`DatabaseIterator` implements `Iterator` and `Countable`:

```php
$iterator = $db->setQuery($query)->getIterator();

echo \count($iterator);
```

Two things to be aware of:

* The iterator takes ownership of the statement. Calling `setQuery()` again on the driver while
  iterating breaks it — finish the loop first.
* It is forward-only. `rewind()` after the first pass does not restart the result set on most
  drivers.

## Fetch modes

`FetchMode` mirrors PDO's constants and is used where a statement is driven directly:

| Constant | Value | Row shape |
|---|---|---|
| `FetchMode::ASSOCIATIVE` | 2 | Associative array |
| `FetchMode::NUMERIC` | 3 | Numeric array |
| `FetchMode::MIXED` | 4 | Both |
| `FetchMode::STANDARD_OBJECT` | 5 | `stdClass` |
| `FetchMode::COLUMN` | 7 | A single column value |
| `FetchMode::CUSTOM_OBJECT` | 8 | An instance of a named class |

`FetchOrientation` covers the scroll direction for cursors that support it (`NEXT`, `PRIOR`,
`FIRST`, `LAST`, `ABSOLUTE`, `RELATIVE`).

Most application code never touches these — the `load*` methods pick the right mode. They matter
when you work with a `StatementInterface` yourself:

```php
$statement = $db->prepareStatement((string) $query);
$statement->execute();

while ($row = $statement->fetch(FetchMode::ASSOCIATIVE)) {
    // …
}

$statement->closeCursor();
```

`prepareStatement()` is protected on `DatabaseDriver`, so this route is for driver subclasses
rather than application code.

## Freeing results

The `load*` methods free the result themselves, and `setQuery()` frees whatever was still open
before it stores the new query. `freeResult()` is protected, so there is nothing to call from
application code — but the ordering matters:

```php
$db->setQuery($first)->execute();
$db->setQuery($second)->loadObjectList();   // the first result is released here
```

Holding a large unbuffered result open keeps the connection busy, and an outstanding iterator is
the one case that is not cleaned up for you — finish or discard it before reusing the driver.
