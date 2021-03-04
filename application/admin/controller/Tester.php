<?php

namespace app\admin\controller;

use think\Controller;

/**
 * 测试
 * @internal
 */
class Tester extends Controller
{

    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 后台首页
     */
    public function index()
    {

    }

}
