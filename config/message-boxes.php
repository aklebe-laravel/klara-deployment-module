<?php
return [
    'deployment' => [
        'data-table' => [
            // launch
            'launch-item'   => [
                'title'   => 'Launch Item',
                'content' => 'ask_launch_item',
                // constant names from defaultActions[] or closure
                'actions' => [
                    'cancel',
                    'launch-item',
                ],
            ],
            // simulate
            'simulate-item' => [
                'title'   => 'Simulate Item',
                'content' => 'ask_simulate_item',
                // constant names from defaultActions[] or closure
                'actions' => [
                    'cancel',
                    'simulate-item',
                ],
            ],
        ],

    ],
];
