# Updating from v2 to v3

Release 3.0.0 raises the PHP requirement, adds two methods to `DatabaseInterface` and deprecates
`getQuery(true)`.

## At a glance

| | v2 (2.2.x) | v3 (3.0.0) |
|---|---|---|
| PHP | `^7.2.5 \| ^8.0` | `^8.1.0` |
| `DatabaseInterface::getPrefix()` | on `DatabaseDriver` only | part of the interface |
| `DatabaseInterface::quoteName()` | on `DatabaseDriver` only | part of the interface |
| `getQuery(true)` | supported | deprecated |

## Minimum supported PHP version raised

All Framework packages now require **PHP 8.1** or newer.

## `getPrefix()` and `quoteName()` added to `DatabaseInterface`

```php
public function getPrefix();
public function quoteName($name, $as = null);
```

Both existed on `DatabaseDriver` in 2.x; they are now part of the contract. Classes that extend
`DatabaseDriver` inherit them and need no change. A class implementing `DatabaseInterface` directly
— a test double, a decorator, a proxy — must add them:

```php
class LoggingDatabase implements DatabaseInterface
{
    public function getPrefix()
    {
        return $this->inner->getPrefix();
    }

    public function quoteName($name, $as = null)
    {
        return $this->inner->quoteName($name, $as);
    }
}
```

## `DatabaseDriver::getQuery(true)` deprecated

`getQuery()` does two different things depending on its argument: with `true` it returns a **new**
query object, otherwise the **last query set on the driver**. The parameter is deprecated and will
be removed in 5.0 — after that, `getQuery()` will only ever return the last query set.

```php
// deprecated
$query = $db->getQuery(true);

// use
$query = $db->createQuery();
```

The deprecation is emitted through `symfony/deprecation-contracts`, so it shows up in your
deprecation log rather than as a PHP notice.

If you maintain a driver that implements `DatabaseInterface` directly, add `createQuery()` now — it
becomes part of the interface in 4.0.

## Dependency changes

| Package | v2 (2.2.x) | v3 (3.0.0) |
|---|---|---|
| `php` | `^7.2.5 \| ^8.0` | `^8.1.0` |
| `joomla/event` | `^2.0` | `^3.0` |
| `symfony/deprecation-contracts` | `^2.1` | `^2 \| ^3` |

Suggested and development packages moved to their 3.0 lines. `joomla/coding-standards` was replaced
by `squizlabs/php_codesniffer` with the ruleset in `ruleset.xml`.
