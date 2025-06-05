<?php
// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// API Configuration
define('API_KEY', getenv('OPENAI_API_KEY') ?: '');
if (empty(API_KEY)) {
    die('Error: OPENAI_API_KEY is not set in .env file');
}

define('API_MODEL', getenv('OPENAI_MODEL') ?: 'gpt-3.5-turbo');
define('API_TEMPERATURE', getenv('OPENAI_TEMPERATURE') ?: 0.7);
define('API_MAX_TOKENS', getenv('OPENAI_MAX_TOKENS') ?: 1000);

// Application Configuration
define('APP_NAME', 'AI Recipe Generator');
define('APP_VERSION', '1.0.0');
define('DEBUG_MODE', getenv('DEBUG_MODE') ?: false);

// Error handling
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}