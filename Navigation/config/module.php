<?php
/**
 * Module Name: Navigation
 * Author: Nick Sagona
 * Description: This is the navigation module for Phire CMS 2
 * Version: 1.0
 */
return [
    'Navigation' => [
        'prefix'     => 'Navigation\\',
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
            'Navigation\Model\Navigation' => []
        ],
        'events' => [
            [
                'name'     => 'app.route.pre',
                'action'   => 'Navigation\Event\Category::bootstrap',
                'priority' => 1000
            ]
        ]
    ]
];
