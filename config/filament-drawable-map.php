<?php

// config for DiegoBas/FilamentDrawableMap
return [
    'providers' => [
        'google' => [
            'key' => env('GOOGLE_MAPS_API_KEY', null),
        ],
    ],
    'location' => [
        'latitude' => 39.500676,
        'longitude' => -0.439357,
    ],
];
