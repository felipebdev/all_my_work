<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'public_local' => [
            'driver' => 'local',
            'url' => env('APP_URL').'/uploads',
            'root' => public_path('/uploads'),
        ],

        'uploads_temp' => [
            'driver' => 'local',
            'root' => public_path('/uploads_temp'),
        ],

        'authorsProfiles' => [
            'driver' => 'local',
            'root' => public_path().'/uploads/authors_profiles',
        ],

        'images' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        'documents' => [
            'driver' => 's3',
            'key' => env('DOCUMENTS_AWS_ACCESS_KEY_ID'),
            'secret' => env('DOCUMENTS_AWS_SECRET_ACCESS_KEY'),
            'region' => env('DOCUMENTS_AWS_DEFAULT_REGION'),
            'bucket' => env('DOCUMENTS_AWS_BUCKET'),
            'url' => env('DOCUMENTS_AWS_URL'),
        ],

        'linode' => [
            'driver' => 's3',
            'key' => env('LINODE_KEY'),
            'secret' => env('LINODE_SECRET'),
            'endpoint' => env('LINODE_ENDPOINT'),
            'region' => env('LINODE_REGION'),
            'bucket' => env('LINODE_BUCKET'),
            'url' => env('LINODE_URL'),
            'visibility' => env('LINODE_VISIBILITY'),
        ],

        'panda' => [
            'driver' => 'panda',
            'email' => env('PANDA_EMAIL'),
            'password' => env('PANDA_PASSWORD'),
            'url' => env('PANDA_URL'),
        ]

    ],

];
