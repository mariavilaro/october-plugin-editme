<?php

return [
    'plugin' => [
        'name' => 'EditMe',
        'description' => 'In-context content editor',
    ],
    'component_editme' => [
        'name' => 'EditMe Component',
        'description' => 'This component allows in-context editing.',
        'property_message' => [
            'title' => 'Message',
            'description' => 'Translation string to show or edit',
        ],
    ],
    'settings' => [
        'name' => 'Editor advanced settings',
        'description' => 'Customize global editor advanced options',
        'froala_custom_defaults' => 'Custom defaults',
        'froala_custom_defaults_comment' => 'Custom default options for Froala editor',
    ],
];