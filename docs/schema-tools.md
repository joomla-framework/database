# Schema tools

Introspection, XML import/export, and the two console commands built on them.

## Introspection

```php
$db->getTableList();                          // every table in the current database
$db->getTableColumns('#__articles');          // ['id' => 'int unsigned', 'title' => 'varchar', …]
$db->getTableColumns('#__articles', false);   // full column objects, not just types
$db->getTableKeys('#__articles');             // indexes
$db->getTableCreate('#__articles');           // the CREATE TABLE statement
```

`getTableColumns()` defaults to `$typeOnly = true`, which returns a name-to-type map. Pass `false`
for the driver's full column description — null-ability, default value, extra attributes — whose
shape differs between servers.

`getTableList()` returns *raw* table names, with the real prefix rather than `#__`.

## DDL helpers

```php
$db->createDatabase($options, $utf = true);   // $options needs db_name and db_user
$db->select('other_database');
$db->dropTable('#__articles', $ifExists = true);
$db->truncateTable('#__articles');
$db->renameTable('#__old', '#__new', $backup = null, $prefix = null);
```

> **These do not quote identifiers.** `renameTable()` in the MySQL drivers concatenates the names
> straight into the statement, and the rest are inconsistent about it. A table name that is a
> reserved word or contains a hyphen produces invalid SQL; a name coming from a config file, an
> XML manifest or a form field is an injection point.
>
> ```php
> $db->renameTable($db->quoteName($old), $db->quoteName($new));
> ```
>
> Quoting the argument yourself is safe — the drivers do not quote again.

There is no schema builder. Creating tables and altering columns means writing DDL by hand, with
the caveat above.

There is also no migration system: no up/down steps, no history table, no rollback. If you need
versioned schema changes, that layer is yours to build (or import) on top.

## Exporting

`DatabaseExporter` writes structure and optionally data as XML.

```php
$exporter = (new DatabaseFactory())->getExporter('mysqli', $db);

$xml = (string) $exporter
    ->from(['#__articles', '#__categories'])
    ->withStructure(true)
    ->withData(true)
    ->asXml();

file_put_contents('schema.xml', $xml);
```

| Method | Effect |
|---|---|
| `from($tables)` | One table name or an array of them |
| `withStructure($setting = true)` | Include `CREATE TABLE` information |
| `withData($setting = false)` | Include rows — **off by default** |
| `asXml()` | Select the output format (currently the only one) |
| `check()` | Validate that a driver and a table list are set |
| `setDbo($db)` | Attach the driver, if not passed to the factory |

The exporter renders on `__toString()`, which means:

```php
$xml = (string) $exporter;      // not $exporter->export()
```

**`__toString()` swallows exceptions.** A failed `check()` or a query error produces an *empty
string*, not an error. Call `check()` yourself first if you want to know:

```php
$exporter->check();             // throws on a missing driver or table list
$xml = (string) $exporter;

if ($xml === '') {
    throw new \RuntimeException('Export produced no output');
}
```

Exporters exist for `mysql`, `mysqli` and `pgsql`. Asking the factory for one on `sqlite` or
`sqlsrv` raises `Exception\UnsupportedAdapterException`.

## Importing

```php
$importer = (new DatabaseFactory())->getImporter('mysqli', $db);

$importer
    ->from(file_get_contents('schema.xml'))
    ->asXml()
    ->mergeStructure();     // create or alter tables to match the XML

$importer->importData();    // insert the rows
```

| Method | Effect |
|---|---|
| `from($source)` | XML string or a `SimpleXMLElement` |
| `asXml()` | Select the input format |
| `withStructure($setting = true)` | Include structure |
| `mergeStructure()` | Create missing tables, add/alter/drop columns and keys to match |
| `importData()` | Insert the rows from the XML |
| `check()` | Validate that a driver and a source are set |

`mergeStructure()` is destructive in the direction of the XML: a column present in the database but
absent from the file is dropped. Run it against a copy first.

> The importer processes a file that usually comes from somewhere else. Treat the XML as untrusted
> input: it names tables and column types that end up in DDL. This part of the package has not been
> audited in depth — validate the source before importing.

`Exception\UnknownTypeException` is raised when a column type in the XML has no mapping for the
target driver, which is the usual failure when moving a dump between MySQL and PostgreSQL.

## Console commands

Two `joomla/console` commands wrap the exporter and importer. They need `joomla/console`,
`joomla/filesystem` and — for the `--zip` options — `joomla/archive`.

```php
use Joomla\Console\Application;
use Joomla\Database\Command\ExportCommand;
use Joomla\Database\Command\ImportCommand;

$app = new Application();
$app->addCommand(new ExportCommand($db));
$app->addCommand(new ImportCommand($db));
$app->execute();
```

### `database:export`

| Option | Meaning |
|---|---|
| `--folder` | Where to write the files (default `.`) |
| `--table` | Export a single table instead of all of them |
| `--zip` | Write the export into a ZIP archive |

```bash
php console.php database:export --folder=./backup
php console.php database:export --table=#__articles --zip
```

One XML file is written per table, named after the table.

### `database:import`

| Option | Meaning |
|---|---|
| `--folder` | Directory holding the files to import (default `.`) |
| `--table` | Import a single table |
| `--zip` | Name of a ZIP archive to import from |

```bash
php console.php database:import --folder=./backup
```

Import applies the structure and then the data, table by table. There is no dry-run and no
transaction spanning the whole run — an error partway through leaves the database in a mixed state.
