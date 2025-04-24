<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


define('LARAVEL_START', microtime(true));

// Find the USB drive path
$secretKeyPath = "D:/.strangeThing";



$encryptedEnvPath = __DIR__ . '/../.env.enc';
$decryptedEnvPath = __DIR__ . '/../.env';

// Check if the USB drive with secret key is connected
if (file_exists($secretKeyPath)) {
    try {
        // Decrypt the .env file
        $command = sprintf(
            'openssl enc -d -aes-256-cbc -salt -in %s -out %s -pass file:"%s" -pbkdf2 -iter 100000',
            escapeshellarg($encryptedEnvPath),
            escapeshellarg($decryptedEnvPath),
            escapeshellarg($secretKeyPath)
        );

        // Execute decryption
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        // Check if decryption was successful
        if ($returnVar !== 0 || !file_exists($decryptedEnvPath)) {
            throw new Exception('Decryption failed');
        }
    } catch (Exception $e) {
        // Handle decryption errors
        die('Error decrypting .env file: ' . $e->getMessage());
    }
} else {
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

// Optional: Securely remove the decrypted .env file after use
register_shutdown_function(function () use ($decryptedEnvPath) {
    if (file_exists($decryptedEnvPath)) {
        unlink($decryptedEnvPath);
    }
});