<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        // $hooks = config('addons.hooks');
        // $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        // $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        // Config::parse($addonComposerCfg, "json", "composer");
        // $config = Config::get("composer");
        // $addonVersion = isset($config['version']) ? $config['version'] : '未知';
        // $this->view->assign([
        //     'addonversion'     => $addonVersion,
        //     'uploadmode'       => $uploadmode
        // ]);

        return $this->view->fetch();
    }

}
