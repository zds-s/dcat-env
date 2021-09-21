<?php

namespace SaTan\Dcat\EnvHelper;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;

class DcatEnvServiceProvider extends ServiceProvider
{
    protected $menu = [
        [
            'title' => 'edit env',
            'icon' => 'fa-envira',
            'uri' => 'satan/env'
        ]
    ];

    public function register()
    {
        //
    }


    public function settingForm()
    {
        return new Setting($this);
    }
}
