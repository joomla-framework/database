<?php

include __DIR__ . '/get_phpunit_env.php';

$database = 'MS SQL Server';
$dsn      = 'sqlsrv:Server==' . JOOMLA_TEST_DB_HOST . ',' . JOOMLA_TEST_DB_PORT;

include __DIR__ . '/wait.pdo.php';
