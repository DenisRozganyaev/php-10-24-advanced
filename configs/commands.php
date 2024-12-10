<?php
return [
    'setup' => [
        [
            'command' => 'migration:create',
            'description' => 'Create an empty migration file',
            'arguments' => [
                [
                    'name' => 'name',
                    'required' => true,
                    'description' => 'Migration file name'
                ]
            ]
        ],
        [
            'command' => 'migration:run',
            'description' => 'Run all migrations',
            'arguments' => []
        ],
        [
            'command' => 'migration:rollback',
            'description' => 'Rollback migrations',
            'arguments' => []
        ],
        [
            'command' => 'db:seed',
            'description' => 'Run database seeds',
        ],
    ],
    'commands' => [
        'migration:create' => \App\Commands\Migrations\Create::class,
        'migration:run' => \App\Commands\Migrations\Run::class,
        'migration:rollback' => \App\Commands\Migrations\Rollback::class,
        'db:seed' => \App\Commands\Seed::class,
    ],
];
