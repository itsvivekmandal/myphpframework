<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

require_once __DIR__ . '/../routes/api.php';

Router::dispatch($_SERVER['REQUEST_URI']);

// die('working');
