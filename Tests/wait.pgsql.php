<?php

include __DIR__ . '/get_phpunit_env.php';

$database = 'PostgreSQL';
$dsn       = 'pgsql:host=' . JOOMLA_TEST_DB_HOST . ' port=' . JOOMLA_TEST_DB_PORT;

include __DIR__ . '/wait.pdo.php';
