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

$settings['jwt'] = [
    'issuer' => 'covid-collaboration.biobank.ca',
    'lifetime' => 14400,  // Max lifetime in seconds

    // The private key
    'private_key' => '-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAwQB/rqEbY+CFd8gu6KVE4kU0AHjZXyOYNXKKi5+GnCe5bMU/
nDMIY4lNvbQds09sMH789DiKQO31lLBTjJTRLDcKHIZjIsITH7PF3DT7m6dulYcR
qMjBaUXb5/A1OmSF7t7UwmvUDKLZrjcVV28T4lznMunRPvr+cty34bVRrQni8HmB
tosivaBN27vWlTickid3zGqU9kKOVKkv1H9dwhZHf2a+g2lgwNXV5t7UHgHDKT6b
XGlz1NxMX9siTmQFJ6M7Cm5z+gDP/WgUQTukon/6gOZeLRs5kMbUyll2e5dQKmbA
1wjW7TYKO2ThhKleW2NE6XEZoC/K4C79y10cjwIDAQABAoIBAD8XmqXLpNiRnQ/7
MNp8rHgLh2RgHS8sw6U1PHVIQQjrM4KLOLAnPqbLS86oAs2LOSLhYG+1y/xNzeo9
ehKK05ZQp82LYP0L1JSYgCXsbBQGN/BJp0w11IRFgg3gSIY+NkpQLd0gwsEeu2po
N2HNvgJRvIK4TN75zhRecv4l9/1InLS6dMWBS+XtH8xHgtbZEHn9qD2mkGEVKYJ3
dunIfXYm0SniuVgDlI3eaMyxkso5zHXWEGx1vKdz3S19JfgNHfWgh2rC8YXfFncM
3GX6P/HbsaQUChFzLAk8QYtriBAcAZDgiyFXrW91BR1ybt0rDpU/nbCpDkIhlrtt
wGqM4gECgYEA5JJvtzkIS8SpR81omBx53mx7gkzW5wIHkqn4/Ark97m8syWncGPR
dnDwOWw0rz/UzXI8vN0bbHzhAhQuaXgkQZGF6mzGe3PfK4uFT/mNwUA643brto1n
LoR+8Yot16vg06gqA5wTd85T1b7KybI0qWivffvHfTkJzefxyOwGkV8CgYEA2Clg
SQ6Mw8R4xYNvjODMzZjJ8uC24ayo+wCFErJ6AFZNo3IsPlFWwukAriB9y3E9UnH1
Xf05RYNf/Zz/Rg/zaIxhy5sMAZh6Zal7SwiCCRgkKfghfOxnBk+rv/r+10OGGI4Q
0FOWjqQcp1xkadilgql/vSysfd7VZHAPXwaQUtECgYEAsnbrpyFuqsoYaimleu7w
8Iu/O5OHT8Mz6n2wHArdj9aD0VSbEZO+Xj38MrmbwSGTo/2IEuaInQI8JQVg35Sg
qllXOBxKNOXZ4AQFyNXOqo7d6/BURqrNX3KwMyNye6yF1Hy+oSbhxG9i4ccgSq9L
kuJb83/82HcKgqyRChHPZv0CgYA9xHz+A0lX+4FjNy6d+/Kp4Tn7zBiWHgdfSgO8
lwFjrUWcKdjYqdd39Kq8Fw67Ho2eTHAHvn8qDONWDhGnzJEKU+ryCkkA/7gh6q8P
fsvhm2NiFsRC9S5vUD7MqgU+L85Wn+nQDcKc2epSLIWI9V4+Gv8kaGqVwSILBDZw
OejtsQKBgQCN8eFR5fdx6h1isOrVKOV6oQVAuNjaB+ZUrwasbkHB8F3ED9feXn1S
y9gAWZIDg6pMUv5m84p64tL0Rh9MBKa4IIaDhJD4AlTBUPuvCw+xyti5f75j/n5w
5M9rYkp+sM6t1PFiNBYstoiRoLk2KMS2C5Mhqrz0ImwLqy31VJ1JSw==
-----END RSA PRIVATE KEY-----',

    'public_key' => '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwQB/rqEbY+CFd8gu6KVE
4kU0AHjZXyOYNXKKi5+GnCe5bMU/nDMIY4lNvbQds09sMH789DiKQO31lLBTjJTR
LDcKHIZjIsITH7PF3DT7m6dulYcRqMjBaUXb5/A1OmSF7t7UwmvUDKLZrjcVV28T
4lznMunRPvr+cty34bVRrQni8HmBtosivaBN27vWlTickid3zGqU9kKOVKkv1H9d
whZHf2a+g2lgwNXV5t7UHgHDKT6bXGlz1NxMX9siTmQFJ6M7Cm5z+gDP/WgUQTuk
on/6gOZeLRs5kMbUyll2e5dQKmbA1wjW7TYKO2ThhKleW2NE6XEZoC/K4C79y10c
jwIDAQAB
-----END PUBLIC KEY-----',

];

return $settings;
