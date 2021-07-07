<?php
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        'siteUrl' => 'http://camptrak.studentlifeportal.com',

        // View settings
        'view' => [
            'template_path' => __DIR__ . '/templates',
            'twig' => [
                'cache' => __DIR__ . '/../cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../log/app.log',
        ],

        // database settings
        'db' => [
            'host' => 'localhost',
            'dbname' => 'gciapi',
            'user' => 'gciapi',
            'pass' => 'GCI.api.135',
        ],
    ],
];
