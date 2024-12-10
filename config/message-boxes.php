<?php
return [
    'deployment' => [
        'data-table' => [
            // launch
            'launchItem'   => [
                'title'   => 'Launch Item',
                'content' => 'ask_launch_item',
                // constant names from defaultActions[] or closure
                'actions' => [
                    'cancel',
                    'launchItem',
                ],
            ],
            // simulate
            'simulateItem' => [
                'title'   => 'Simulate Item',
                'content' => 'ask_simulate_item',
                // constant names from defaultActions[] or closure
                'actions' => [
                    'cancel',
                    'simulateItem',
                ],
            ],
        ],

    ],
];
