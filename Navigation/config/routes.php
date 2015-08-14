<?php

return [
    APP_URI => [
        '/navigation[/]' => [
            'controller' => 'Navigation\Controller\IndexController',
            'action'     => 'index',
            'acl'        => [
                'resource'   => 'navigation',
                'permission' => 'index'
            ]
        ],
        '/navigation/add[/]' => [
            'controller' => 'Navigation\Controller\IndexController',
            'action'     => 'add',
            'acl'        => [
                'resource'   => 'navigation',
                'permission' => 'add'
            ]
        ],
        '/navigation/edit/:id' => [
            'controller' => 'Navigation\Controller\IndexController',
            'action'     => 'edit',
            'acl'        => [
                'resource'   => 'navigation',
                'permission' => 'edit'
            ]
        ],
        '/navigation/remove[/]' => [
            'controller' => 'Navigation\Controller\IndexController',
            'action'     => 'remove',
            'acl'        => [
                'resource'   => 'navigation',
                'permission' => 'remove'
            ]
        ]
    ]
];
