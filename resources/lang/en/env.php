<?php
/**
 * @author    : Death-Satan
 * @date      : 2021/9/20
 * @createTime: 0:23
 * @company   : Death撒旦
 * @link      https://www.cnblogs.com/death-satan
 */
return [
    'setting' => [
        'title' => 'tips',
        'content' => 'There is no configuration item for this plug-in'
    ],
    'content' => [
        'title' => 'Edit env file',
        'description' => 'Quick [edit / add] env file configuration'
    ],
    'grid' => [
        'name' => 'Configuration item',
        'value' => 'Configuration value',
        'notes' => 'notes'
    ],
    'alert' => [
        'errors' => [
            'title' => 'Errors',
            'content' => 'Sorry, env file not found. Or insufficient permissions'
        ]
    ],
    'controller' => [
        'store' => [
            'in' => 'Configuration :name already exists'
        ],
        'update' => [
            'equal' => 'The configuration item :name has not changed'
        ]
    ]
];
