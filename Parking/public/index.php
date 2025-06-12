<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Find the USB drive path
$secretKeyPath = "E:/.strangeThing";

// Check if the USB drive with secret key is connected
if (!file_exists($secretKeyPath)) {
    // USB drive not connected or file not found
    die('Secret key file not found on USB drive named "Parking"');
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());