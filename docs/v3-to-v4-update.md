# Updating from v3 to v4

Release 4.0.0 raises the PHP requirement and removes the deprecations that 2.x and 3.x announced.

## At a glance

| | v3 (3.4.2) | v4 (4.0.0) |
|---|---|---|
| PHP | `^8.1.0` | `^8.3.0` |
| `Query\LimitableInterface` | deprecated | **removed** |
| `Query\PreparableInterface` | deprecated | **removed** |
| `DatabaseQuery::castAsChar()` | deprecated | **removed** |
| `DatabaseDriver::quoteNameStr()` | deprecated | **removed** |
| `DatabaseInterface::createQuery()` | on `DatabaseDriver` only | part of the interface |
| `getQuery(true)` | deprecated, removal in 4.0 | deprecated, removal moved to 5.0 |

## Minimum supported PHP version raised

All Framework packages now require **PHP 8.3** or newer.

The minimum database versions are unchanged:

| Server | Minimum |
|---|---|
| MySQL | 5.6 |
| PostgreSQL | 9.2.0 |
| MS SQL | 11.0.2100.60 (SQL Server 2012) |

## `LimitableInterface` and `PreparableInterface` removed

Both were deprecated in 2.0 when `QueryInterface` started extending them. Their methods —
`setLimit()`, `processLimit()`, `bind()`, `unbind()`, `bindArray()` — are now declared on
`QueryInterface` itself.

```php
// v3
class MyQuery extends DatabaseQuery implements LimitableInterface, PreparableInterface

// v4
class MyQuery extends DatabaseQuery implements QueryInterface
```

Type hints and `instanceof` checks against the old interfaces are fatal errors now:

```bash
grep -rn "LimitableInterface\|PreparableInterface" src/
```

Extending `DatabaseQuery` already gives you `QueryInterface`, so most custom query classes need no
change beyond deleting the `implements` clause.

## `DatabaseQuery::castAsChar()` removed

```php
// v3
$query->castAsChar($value);

// v4
$query->castAs('CHAR', $value);
```

`castAs()` has been available since 2.0 and takes an optional third argument for the length:

```php
$query->castAs('CHAR', $value, 10);
```

## `DatabaseDriver::quoteNameStr()` removed

```php
// v3
$this->quoteNameStr($parts);

// v4
$this->quoteNameString($name, $asSinglePart = false);
```

The method was `protected`, so this only affects driver subclasses. Note that the replacement takes
a **string**, not an array of parts — it does the splitting itself.

## `createQuery()` is part of `DatabaseInterface`

```php
public function createQuery(): QueryInterface;
```

Any class implementing `DatabaseInterface` directly — rather than extending `DatabaseDriver` —
must now provide it. If you ship a custom query class, this is the place it is returned from:

```php
public function createQuery(): QueryInterface
{
    return new MyQuery($this);
}
```

## `getQuery(true)` survives one more major

The deprecation message used to say the `$new` parameter would go in 4.0. It now says 5.0, so the
call still works — but the deprecation still fires:

```php
// deprecated
$query = $db->getQuery(true);

// use
$query = $db->createQuery();
```

`getQuery()` without an argument keeps its meaning: it returns the query last set on the driver.

## `alterDbCharacterSet()` validates its argument

```php
// v3
if ($dbName === null) {
    throw new \RuntimeException('Database name must not be null.');
}

// v4
if ($dbName === null || !is_string($dbName) || $dbName === '') {
    throw new \RuntimeException('Database name must not be null and a non empty string.');
}
```

An empty string or a non-string used to reach the server as a broken statement; it now fails early.
The documented return type also narrowed from `boolean|resource` to `boolean`.

## Dependency changes

| Package | v3 (3.4.2) | v4 (4.0.0) |
|---|---|---|
| `php` | `^8.1.0` | `^8.3.0` |
| `joomla/event` | `^3.0` | `^4.0` |
| `symfony/deprecation-contracts` | `^2\|^3` | unchanged |

Suggested and development packages moved to their 4.0 lines — `joomla/archive`, `joomla/console`,
`joomla/di`, `joomla/filesystem`, `joomla/registry`, `joomla/test`. `psr/log` moved to `^3.0.2`,
PHPUnit to `^12.0`, and `phan/phan` was replaced by `phpstan/phpstan-deprecation-rules`.

`psr/log` 3.0 matters if you use `Monitor\LoggingMonitor`: the PSR-3 interfaces gained parameter
and return types, so a custom logger written against `psr/log` 1.x needs updating.
