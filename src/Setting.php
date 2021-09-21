<?php

namespace SaTan\Dcat\EnvHelper;

use Dcat\Admin\Extend\Setting as Form;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Alert;

class Setting extends Form
{
    public function form()
    {
        admin_exit(
            Alert::make($this->trans('env.setting.content'))->info()
        );
    }

    public function title()
    {
        return $this->trans('env.setting.title');
    }
}
