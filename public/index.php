<?php

define('BASE_PATH', dirname(__DIR__));

$config = require BASE_PATH . '/config/' . trim(file_get_contents(BASE_PATH . '/mode.php')) . '.config.php';

$app = require BASE_PATH . '/bootstrap/bootstrapper.php';

$app->run();