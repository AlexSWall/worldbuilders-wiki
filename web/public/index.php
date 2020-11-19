<?php

$config = require '/config/backend/' . trim(file_get_contents('/config/backend/mode.php')) . '.config.php';

$app = require '../backend/bootstrap/bootstrapper.php';

$app->run();
