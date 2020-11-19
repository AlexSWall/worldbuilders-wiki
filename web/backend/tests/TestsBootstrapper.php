<?php

define('BASE_PATH', dirname(__DIR__));

$config = require BASE_PATH . '/config/tests.config.php';

$app = require BASE_PATH . '/bootstrap/bootstrapper.php';

return $app;