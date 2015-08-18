<?php
/**
 * Module Name: phire-navigation
 * Author: Nick Sagona
 * Description: This is the navigation module for Phire CMS 2
 * Version: 1.0
 */
return [
    'phire-navigation' => [
        'prefix'     => 'Phire\Navigation\\',
        'src'        => __DIR__ . '/../src',
        'routes'     => include 'routes.php',
        'resources'  => include 'resources.php',
        'forms'      => include 'forms.php',
        'nav.phire'  => [
            'navigation' => [
                'name' => 'Navigation',
                'href' => '/navigation',
                'acl' => [
                    'resource'   => 'navigation',
                    'permission' => 'index'
                ],
                'attributes' => [
                    'class' => 'navigation-nav-icon'
                ]
            ]
        ],
        'models' => [
            'Phire\Navigation\Model\Navigation' => []
        ],
        'events' => [
            [
                'name'     => 'app.send',
                'action'   => 'Phire\Navigation\Event\Navigation::getNavigation',
                'priority' => 1000
            ],
            [
                'name'     => 'app.send',
                'action'   => 'Phire\Navigation\Event\Navigation::updateNavigation',
                'priority' => 1000
            ]
        ]
    ]
];
