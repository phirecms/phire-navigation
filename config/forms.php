<?php
/**
 * phire-navigation form configuration
 */
return [
    'Phire\Navigation\Form\Navigation' => [
        [
            'submit' => [
                'type'       => 'submit',
                'value'      => 'Save',
                'attributes' => [
                    'class'  => 'save-btn wide'
                ]
            ],
            'create_nav_from' => [
                'type'  => 'select',
                'label' => 'Create From',
                'value' => [
                    '----' => '-----'
                ]
            ],
            'on_class' => [
                'type'       => 'text',
                'label'      => 'On Class',
                'attributes' => [
                    'size'  => 17
                ]
            ],
            'off_class' => [
                'type'       => 'text',
                'label'      => 'Off Class',
                'attributes' => [
                    'size'  => 17
                ]
            ],
            'indent' => [
                'type'       => 'text',
                'label'      => 'Indent',
                'attributes' => [
                    'size'  => 2,
                    'value' => 0
                ]
            ],
            'id' => [
                'type'  => 'hidden',
                'value' => 0
            ]
        ],
        [
            'title' => [
                'type'       => 'text',
                'label'      => 'Title',
                'required'   => true,
                'attributes' => [
                    'size'   => 60,
                    'style'  => 'width: 99.5%'
                ]
            ]
        ],
        [
            'top_node' => [
                'type'       => 'text',
                'label'      => '&nbsp;',
                'attributes' => [
                    'size'        => 15,
                    'placeholder' => 'Top Node'
                ]
            ],
            'top_id' => [
                'type'       => 'text',
                'attributes' => [
                    'size'        => 15,
                    'placeholder' => 'ID'
                ]
            ],
            'top_class' => [
                'type'       => 'text',
                'attributes' => [
                    'size'        => 15,
                    'placeholder' => 'Class'
                ]
            ],
            'top_attributes' => [
                'type'       => 'text',
                'attributes' => [
                    'size'        => 40,
                    'placeholder' => 'Attributes'
                ]
            ],
            'parent_node' => [
                'type'       => 'text',
                'label'      => '&nbsp;',
                'attributes' => [
                    'size'        => 15,
                    'placeholder' => 'Parent Node'
                ]
            ],
            'parent_id' => [
                'type'       => 'text',
                'attributes' => [
                    'size'        => 15,
                    'placeholder' => 'ID'
                ]
            ],
            'parent_class' => [
                'type'       => 'text',
                'attributes' => [
                    'size'        => 15,
                    'placeholder' => 'Class'
                ]
            ],
            'parent_attributes' => [
                'type'       => 'text',
                'attributes' => [
                    'size'        => 40,
                    'placeholder' => 'Attributes'
                ]
            ],
            'child_node' => [
                'type'       => 'text',
                'label'      => '&nbsp;',
                'attributes' => [
                    'size'        => 15,
                    'placeholder' => 'Child Node'
                ]
            ],
            'child_id' => [
                'type'       => 'text',
                'attributes' => [
                    'size'        => 15,
                    'placeholder' => 'ID'
                ]
            ],
            'child_class' => [
                'type'       => 'text',
                'attributes' => [
                    'size'        => 15,
                    'placeholder' => 'Class'
                ]
            ],
            'child_attributes' => [
                'type'       => 'text',
                'attributes' => [
                    'size'        => 40,
                    'placeholder' => 'Attributes'
                ]
            ]
        ]
    ]
];
