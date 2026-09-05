<?php

return [

    /*
    |--------------------------------------------------------------------------
    | V2 Architecture Dependency Rules
    |--------------------------------------------------------------------------
    |
    | Direction:
    |
    | Modules -> Shared
    | Modules -> Core
    | Core    -> Shared
    | Shared  -> nothing
    |
    | Modules must not depend on other Modules.
    |
    */

    'layers' => [
        'modules' => 'app/Modules',
        'core' => 'app/Core',
        'shared' => 'app/Shared',
        'support' => 'app/Support',
    ],

    'rules' => [

        'Modules' => [
            'allowed' => [
                'App\\Shared',
                'App\\Core',
                'App\\Support',
            ],
            'forbidden' => [
                'App\\Modules\\*',
            ],
        ],

        'Core' => [
            'allowed' => [
                'App\\Shared',
                'App\\Support',
            ],
            'forbidden' => [
                'App\\Modules\\*',
            ],
        ],

        'Shared' => [
            'allowed' => [],
            'forbidden' => [
                'App\\Modules\\*',
                'App\\Core\\*',
            ],
        ],
    ],

];
