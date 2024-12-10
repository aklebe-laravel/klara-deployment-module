<?php

use Modules\KlaraDeployment\app\Models\Deployment;

return [
    // class of eloquent model
    "model"     => Deployment::class,
    // update data if exists and data differ (default false)
    "update"    => true,
    // columns to check if data already exists (AND WHERE)
    "uniques"   => ["code"],
    // relations to update/create
    "relations" => [
        "res" => [
            // relation method which have to exists
            "method" => "tasks",
            // column(s) to find specific #sync_relations items below
            "columns" => "code",
            // delete items if not listed here (default: false)
            "delete" => false,
        ],
    ],
    // data rows itself
    "data"      => [
        [
            "is_enabled"      => true,
            "rating"          => 9900,
            "code"            => 'module.klara-deployment::clear-cache',
            "label"           => 'Clear Caches',
            "description"     => 'Clearing redis, view and livewire caches.',
            "var_list"        => [],
            "#sync_relations" => [
                "res" => [
                    'artisan::deploy-env:cc',
                ]
            ]
        ],
        [
            "is_enabled"      => true,
            "code"            => 'module.klara-deployment::build-frontend',
            "rating"          => 9800,
            "label"           => 'Build Frontend',
            "description"     => '1) Cache clear process 2) build mercy assets 3) build frontend by npm build',
            "var_list"        => [],
            "#sync_relations" => [
                "res" => [
                    'artisan::deploy-env:build-frontend',
                ]
            ]
        ],
        [
            "is_enabled"      => true,
            "code"            => 'module.klara-deployment::system-update',
            "rating"          => 9700,
            "label"           => 'System Update',
            "description"     => 'Runs composer update, npm update, npm build, migrate, clearing caches, build frontend. Inclusive maintenance mode.',
            "var_list"        => [],
            "#sync_relations" => [
                "res" => [
                    'artisan::deploy-env:system-update',
                ]
            ]
        ],
        [
            "is_enabled"      => true,
            "code"            => 'module.klara-deployment::terraform',
            "rating"          => 9500,
            "label"           => 'Terraform',
            "description"     => '1) Migrate 2) Module Terraforming',
            "var_list"        => [],
            "#sync_relations" => [
                "res" => [
                    'artisan::migrate',
                    'artisan::deploy-env:terraform-modules',
                ]
            ]
        ],
        [
            "is_enabled"      => true,
            "code"            => 'module.klara-deployment::require-dependencies',
            "label"           => 'Require Dependencies',
            "description"     => 'Require all modules and all themes already installed and additional registered in config mercy-dependencies. Running deploy-env:system-update after task all was success.',
            "var_list"        => [],
            "#sync_relations" => [
                "res" => [
                    'artisan::deploy-env:require-dependencies',
                ]
            ]
        ],
        [
            "is_enabled"      => true,
            "code"            => 'module.klara-deployment::module-info',
            "label"           => 'Modules information',
            "description"     => 'Info about modules.',
            "var_list"        => [],
            "#sync_relations" => [
                "res" => [
                    'artisan::deploy-env:module-info',
                ]
            ]
        ],
    ],
];
