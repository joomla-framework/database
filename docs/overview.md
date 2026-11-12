# Overview

The Database package is the framework's data-access layer: drivers for MySQL, PostgreSQL, SQLite
and SQL Server behind one interface, a query builder, prepared statements, schema import/export and
query monitoring.

```bash
composer require joomla/database
```

## Getting started

```php
use Joomla\Database\DatabaseFactory;

$db = (new DatabaseFactory())->getDriver('mysqli', [
    'host'     => 'localhost',
    'user'     => 'app',
    'password' => $password,
    'database' => 'app',
    'prefix'   => 'app_',
]);

$query = $db->createQuery()
    ->select(['id', 'title'])
    ->from($db->quoteName('#__articles'))
    ->where($db->quoteName('state') . ' = :state')
    ->bind(':state', $state, \Joomla\Database\ParameterType::INTEGER);

$articles = $db->setQuery($query)->loadObjectList();
```

Three things happen in that snippet, and each has its own page:

1. `getDriver()` builds a connection — [Connections and drivers](connections.md)
2. `createQuery()` returns a builder — [Query builder](query-builder.md)
3. `bind()` registers a prepared-statement parameter — [Parameters and escaping](parameters-and-escaping.md)

The result is then read with one of the `load*` methods — [Reading results](results.md).

## The moving parts

| Class / interface | Role |
|---|---|
| `DatabaseInterface` | What every driver offers: connect, query, load, quote, schema introspection |
| `DatabaseDriver` | The abstract base every driver extends |
| `DatabaseFactory` | Builds drivers, queries, exporters, importers and iterators |
| `QueryInterface` / `DatabaseQuery` | The query builder |
| `StatementInterface` | A prepared statement |
| `ParameterType` | Abstract parameter types (`INTEGER`, `STRING`, `BOOLEAN`, `NULL`, `LARGE_OBJECT`) |
| `FetchMode` | How a row is shaped when fetched |
| `DatabaseIterator` | Row-by-row traversal without loading everything |
| `DatabaseExporter` / `DatabaseImporter` | Schema and data as XML |
| `QueryMonitorInterface` | Observe every query the driver runs |

## Supported drivers

| Name for `getDriver()` | Class | Notes |
|---|---|---|
| `mysqli` | `Mysqli\MysqliDriver` | The default |
| `mysql` | `Mysql\MysqlDriver` | MySQL over PDO |
| `pgsql` | `Pgsql\PgsqlDriver` | PostgreSQL over PDO |
| `sqlite` | `Sqlite\SqliteDriver` | SQLite over PDO |
| `sqlsrv` | `Sqlsrv\SqlsrvDriver` | Microsoft SQL Server |
| `sqlazure` | `Sqlazure\SqlazureDriver` | Azure SQL, extends `sqlsrv` |

Only `mysqli`, `mysql`, `pgsql` and `sqlite` ship exporters and importers.

## Writing

```php
// Insert an object; the third argument names the auto-increment column
$row = (object) ['title' => 'Hello', 'state' => 1];
$db->insertObject('#__articles', $row, 'id');
echo $row->id;      // filled in by insertObject()

// Update by primary key
$row->title = 'Hello again';
$db->updateObject('#__articles', $row, 'id');

// Or build the statement
$query = $db->createQuery()
    ->update($db->quoteName('#__articles'))
    ->set($db->quoteName('state') . ' = :state')
    ->where($db->quoteName('id') . ' = :id')
    ->bind(':state', $state, ParameterType::INTEGER)
    ->bind(':id', $id, ParameterType::INTEGER);

$db->setQuery($query)->execute();
```

## Transactions

```php
$db->transactionStart();

try {
    $db->setQuery($first)->execute();
    $db->setQuery($second)->execute();
    $db->transactionCommit();
} catch (\Joomla\Database\Exception\ExecutionFailureException $e) {
    $db->transactionRollback();
    throw $e;
}
```

All three methods take an `$asSavepoint` flag for nesting, but savepoint support differs between
drivers — see [Connections and drivers](connections.md#transactions).

## Errors

| Exception | Raised when |
|---|---|
| `Exception\ConnectionFailureException` | The connection could not be opened |
| `Exception\ExecutionFailureException` | The server rejected the statement |
| `Exception\PrepareStatementFailureException` | The statement could not be prepared |
| `Exception\UnsupportedAdapterException` | No driver class for the requested name, or the extension is missing |
| `Exception\UnknownTypeException` | An importer/exporter met a column type it cannot map |
| `Exception\QueryTypeAlreadyDefinedException` | A query object was asked to change from e.g. SELECT to UPDATE |
| `Exception\DatabaseNotFoundException` | A `DatabaseAwareInterface` consumer was used without a driver |

All of them extend or implement PHP's `RuntimeException`.

## Continue reading

* [Connections and drivers](connections.md) — factory, options, DI, events, monitors, transactions
* [Query builder](query-builder.md) — `select`, `join`, `where`, unions, limits
* [Parameters and escaping](parameters-and-escaping.md) — `bind`, `quote`, `quoteName`, the prefix
* [Reading results](results.md) — `load*`, fetch modes, iterators
* [Schema tools](schema-tools.md) — introspection, importer, exporter, console commands

## Things to know before you build on this

**`bind()` takes its value by reference.**

```php
public function bind($key, &$value, $dataType = ParameterType::STRING, $length = 0, $driverOptions = [])
```

You cannot bind a literal, and a variable changed between `bind()` and `execute()` changes the
query. Inside a loop, every `bind()` of the loop variable ends up pointing at the same slot. There
is no `bindValue()`. See [Parameters and escaping](parameters-and-escaping.md#binding-by-reference)
for the working patterns.

**`ParameterType` constants are strings, not PDO integers.** `ParameterType::INTEGER` is `'int'`,
not `1`. Passing a `PDO::PARAM_*` constant instead silently binds the wrong type.

**`PdoDriver::escape()` does not consult the connection.** It is a hand-written escaper —
`str_replace("'", "''")` plus `addcslashes()` — that never calls `PDO::quote()`. On a multi-byte
connection charset such as GBK or SJIS the escaping can be defeated, and under MySQL's
`NO_BACKSLASH_ESCAPES` mode it inserts literal backslashes into your data. Bind parameters instead
of quoting; reserve `quote()` for values you control.

**DDL methods do not quote identifiers.** `renameTable()` in the MySQL drivers concatenates the
table names straight into the statement, and the other DDL helpers are inconsistent. A table name
that is a reserved word or contains a hyphen produces invalid SQL; a table name from a config file
or form is an injection point. Quote it yourself with `quoteName()` before passing it in.

**`quoteName()` treats every dot as a separator.** `quoteName('a.b')` becomes two quoted parts. A
column whose name genuinely contains a dot cannot be expressed — the `$asSinglePart` parameter that
would do it is not reachable from `quoteName()`. Identifier injection through embedded quotes is
*not* possible: the closing quote is doubled before the split.

**`replacePrefix()` runs a hand-written SQL scanner on every query.** It walks the whole statement
character by character to replace `#__`, tracking quote and escape state. It diverges from the
server on dollar-quoting, on `#` inside backtick identifiers and inside comments, and it costs time
proportional to the query length on every execution.

**`escape()` returns mixed types.** All three implementations return an `int` for integer input and
a string otherwise, despite the `string` docblock. `SqlsrvDriver::escape()` goes further and emits
SQL syntax (`' + CHAR(0) + N'`) for NUL bytes, so its result is only valid when embedded directly
between two single quotes.

**No migrations and no schema builder.** There is an XML import/export format, but no versioned
migration system with up/down, history table and rollback, and no programmatic way to define
tables. Also missing: read/write splitting, replica routing, connection pooling, a query cache, and
any bundled `QueryMonitorInterface` implementation that aggregates timings.
