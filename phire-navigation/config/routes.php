<?php

return [
    APP_URI => [
        '/navigation[/]' => [
            'controller' => 'Phire\Navigation\Controller\IndexController',
            'action'     => 'index',
            'acl'        => [
                'resource'   => 'navigation',
                'permission' => 'index'
            ]
        ],
        '/navigation/add[/]' => [
            'controller' => 'Phire\Navigation\Controller\IndexController',
            'action'     => 'add',
            'acl'        => [
                'resource'   => 'navigation',
                'permission' => 'add'
            ]
        ],
        '/navigation/edit/:id' => [
            'controller' => 'Phire\Navigation\Controller\IndexController',
            'action'     => 'edit',
            'acl'        => [
                'resource'   => 'navigation',
                'permission' => 'edit'
            ]
        ],
        '/navigation/manage/:id' => [
            'controller' => 'Phire\Navigation\Controller\IndexController',
            'action'     => 'manage',
            'acl'        => [
                'resource'   => 'navigation',
                'permission' => 'manage'
            ]
        ],
        '/navigation/remove[/]' => [
            'controller' => 'Phire\Navigation\Controller\IndexController',
            'action'     => 'remove',
            'acl'        => [
                'resource'   => 'navigation',
                'permission' => 'remove'
            ]
        ]
    ]
];
