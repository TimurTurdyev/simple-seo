<?php

declare(strict_types=1);

return [

    'meta' => [
        'title' => [
            'default' => null,
            'suffix' => null,
        ],
        'description' => [
            'default' => null,
        ],
    ],

    'open_graph' => [
        'site_name' => null,
        'type' => 'website',
        'locale' => null,
    ],

    'twitter' => [
        // One of: summary, summary_large_image, app, player. Null renders nothing
        // by default because X falls back to Open Graph tags.
        'card' => null,
        'site' => null,
    ],

];
