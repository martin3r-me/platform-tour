<?php

return [
    'key'         => 'tour',
    'title'       => 'Regie',
    'description' => 'Geführte Touren / Onboarding-Walkthroughs (Presenter-getrieben, teilbar per Link).',
    'version'     => '1.0.0',

    'routing' => [
        'prefix'     => 'tour',
        'middleware' => ['web', 'auth'],
    ],

    'guard' => 'web',

    'navigation' => [
        'route' => 'tour.dashboard',
        'icon'  => 'heroicon-o-film',
        'order' => 48,
    ],

    'sidebar' => [
        [
            'group' => 'Allgemein',
            'items' => [
                ['label' => 'Dashboard', 'route' => 'tour.dashboard', 'icon' => 'heroicon-o-home'],
                ['label' => 'Touren', 'route' => 'tour.tours.index', 'icon' => 'heroicon-o-film'],
            ],
        ],
    ],
];
