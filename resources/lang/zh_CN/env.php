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
        'title' => '温馨提示',
        'content' => '此插件没有配置项'
    ],
    'content' => [
        'title' => '编辑env文件',
        'description' => '快速[编辑/新增]env文件配置'
    ],
    'grid' => [
        'name' => '配置项',
        'value' => '配置值',
        'notes' => '注释'
    ],
    'alert' => [
        'errors' => [
            'title' => '错误',
            'content' => '抱歉,env文件未找到.或者权限不足'
        ]
    ],
    'controller' => [
        'store' => [
            'in' => '配置 :name 已存在'
        ],
        'update' => [
            'equal' => '配置项 :name 未更改'
        ]
    ]
];
