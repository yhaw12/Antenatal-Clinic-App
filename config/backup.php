<?php
return [
    'backup' => [
        'name' => env('APP_NAME', 'laravel-backup'),
        'source' => [
            'files' => [
                'include' => [base_path()],
                'exclude' => [base_path('vendor'), base_path('node_modules')],
                'follow_links' => false,
            ],
            'databases' => [
                'mysql',
            ],
        ],
        'destination' => [
            'disks' => [
                env('BACKUP_DISK', 's3'),
            ],
        ],
        'temporary_directory' => storage_path('app/backup-temp'),
    ],
    'cleanup' => [
        'strategy' => [
            'keep_daily' => 7,
            'keep_weekly' => 4,
            'keep_monthly' => 3,
        ],
    ],
];
