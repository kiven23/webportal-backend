<?php
require 'custom_db/db.php';
$connections = [
    'sqlite' => [
        'driver' => 'sqlite',
        'database' => env('DB_DATABASE', database_path('database.sqlite')),
        'prefix' => '',
    ],

    'mysql' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'database' => 'webportal_8',
        'username' => 'root',
        'password' => 'M15@2dwin0n7y',
       
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],
    'diapidata' => [
        'driver' => 'mysql',
        'host' => '192.168.1.3',
        'port' => '3306',
        'database' => 'production',
        'username' => 'stevefox',
        'password' => 'M15@2dwin0n7y',
       
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],
    'pgsql' => [
        'driver' => 'pgsql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '5432'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
        'sslmode' => 'prefer',
    ],
    'sqlsrv' => [
        'driver' => env('DB_CONNECTION_SECOND'),
        'url' => env('DATABASE_URL_SECOND'),
        'host' => env('DB_HOST_SECOND'),
        'port' => env('DB_PORT_SECOND'),
        'database' => env('DB_DATABASE_SECOND'),
        'username' => env('DB_USERNAME_SECOND'),
        'password' => env('DB_PASSWORD_SECOND'),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        
    ],
    'sqlsrv2' => [
        'driver' => env('DB_CONNECTION_THIRD'),
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST_THIRD', 'localhost'),
        'port' => env('DB_PORT_THIRD', '1433'),
        'database' => env('DB_DATABASE_THIRD', 'forge'),
        'username' => env('DB_USERNAME_THIRD', 'forge'),
        'password' => env('DB_PASSWORD_THIRD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
    ],
    'sqlsrv3' => [
        'driver' => env('DB_CONNECTION_FOURTH'),
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST_FOURTH', 'localhost'),
        'port' => env('DB_PORT_FOURTH', '1433'),
        'database' => env('DB_DATABASE_FOURTH', 'forge'),
        'username' => env('DB_USERNAME_FOURTH', 'forge'),
        'password' => env('DB_PASSWORD_FOURTH', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
    ],
    'sqlsrvLIVESAP' => [
        'driver' => env('DB_CONNECTION_LIVE'),
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST_LIVE', 'localhost'),
        'port' => env('DB_PORT_LIVE', '1433'),
        'database' => env('DB_DATABASE_LIVE', 'forge'),
        'username' => env('DB_USERNAME_LIVE', 'forge'),
        'password' => env('DB_PASSWORD_LIVE', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
    ],
    'mysql-qportal' => [
        'driver' => env('DB_CONNECTION_SERVER2'),
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST_SERVER2', 'localhost'),
        'port' => env('DB_PORT_SERVER2', '1433'),
        'database' => env('DB_DATABASE_SERVER2', 'forge'),
        'username' => env('DB_USERNAME_SERVER2', 'forge'),
        'password' => env('DB_PASSWORD_SERVER2', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
    ],
    'mysql-qportal-test' => [
        'driver' => env('DB_CONNECTION_SERVER2_TEST'),
        'url' => env('DATABASE_URL_TEST'),
        'host' => env('DB_HOST_SERVER2_TEST', 'localhost'),
        'port' => env('DB_PORT_SERVER2_TEST', '1433'),
        'database' => env('DB_DATABASE_SERVER2_TEST', 'forge'),
        'username' => env('DB_USERNAME_SERVER2_TEST', 'forge'),
        'password' => env('DB_PASSWORD_SERVER2_TEST', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
    ],
];

foreach ($database_list as  $dbs) {
    $entryname = $dbs['entryname'];
    $connections[$entryname] =  [
        'driver' => $dbs['connection'],
        'url' => $dbs['server'],
        'host' => $dbs['server'],
        'port' => $dbs['port'],
        'database' => $dbs['dbname'],
        'username' => $dbs['username'],
        'password' => $dbs['password'],
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        
    ];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => $connections,
    //[

    //     'sqlite' => [
    //         'driver' => 'sqlite',
    //         'database' => env('DB_DATABASE', database_path('database.sqlite')),
    //         'prefix' => '',
    //     ],

        // 'mysql' => [
        //     'driver' => 'mysql',
        //     'host' => 'localhost',
        //     'port' => '3306',
        //     'database' => 'webportal_8',
        //     'username' => 'root',
        //     'password' => 'M15@2dwin0n7y',
           
    //         'unix_socket' => env('DB_SOCKET', ''),
    //         'charset' => 'utf8mb4',
    //         'collation' => 'utf8mb4_unicode_ci',
    //         'prefix' => '',
    //         'strict' => true,
    //         'engine' => null,
    //     ],
    //     'pgsql' => [
    //         'driver' => 'pgsql',
    //         'host' => env('DB_HOST', '127.0.0.1'),
    //         'port' => env('DB_PORT', '5432'),
    //         'database' => env('DB_DATABASE', 'forge'),
    //         'username' => env('DB_USERNAME', 'forge'),
    //         'password' => env('DB_PASSWORD', ''),
    //         'charset' => 'utf8',
    //         'prefix' => '',
    //         'schema' => 'public',
    //         'sslmode' => 'prefer',
    //     ],
    //     'sqlsrv' => [
    //         'driver' => env('DB_CONNECTION_SECOND'),
    //         'url' => env('DATABASE_URL_SECOND'),
    //         'host' => env('DB_HOST_SECOND'),
    //         'port' => env('DB_PORT_SECOND'),
    //         'database' => env('DB_DATABASE_SECOND'),
    //         'username' => env('DB_USERNAME_SECOND'),
    //         'password' => env('DB_PASSWORD_SECOND'),
    //         'charset' => 'utf8',
    //         'prefix' => '',
    //         'prefix_indexes' => true,
    //     ],
    //     'sqlsrv2' => [
    //         'driver' => env('DB_CONNECTION_THIRD'),
    //         'url' => env('DATABASE_URL'),
    //         'host' => env('DB_HOST_THIRD', 'localhost'),
    //         'port' => env('DB_PORT_THIRD', '1433'),
    //         'database' => env('DB_DATABASE_THIRD', 'forge'),
    //         'username' => env('DB_USERNAME_THIRD', 'forge'),
    //         'password' => env('DB_PASSWORD_THIRD', ''),
    //         'charset' => 'utf8',
    //         'prefix' => '',
    //         'prefix_indexes' => true,
    //     ],
    //     'sqlsrv3' => [
    //         'driver' => env('DB_CONNECTION_FOURTH'),
    //         'url' => env('DATABASE_URL'),
    //         'host' => env('DB_HOST_FOURTH', 'localhost'),
    //         'port' => env('DB_PORT_FOURTH', '1433'),
    //         'database' => env('DB_DATABASE_FOURTH', 'forge'),
    //         'username' => env('DB_USERNAME_FOURTH', 'forge'),
    //         'password' => env('DB_PASSWORD_FOURTH', ''),
    //         'charset' => 'utf8',
    //         'prefix' => '',
    //         'prefix_indexes' => true,
    //     ],








    // ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
