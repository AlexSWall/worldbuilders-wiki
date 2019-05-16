<?php

define('BASE_PATH', dirname(__DIR__));

$app = null;

require BASE_PATH . '/bootstrap/app.php';

$app->run();
