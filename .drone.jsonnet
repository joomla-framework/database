local volumes = [
    {
        name: 'composer-cache',
        path: '/tmp/composer-cache',
    },
];

local hostvolumes = [
    {
        name: 'composer-cache',
        host: { path: '/tmp/composer-cache' },
    },
];

local install_sqlsrv(phpversion) = {
    name: 'Install MS SQL Server',
    image: 'joomlaprojects/docker-images:php' + phpversion,
    commands: [
        'apt-get update',
        'apt-get install -y software-properties-common gnupg',
        'add-apt-repository -y -s ppa:ondrej/php',
        'apt-get update',
        'apt-get install -y php-dev gcc musl-dev make',
        'pecl install sqlsrv pdo_sqlsrv && docker-php-ext-enable sqlsrv pdo_sqlsrv',
    ]
};

local composer(phpversion, params) = {
    name: 'composer',
    image: 'joomlaprojects/docker-images:php' + phpversion,
    volumes: volumes,
    commands: [
        'php -v',
        'sleep 20',
        'composer update ' + params,
    ],
};

local phpunit(phpversion, phpunit_config) = {
    name: 'PHPUnit',
    image: 'joomlaprojects/docker-images:php' + phpversion,
    [if phpversion == '8.2' then 'failure']: 'ignore',
    commands: ['vendor/bin/phpunit --configuration ' + phpunit_config],
};

local pipeline_sqlite(phpversion, driver, params) = {
    kind: 'pipeline',
    name: 'PHP ' + phpversion + ' with SQLite (' + driver + ')',
    environment: { DB: driver },
    volumes: hostvolumes,
    steps: [
        composer(phpversion, params),
        phpunit(phpversion, './.travis/phpunit.' + driver + '.xml'),
    ],
};

local pipeline_mysql(phpversion, driver, dbversion, params) = {
    kind: 'pipeline',
    name: 'PHP ' + phpversion + ' with MySQL ' + dbversion + ' (' + driver + ')',
    environment: { DB: driver },
    volumes: hostvolumes,
    steps: [
        composer(phpversion, params),
        phpunit(phpversion, './.travis/phpunit.' + driver + '.xml'),
    ],
    services: [
        {
            name: 'mysql',
            image: 'mysql:' + dbversion,
            environment: {
                MYSQL_ALLOW_EMPTY_PASSWORD: 'yes',
                MYSQL_DATABASE: 'joomla_ut',
                MYSQL_ROOT_PASSWORD: '',
            },
        },
    ],
};

local pipeline_mariadb(phpversion, driver, dbversion, params) = {
    kind: 'pipeline',
    name: 'PHP ' + phpversion + ' with MariaDB ' + dbversion + ' (' + driver + ')',
    environment: { DB: driver },
    volumes: hostvolumes,
    steps: [
        composer(phpversion, params),
        phpunit(phpversion, './.travis/phpunit.' + driver + '.xml'),
    ],
    services: [
        {
            name: 'mariadb',
            image: 'mariadb:' + dbversion,
            environment: {
                MARIADB_ALLOW_EMPTY_ROOT_PASSWORD: 'yes',
                MARIADB_DATABASE: 'joomla_ut',
                MARIADB_ROOT_PASSWORD: '',
            },
        },
    ],
};

local pipeline_postgres(phpversion, driver, dbversion, params) = {
    kind: 'pipeline',
    name: 'PHP ' + phpversion + ' with PostgreSQL ' + dbversion + ' (' + driver + ')',
    environment: { DB: driver },
    volumes: hostvolumes,
    steps: [
        composer(phpversion, params),
        phpunit(phpversion, './.travis/phpunit.' + driver + '.xml'),
    ],
    services: [
        {
            name: 'postgresql',
            image: 'postgres:' + dbversion,
            environment: {
                POSTGRES_HOST_AUTH_METHOD: 'trust',
                POSTGRES_PASSWORD: '',
                POSTGRES_USER: 'postgres',
            },
            ports: [
                {
                    container: 5432,
                    host: 5432,
                },
            ],
            commands: [
                "psql -U postgres -c 'create database joomla_ut;'",
                "psql -U postgres -d joomla_ut -a -f Tests/Stubs/Schema/pgsql.sql",
            ]
        },
    ],
};

local pipeline_sqlsrv(phpversion, driver, dbversion, params) = {
    kind: 'pipeline',
    name: 'PHP ' + phpversion + ' with MS SQL Server ' + dbversion + ' (' + driver + ')',
    environment: { DB: driver },
    volumes: hostvolumes,
    steps: [
        install_sqlsrv(phpversion),
        composer(phpversion, params),
        phpunit(phpversion, './.travis/phpunit.' + driver + '.xml'),
    ],
    services: [
        {
            name: 'mssql-server',
            image: 'mcr.microsoft.com/mssql/server:' + dbversion,
            environment: {
                ACCEPT_EULA: 'Y',
                SA_PASSWORD: 'JoomlaFramework123',
            },
            ports: [
                {
                    container: 1433,
                    host: 1433,
                },
            ],
        },
    ],
};

[
    {
        kind: 'pipeline',
        name: 'Codequality',
        volumes: hostvolumes,
        steps: [
            {
                name: 'composer',
                image: 'joomlaprojects/docker-images:php7.4',
                volumes: volumes,
                commands: [
                    'php -v',
                    'composer update',
                    'composer require phpmd/phpmd phpstan/phpstan',
                ],
            },
            {
                name: 'phpcs',
                image: 'joomlaprojects/docker-images:php7.4',
                depends: [ 'composer' ],
                commands: [
                    'vendor/bin/phpcs --config-set installed_paths vendor/joomla/coding-standards',
                    'vendor/bin/phpcs --standard=ruleset.xml src/',
                ],
            },
            {
                name: 'phpmd',
                image: 'joomlaprojects/docker-images:php7.4',
                depends: [ 'composer' ],
                failure: 'ignore',
                commands: [
                    'vendor/bin/phpmd src text cleancode',
                    'vendor/bin/phpmd src text codesize',
                    'vendor/bin/phpmd src text controversial',
                    'vendor/bin/phpmd src text design',
                    'vendor/bin/phpmd src text unusedcode',
                ],
            },
            {
                name: 'phpstan',
                image: 'joomlaprojects/docker-images:php7.4',
                depends: [ 'composer' ],
                failure: 'ignore',
                commands: [
                    'vendor/bin/phpstan analyse src',
                ],
            },
            {
                name: 'phploc',
                image: 'joomlaprojects/docker-images:php7.4',
                depends: [ 'composer' ],
                failure: 'ignore',
                commands: [
                    'phploc src',
                ],
            },
            {
                name: 'phpcpd',
                image: 'joomlaprojects/docker-images:php7.4',
                depends: [ 'composer' ],
                failure: 'ignore',
                commands: [
                    'phpcpd src',
                ],
            },
        ],
    },
#    pipeline_sqlite('7.2', 'sqlite', '--prefer-stable --prefer-lowest'),
#    pipeline_sqlite('7.3', 'sqlite', '--prefer-stable'),
#    pipeline_sqlite('7.4', 'sqlite', '--prefer-stable'),
#    pipeline_sqlite('8.0', 'sqlite', '--prefer-stable'),
#    pipeline_sqlite('8.1', 'sqlite', '--prefer-stable'),
#    pipeline_sqlite('8.2', 'sqlite', '--prefer-stable --ignore-platform-reqs'),
#    pipeline_mysql('7.2', 'mysql.docker', '5.7', '--prefer-stable --prefer-lowest'),
#    pipeline_mysql('7.3', 'mysql.docker', '5.7', '--prefer-stable'),
#    pipeline_mysql('7.4', 'mysql.docker', '5.7', '--prefer-stable'),
#    pipeline_mysql('8.0', 'mysql.docker', '5.7', '--prefer-stable'),
#    pipeline_mysql('8.1', 'mysql.docker', '5.7', '--prefer-stable'),
#    pipeline_mysql('8.2', 'mysql.docker', '5.7', '--prefer-stable --ignore-platform-reqs'),
#    pipeline_mysql('7.3', 'mysql.docker', '8.0', '--prefer-stable'),
#    pipeline_mysql('7.4', 'mysql.docker', '8.0', '--prefer-stable'),
#    pipeline_mysql('8.0', 'mysql.docker', '8.0', '--prefer-stable'),
#    pipeline_mysql('8.1', 'mysql.docker', '8.0', '--prefer-stable'),
#    pipeline_mysql('8.2', 'mysql.docker', '8.0', '--prefer-stable --ignore-platform-reqs'),
#    pipeline_mysql('7.2', 'mysqli.docker', '5.7', '--prefer-stable --prefer-lowest'),
#    pipeline_mysql('7.3', 'mysqli.docker', '5.7', '--prefer-stable'),
#    pipeline_mysql('7.4', 'mysqli.docker', '5.7', '--prefer-stable'),
#    pipeline_mysql('8.0', 'mysqli.docker', '5.7', '--prefer-stable'),
#    pipeline_mysql('8.1', 'mysqli.docker', '5.7', '--prefer-stable'),
#    pipeline_mysql('8.2', 'mysqli.docker', '5.7', '--prefer-stable --ignore-platform-reqs'),
#    pipeline_mysql('7.3', 'mysqli.docker', '8.0', '--prefer-stable'),
#    pipeline_mysql('7.4', 'mysqli.docker', '8.0', '--prefer-stable'),
#    pipeline_mysql('8.0', 'mysqli.docker', '8.0', '--prefer-stable'),
#    pipeline_mysql('8.1', 'mysqli.docker', '8.0', '--prefer-stable'),
#    pipeline_mysql('8.2', 'mysqli.docker', '8.0', '--prefer-stable --ignore-platform-reqs'),
#    pipeline_mariadb('7.2', 'mariadb', '10.0', '--prefer-stable --prefer-lowest'),
#    pipeline_mariadb('7.2', 'mariadb', '10.1', '--prefer-stable --prefer-lowest'),
#    pipeline_mariadb('7.2', 'mariadb', '10.2', '--prefer-stable --prefer-lowest'),
#    pipeline_mariadb('7.3', 'mariadb', '10.2', '--prefer-stable'),
#    pipeline_mariadb('7.4', 'mariadb', '10.2', '--prefer-stable'),
#    pipeline_mariadb('8.0', 'mariadb', '10.2', '--prefer-stable'),
#    pipeline_mariadb('8.1', 'mariadb', '10.2', '--prefer-stable'),
#    pipeline_mariadb('8.2', 'mariadb', '10.2', '--prefer-stable --ignore-platform-reqs'),
#    pipeline_postgres('7.2', 'pgsql', '9.4', '--prefer-stable --prefer-lowest'),
#    pipeline_postgres('7.2', 'pgsql', '9.5', '--prefer-stable --prefer-lowest'),
#    pipeline_postgres('7.3', 'pgsql', '9.6', '--prefer-stable'),
#    pipeline_postgres('7.3', 'pgsql', '10', '--prefer-stable'),
#    pipeline_postgres('7.4', 'pgsql', '10', '--prefer-stable'),
#    pipeline_postgres('8.0', 'pgsql', '10', '--prefer-stable'),
#    pipeline_postgres('8.1', 'pgsql', '10', '--prefer-stable'),
#    pipeline_postgres('8.2', 'pgsql', '10', '--prefer-stable --ignore-platform-reqs'),
#    pipeline_postgres('7.3', 'pgsql', '11', '--prefer-stable'),
#    pipeline_postgres('7.4', 'pgsql', '11', '--prefer-stable'),
#    pipeline_postgres('8.0', 'pgsql', '11', '--prefer-stable'),
#    pipeline_postgres('8.1', 'pgsql', '11', '--prefer-stable'),
#    pipeline_postgres('8.2', 'pgsql', '11', '--prefer-stable --ignore-platform-reqs'),
#    pipeline_sqlsrv('7.2', 'sqlsrv', '2017-latest', '--prefer-stable --prefer-lowest'),
#    pipeline_sqlsrv('7.3', 'sqlsrv', '2017-latest', '--prefer-stable'),
    pipeline_sqlsrv('7.4', 'sqlsrv', '2017-latest', '--prefer-stable'),
#    pipeline_sqlsrv('8.0', 'sqlsrv', '2017-latest', '--prefer-stable'),
#    pipeline_sqlsrv('8.1', 'sqlsrv', '2017-latest', '--prefer-stable'),
#    pipeline_sqlsrv('8.2', 'sqlsrv', '2017-latest', '--prefer-stable --ignore-platform-reqs'),
]
