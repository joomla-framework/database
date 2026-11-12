# Joomla Database Package — Documentation

A database abstraction layer: drivers for MySQL, PostgreSQL, SQLite and SQL Server behind one
interface, a portable query builder, prepared statements, schema import/export and query
monitoring.

## Guide

* [Overview](overview.md) — getting started, the moving parts, transactions, errors, and the caveats
* [Connections and drivers](connections.md) — factory, options, DI, connection events, monitors
* [Query builder](query-builder.md) — SELECT, joins, WHERE, unions, limits, portable SQL fragments
* [Parameters and escaping](parameters-and-escaping.md) — `bind()`, `ParameterType`, `quoteName()`, the `#__` prefix
* [Reading results](results.md) — the `load*` methods, iterators, fetch modes
* [Schema tools](schema-tools.md) — introspection, importer, exporter, console commands

## Upgrading

* [Updating from v1 to v2](v1-to-v2-update.md) — PHP 7.2, prepared statements, query monitors,
  query builder changes
* [Updating from v2 to v3](v2-to-v3-update.md) — PHP 8.1, `DatabaseInterface` additions,
  `getQuery(true)` deprecated
* [Updating from v3 to v4](v3-to-v4-update.md) — PHP 8.3, deprecated interfaces and methods removed
