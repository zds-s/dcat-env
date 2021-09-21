<?php
/**
 * @author    : Death-Satan
 * @date      : 2021/9/20
 * @createTime: 1:24
 * @company   : Death撒旦
 * @link      https://www.cnblogs.com/death-satan
 */

namespace SaTan\Dcat\EnvHelper\Http\Repositories;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Alert;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SaTan\Dcat\EnvHelper\DcatEnvServiceProvider;

class Env implements \Dcat\Admin\Contracts\Repository
{

    /**
     * @inheritDoc
     */
    public function getKeyName()
    {
        return 'name';
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAtColumn()
    {
        return 'create_at';
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAtColumn()
    {
        return 'update_at';
    }


    protected function trans($key, $replace = [], $locale = null)
    {
        return DcatEnvServiceProvider::trans($key, $replace, $locale);
    }

    /**
     * @return false|resource|void
     */
    protected function getenv()
    {
        $env_path = base_path('.env');

        if (is_file($env_path) && is_readable($env_path)) {
            return fopen($env_path, 'r');
        } else {
            admin_exit(
                Alert::make()
                    ->title($this->trans('env.alert.errors.title'))
                    ->content($this->trans('env.alert.errors.content'))
            );
        }

    }

    /**
     * @inheritDoc
     */
    public function isSoftDeletes()
    {
        return false;
    }


    protected function getDataArray()
    {
        $env = $this->getenv();
        $data = [];
        while (!feof($env)) {
            $now_line = fgets($env);
            $now_data = [];
            //筛选注释
            if (substr($now_line, 0, 1) === '#' || substr($now_line, 0, 1) === "\n") {
                continue;
            }

            if ($name = strstr($now_line, '=', true)) {
                $now_data['name'] = $name;
                $content = substr($now_line, strpos($now_line, '=') + 1);
                //取值
                if (substr($content, 0, 1) === '#') {
                    $now_data['value'] = '';
                    $now_data['notes'] = substr($content, 0);
                } elseif (substr($content, 0, 1) === '"') {
                    $now_data['value'] = substr($content, 0, strpos($content, '"', 1) + 1);
                    $now_content = substr($content, strpos($content, '"', 1) + 1);
                    $now_data['notes'] = substr($now_content, 0, 1) === '#' ? substr($now_content, 1) : $now_content;
                } else {
                    $now_data['value'] = strpos($content, '#') ? substr($content, 0, strpos($content, '#')) : $content;
                    $now_data['notes'] = strpos($content, '#') ? substr($content, strpos($content, '#') + 1) : "\n";
                }

                $data[] = $now_data;
                continue;

            } else {
                continue;
            }
        }
        fclose($env);
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function get(Grid\Model $model)
    {
        // 获取当前页数
        $currentPage = (int)$model->getCurrentPage();
        // 获取每页显示行数
        $perPage = (int)$model->getPerPage();

        // 获取筛选参数
        $name = $model->filter()->input('name');
        $env_data = collect($this->getDataArray());
        if (!empty($name)) {
            $env_data = collect($env_data->filter(function ($data) use ($name) {
                    return (bool)strstr(strtolower($data['name']), strtolower($name));
                })->all() ?? []);
        }

        $chunks = $env_data->chunk($perPage);
        $data = [];
        $data['total'] = $env_data->count();
        $data['subjects'] = $chunks[$currentPage - 1] ?? [];
        return $model->makePaginator(
            $data['total'] ?? 0, // 传入总记录数
            $data['subjects'] ?? [] // 传入数据二维数组
        );
    }

    /**
     * @inheritDoc
     */
    public function edit(Form $form)
    {
        $env_data = collect($this->getDataArray());
        $name = $form->getKey();
        return $env_data->first(function ($data) use ($name) {
            return (bool)strstr(strtolower($data['name']), strtolower($name));
        });
    }

    /**
     * @inheritDoc
     */
    public function detail(Show $show)
    {
        $name = $show->getKey();

        $env_data = collect($this->getDataArray());
        return $env_data->first(function ($data) use ($name) {
            return (bool)strstr(strtolower($data['name']), strtolower($name));
        });

    }

    /**
     * 转换大写
     * @param $name
     * @return string
     */
    protected function getName($name)
    {
        return Str::upper(Str::replace(['.'], '_', $name));
    }

    /**
     * @inheritDoc
     */
    public function store(Form $form)
    {
        // 获取待新增的数据
        $attributes = $form->updates();
        $data = $this->getDataArray();
        $names = array_column($data, 'name');
        $name = $this->getName($attributes['name']);

        //如果已经存在
        if (in_array($name, $names)) {
            return $form->response()->error($this->trans('env.controller.store.in', compact('name')));
        }
        //如果不存在
        $str = $name . '=' . $attributes['value'] ?? '' . $attributes['notes'] ?? '' . "\n";
        //直接追加
        file_put_contents(base_path('.env'), $str, FILE_APPEND);
        return $name;
    }

    /**
     * @inheritDoc
     */
    public function updating(Form $form)
    {
        $name = $form->getKey();
        $env_data = collect($this->getDataArray());
        return $env_data->first(function ($data) use ($name) {
            return (bool)strstr(strtolower($data['name']), strtolower($name));
        });
    }

    /**
     * @inheritDoc
     */
    public function update(Form $form)
    {
        // 获取待编辑的数据
        $attributes = $form->updates();
        //获取主键
        $name = $form->getKey();
        //当前文件所在行
        $now_line = 0;
        $env_path = base_path('.env');
        //是否成功修改
        $fp = new \SplFileObject($env_path, 'r+');
        //设置最大行数
        $fp->setMaxLineLen(count(file($env_path)));
        while ($now_line < $fp->getMaxLineLen()) {
            $fp->seek($now_line);
            $content = $fp->current();
            $now_name = substr($content, 0, strpos($content, '='));
            //匹配到当前行
            if ($name === $now_name) {
                //拼接
                $update_str = $attributes['name'] . '=' . $attributes['value'] . '#' . $attributes['notes'] . "\n";
                //如果和原来的一样 就直接跳出
                if ($update_str === $content) {
                    return $form->response()->error($this->trans('env.controller.update.equal', compact('name')));
                }
                file_put_contents(
                    $env_path,
                    str_replace($content, $update_str, file_get_contents($env_path))
                );
                break;
            }
            $now_line++;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(Form $form, array $deletingData)
    {
        $env_path = base_path('.env');
        $content = file_get_contents($env_path);
        foreach ($deletingData as $datum) {
            //获取要删除的字符串
            $deleteString = $datum['name'] . '=' . $datum['value'] . '#' . $datum['notes'];
            $content = str_replace($deleteString, null, $content);
        }
        return file_put_contents($env_path, $content);
    }

    /**
     * @inheritDoc
     */
    public function deleting(Form $form)
    {
        $names = explode(',', $form->getKey());
        $env_data = collect($this->getDataArray());
        return $env_data->filter(function ($data) use ($names) {
            return in_array(Str::upper($data['name']), $names);
        })->toArray();
    }
}
