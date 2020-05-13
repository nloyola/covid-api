<?php
declare(strict_types=1);

use Dotenv\Dotenv;

error_reporting(0);
ini_set('display_errors', '0');

date_default_timezone_set('UTC');

// Load .env file
if (file_exists(__DIR__ . '/../.env')) {
  $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
  $dotenv->load();
}

if (empty(getenv('AUTH_SECRET'))) {
  throw new Error('environment variable not assigned: AUTH_SECRET');
}

$settings = [];

$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';
$settings['mode'] = getenv('MODE');

switch (getenv('APP_LOG_STREAM')) {
  case 'stdout':
    $appLogStream = 'php://stdout';
    break;
  default:
    $appLogStream = $settings['root'] . '/logs';
    break;
}

switch (getenv('APP_LOG_LEVEL')) {
  case 'debug':
    $appLogLevel = Monolog\Logger::DEBUG;
    break;
  case 'info':
    $appLogLevel = Monolog\Logger::INFO;
    break;
  case 'warning':
    $appLogLevel = Monolog\Logger::WARNING;
    break;
  case 'error':
    $appLogLevel = Monolog\Logger::ERROR;
    break;
  default:
    $appLogLevel = Monolog\Logger::INFO;
}

$settings['error_handler_middleware'] = [
  'displayErrorDetails' => getenv('APP_DEBUG') === 'true' ? true : false, // set to false in production
  'logErrors' => true,
  'logErrorDetails' => true
];

$settings['addContentLengthHeader'] = false; // Allow the web server to send the content-length header,
$settings['determineRouteBeforeAppMiddleware'] = true;
$settings['auth_secret'] = getenv('AUTH_SECRET');

// Monolog settings
$settings['logger'] = [
  'name'  => getenv('APP_NAME'),
  'path'  => $appLogStream,
  'level' => $appLogLevel,
];

$settings['router'] = [
    // Should be set only in production
    'cache_file' => '',
];


// Database connection settings
if ($settings['mode'] === 'testing') {
  $settings['db'] = [
    'driver'    => getenv('DB_CONNECTION'),
    'database'  => getenv('DB_DATABASE'),
    'prefix'    => ''
  ];
} else {
  $settings['db'] = [
    'driver'    => getenv('DB_CONNECTION'),
    'host'      => getenv('DB_HOST'),
    'port'      => getenv('DB_PORT'),
    'database'  => getenv('DB_DATABASE'),
    'username'  => getenv('DB_USERNAME'),
    'password'  => getenv('DB_PASSWORD'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
    'options' => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => true,
        // Set default fetch mode to array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Set character set
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
    ],
  ];
}

$settings['email'] = [
  'host'     => getenv('EMAIL_HOST'),
  'port'     => getenv('EMAIL_PORT'),
  'user'     => getenv('EMAIL_USER'),
  'password' => getenv('EMAIL_PASSWORD'),
];

// View settings
$settings['twig'] = [
  'path' => $settings['public'],
  // Should be set to true in production
  'cache_enabled' => true,
  'cache_path' => $settings['temp'] . '/twig-cache',
];

$settings['phinx'] = [
  'paths' => [
    'migrations' => $settings['root'] . '/db/migrations',
    'seeds' => $settings['root'] . '/db/seeds',
  ],
  'default_migration_prefix' => 'db_change_',
  'generate_migration_name' => true,
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_database' => 'local',
    'local' => [],
  ]
];

$settings['commands'] = [
  \App\Console\FakeCommand::class,
  \App\Console\RedcapCommand::class,
  \App\Console\WebAppSecret::class
];

$settings['redcap'] = [
  'api_token' => getenv('REDCAP_API_TOKEN'),
  'api_url'   => 'https://micyrn.med.ualberta.ca/api/'

];

return $settings;
