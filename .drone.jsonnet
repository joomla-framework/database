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

local composer(phpversion, params) = {
    name: 'composer',
    image: 'joomlaprojects/docker-images:php' + phpversion,
    volumes: volumes,
    commands: [
        'php -v',
        'composer update ' + params,
    ],
};

local dbimage = {
    pgsql: 'postgresql',
    sqlite: 'sqlite',
    mysql: 'mysql',
    mysqli: 'mysql',
    mariadb: 'mariadb',
    sqlsrv: 'microsoft/mssql-server-linux',
};

local dbinstall(phpversion, dbtype, dbversion) = {
    name: 'Database Installation',
    image: 'joomlaprojects/docker-images:php' + phpversion,
    commands: [
        if dbtype == 'sqlite' then "echo 'SQLite "+ dbversion + "'",
        if dbtype == 'pgsql' then "echo 'PostgreSQL "+ dbversion + "'",
        if dbtype == 'pgsql' then "docker run -d --name postgres -e POSTGRES_HOST_AUTH_METHOD=trust -p 5433:5432 postgres:" + dbversion,
        if dbtype == 'pgsql' then "docker exec -i postgres bash <<< 'until pg_isready -U postgres > /dev/null 2>&1 ; do sleep 1; done'",
        if dbtype == 'mysql' then "echo 'MySQL "+ dbversion + "'",
        if dbtype == 'mysql' then "echo -e \"[mysqld]\ndefault_authentication_plugin=mysql_native_password\" >/tmp/mysql-auth.cnf",
        if dbtype == 'mysql' then "docker run -d -e MYSQL_ALLOW_EMPTY_PASSWORD=yes -e MYSQL_DATABASE=joomla_ut -v /tmp/mysql-auth.cnf:/etc/mysql/conf.d/auth.cnf:ro -p 33306:3306 --name mysql mysql:" + dbversion,
        if dbtype == 'mysqli' then "echo 'MySQL "+ dbversion + "'",
        if dbtype == 'mysqli' then "echo -e \"[mysqld]\ndefault_authentication_plugin=mysql_native_password\" >/tmp/mysql-auth.cnf",
        if dbtype == 'mysqli' then "docker run -d -e MYSQL_ALLOW_EMPTY_PASSWORD=yes -e MYSQL_DATABASE=joomla_ut -v /tmp/mysql-auth.cnf:/etc/mysql/conf.d/auth.cnf:ro -p 33306:3306 --name mysql mysql:" + dbversion,
        if dbtype == 'mariadb' then "echo 'MariaDB "+ dbversion + "'",
        if dbtype == 'sqlsrv' then "echo 'MS SQL Server "+ dbversion + "'",
        if dbtype == 'sqlsrv' then  "curl https://packages.microsoft.com/keys/microsoft.asc | sudo apt-key add -",
        if dbtype == 'sqlsrv' then  "curl https://packages.microsoft.com/config/ubuntu/14.04/prod.list | sudo tee /etc/apt/sources.list.d/mssql.list",
        if dbtype == 'sqlsrv' then  "sudo apt-get update",
        if dbtype == 'sqlsrv' then  "ACCEPT_EULA=Y",
        if dbtype == 'sqlsrv' then  "sudo apt-get install -qy msodbcsql17 mssql-tools unixodbc libssl1.0.0",
        if dbtype == 'sqlsrv' then  "sudo docker run -e 'ACCEPT_EULA=Y' -e 'SA_PASSWORD=JoomlaFramework123' -p 127.0.0.1:1433:1433 --name db -d microsoft/mssql-server-linux:2017-latest",
        if dbtype == 'sqlsrv' then  "retries=10",
        if dbtype == 'sqlsrv' then  "until (echo quit | /opt/mssql-tools/bin/sqlcmd -S 127.0.0.1 -l 1 -U sa -P JoomlaFramework123 &> /dev/null)",
        if dbtype == 'sqlsrv' then  "do",
        if dbtype == 'sqlsrv' then  "if [[ \"$retries\" -le 0 ]]; then",
        if dbtype == 'sqlsrv' then  "echo SQL Server did not start",
        if dbtype == 'sqlsrv' then  "exit 1",
        if dbtype == 'sqlsrv' then  "fi",
        if dbtype == 'sqlsrv' then  "retries=$((retries - 1))",
        if dbtype == 'sqlsrv' then  "sleep 2s",
        if dbtype == 'sqlsrv' then  "done",
    ],
};

local phpunit(phpversion, dbtype) = {
    name: 'PHPUnit',
    image: 'joomlaprojects/docker-images:php' + phpversion,
    [if phpversion == '8.2' then 'failure']: 'ignore',
    commands: ['vendor/bin/phpunit --configuration ./.travis/phpunit.' + dbtype + '.xml'],
};

local pipeline(name, phpversion, dbtype, dbversion, params) = {
    kind: 'pipeline',
    name: 'PHP ' + name + ' ' + dbtype + ' ' + dbversion,
    volumes: hostvolumes,
    steps: [
        composer(phpversion, params),
        dbinstall(phpversion, dbtype, dbversion),
        phpunit(phpversion, dbtype),
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
    pipeline('7.2 lowest', '7.2', 'mysqli', '5.7', '--prefer-stable --prefer-lowest'),
    pipeline('7.2', '7.2', 'mysqli', '5.7', '--prefer-stable'),
    pipeline('7.3', '7.3', 'mysqli', '5.7', '--prefer-stable'),
    pipeline('7.4', '7.4', 'mariadb', '10.0', '--prefer-stable'),
    pipeline('7.4', '7.4', 'mariadb', '10.2', '--prefer-stable'),
    pipeline('7.4', '7.4', 'mysql', '5.7', '--prefer-stable'),
    pipeline('7.4', '7.4', 'mysqli', '5.7', '--prefer-stable'),
    pipeline('7.4', '7.4', 'sqlite', '3', '--prefer-stable'),
    pipeline('7.4', '7.4', 'sqlsrv', '5.8.0', '--prefer-stable'),
    pipeline('8.0', '8.0', 'mysqli', '5.7', '--prefer-stable'),
    pipeline('8.0', '8.0', 'mysqli', '8.0', '--prefer-stable'),
    pipeline('8.1', '8.1', 'mariadb', '10.0', '--prefer-stable'),
    pipeline('8.1', '8.1', 'mariadb', '10.2', '--prefer-stable'),
    pipeline('8.1', '8.1', 'mysql', '5.7', '--prefer-stable'),
    pipeline('8.1', '8.1', 'mysqli', '5.7', '--prefer-stable'),
    pipeline('8.1', '8.1', 'mysqli', '8.0', '--prefer-stable'),
    pipeline('8.1', '8.1', 'sqlsrv', '5.8.0', '--prefer-stable'),
    pipeline('8.2', '8.2', 'mysqli', '5.7', '--prefer-stable --ignore-platform-reqs'),
]
