# Connections and drivers

## Building a driver

`DatabaseFactory::getDriver()` is the entry point:

```php
use Joomla\Database\DatabaseFactory;

$db = (new DatabaseFactory())->getDriver('mysqli', $options);
```

```php
public function getDriver(string $name = 'mysqli', array $options = []): DatabaseInterface
```

The name is stripped of anything outside `[A-Za-z0-9_.-]` and then turned into a class name —
`mysqli` becomes `Joomla\Database\Mysqli\MysqliDriver`. If that class does not exist, or its PHP
extension is missing, the factory raises `Exception\UnsupportedAdapterException`.

> The driver name becomes part of a class name. If it comes from a configuration file that an
> installer or a form writes, validate it against a list of the names you support rather than
> passing it straight through.

Connecting is lazy: the constructor only stores the options. The first call that needs the server
opens the connection, or you can force it with `$db->connect()`.

```php
$db->connected();       // bool
$db->disconnect();
```

## Options

Common to every driver:

| Key | Default | Meaning |
|---|---|---|
| `database` | `null` | Database (schema) name |
| `prefix` | `''` | Replaces `#__` in every statement |
| `select` | `true` | Select the database right after connecting |
| `monitor` | — | A `QueryMonitorInterface` instance |
| `factory` | the calling factory | `DatabaseFactory` used for queries, iterators, importers |

MySQL and MySQLi additionally accept:

| Key | Meaning |
|---|---|
| `host` | Host name, optionally `host:port` |
| `port` | Port number |
| `socket` | Unix socket path, used instead of host/port |
| `user`, `password` | Credentials |
| `sqlModes` | Array of SQL modes to set after connecting |
| `ssl` | Array with `enable`, `cipher`, `ca`, `capath`, `key`, `cert`, `verify_server_cert` |

```php
$db = (new DatabaseFactory())->getDriver('mysqli', [
    'host'     => 'db.internal',
    'port'     => 3306,
    'user'     => 'app',
    'password' => $password,
    'database' => 'app',
    'prefix'   => 'app_',
    'sqlModes' => ['STRICT_TRANS_TABLES', 'ERROR_FOR_DIVISION_BY_ZERO'],
    'ssl'      => [
        'enable'             => true,
        'ca'                 => '/etc/ssl/certs/db-ca.pem',
        'verify_server_cert' => true,
    ],
]);
```

PostgreSQL, SQLite and SQL Server take the subset that applies to them; SQLite uses `database` as
the file path and ignores host, port and credentials.

## Checking what you connected to

```php
$db->getName();                       // 'mysqli'
$db->getServerType();                 // 'mysql', 'postgresql', 'mssql', 'sqlite'
$db->getVersion();
$db->isMinimumVersion();              // against the driver's declared minimum
$db->getCollation();
$db->getConnectionCollation();
$db->getConnectionEncryption();       // '' when the connection is not encrypted
$db->isConnectionEncryptionSupported();
$db->hasUtfSupport();
```

`getConnectionEncryption()` is the practical way to confirm TLS is actually in use — enabling `ssl`
in the options does not by itself guarantee the server accepted it.

## Registering with a DI container

`Service\DatabaseProvider` wires the package into `joomla/di`:

```php
use Joomla\DI\Container;
use Joomla\Database\Service\DatabaseProvider;

$container = (new Container())
    ->registerServiceProvider(new DatabaseProvider());
```

It expects a `config` service holding a `Joomla\Registry\Registry` with a `database` section:

```php
$container->share('config', fn () => new Registry([
    'database' => [
        'driver'   => 'mysqli',
        'host'     => 'localhost',
        'user'     => 'app',
        'password' => $password,
        'database' => 'app',
        'prefix'   => 'app_',
    ],
]));
```

The provider shares `DatabaseDriver::class` (aliased to `DatabaseInterface::class`) and
`DatabaseFactory::class`.

### Consuming the driver

Classes that need a driver can use `DatabaseAwareInterface` and `DatabaseAwareTrait`:

```php
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;

class ArticleRepository implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    public function find(int $id): ?object
    {
        $db    = $this->getDatabase();     // throws DatabaseNotFoundException when unset
        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__articles'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        return $db->setQuery($query)->loadObject();
    }
}
```

`getDatabase()` raises `Exception\DatabaseNotFoundException` rather than returning `null`, so a
missing `setDatabase()` fails loudly at the point of use.

## Connection events

When a dispatcher is attached, the driver emits two events:

| Constant | Event name | When |
|---|---|---|
| `DatabaseEvents::POST_CONNECT` | `onAfterConnect` | After the connection is opened |
| `DatabaseEvents::POST_DISCONNECT` | `onAfterDisconnect` | After it is closed |

Both carry an `Event\ConnectionEvent` with a `getDriver()` accessor.

```php
use Joomla\Database\DatabaseEvents;
use Joomla\Database\Event\ConnectionEvent;

$dispatcher->addListener(DatabaseEvents::POST_CONNECT, function (ConnectionEvent $event) {
    $event->getDriver()->setQuery("SET time_zone = '+00:00'")->execute();
});

$db->setDispatcher($dispatcher);
```

The events are read-only notifications — you cannot veto or alter the connection from a listener.

## Observing queries

A query monitor sees every statement the driver runs. Pass one as the `monitor` option or set it
later:

```php
$db->setMonitor($monitor);
```

`QueryMonitorInterface` has two methods:

```php
public function startQuery(string $sql, ?array $boundParams = null): void;
public function stopQuery(): void;
```

Three implementations ship with the package:

| Class | Purpose |
|---|---|
| `Monitor\DebugMonitor` | Collects SQL, bound parameters, timings, memory usage and call stacks |
| `Monitor\LoggingMonitor` | Writes each query to a PSR-3 logger at debug level |
| `Monitor\ChainedMonitor` | Runs several monitors in turn |

```php
use Joomla\Database\Monitor\ChainedMonitor;
use Joomla\Database\Monitor\DebugMonitor;
use Joomla\Database\Monitor\LoggingMonitor;

$debug = new DebugMonitor();

$logging = new LoggingMonitor();
$logging->setLogger($logger);           // PSR-3, via LoggerAwareInterface

$chain = new ChainedMonitor();
$chain->addMonitor($debug);
$chain->addMonitor($logging);

$db->setMonitor($chain);

// … later …
foreach ($debug->getLogs() as $i => $sql) {
    printf("%.4fs  %s\n", $debug->getTimings()[$i * 2 + 1] - $debug->getTimings()[$i * 2], $sql);
}
```

`DebugMonitor::getTimings()` returns start and stop values in one flat array — two entries per
query — which is why the index arithmetic above is needed. `getMemoryLogs()` follows the same
shape. There is no bundled monitor that aggregates statistics or flags slow queries; that is left
to you.

Note that a monitor receives the SQL *after* prefix replacement, together with the bound
parameters. If you log it, treat the parameter array as sensitive.

## Transactions

```php
$db->transactionStart();
$db->transactionCommit();
$db->transactionRollback();
```

Each takes `$asSavepoint = false`. With `true`, a nested call issues a `SAVEPOINT SP_<depth>`
instead of a new transaction, and the matching commit or rollback targets that savepoint:

```php
$db->transactionStart();

$db->transactionStart(true);        // SAVEPOINT SP_1
try {
    $db->setQuery($risky)->execute();
    $db->transactionCommit(true);   // RELEASE SAVEPOINT SP_1
} catch (ExecutionFailureException $e) {
    $db->transactionRollback(true); // ROLLBACK TO SAVEPOINT SP_1
}

$db->transactionCommit();
```

Support is not uniform: MySQLi and the PDO drivers implement savepoints, SQLite's behaviour depends
on the PDO layer, and the SQL Server driver handles nesting differently. Test the nesting depth you
actually rely on against the driver you deploy with.

DDL statements cause an implicit commit on MySQL, so a `CREATE TABLE` inside a transaction ends it.

## Locking

```php
$db->lockTable('#__articles');
// …
$db->unlockTables();
```

Locking is a MySQL-family concept; on other drivers these are no-ops or unsupported.
