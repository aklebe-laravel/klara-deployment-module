<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Find all artisan commands and write it into the tasks.
 */
$items = [];
/** @var Command $artisan */
foreach (\Artisan::all() as $name => $artisan) {
    $items[] = [
        "is_enabled"   => true,
        "code"         => 'artisan::'.$name,
        "label"        => 'Artisan command: "'.$name.'"',
        "description"  => $artisan->getDescription(),
        "command_list" => [
            [
                "cmd"  => 'process:exec',
                "line" => 'php artisan '.$name,
                'options' => ['--no-interaction']
            ]
        ],
    ];
}

return [
    // class of eloquent model
    "model"     => \Modules\KlaraDeployment\Models\DeploymentTask::class,
    // update data if exists and data differ (default false)
    "update"    => true,
    // columns to check if data already exists (AND WHERE)
    "uniques"   => ["code"],
    // relations to update/create
    "relations" => [],
    // data rows itself
    "data"      => $items,
];
