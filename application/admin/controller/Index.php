<?php

namespace app\admin\controller;

use app\admin\model\AdminLog;
use app\admin\model\AdminNav;

use app\common\controller\Backend;
use think\Config;
use think\Hook;
use think\Validate;

/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{

    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
    }

    /**
     * 后台首页
     */
    public function index()
    {
        //左侧菜单
        // list($menulist, $navlist, $fixedmenu, $referermenu) = $this->auth->getSidebar([
        //     'dashboard' => 'hot',
        //     'addon'     => ['new', 'red', 'badge'],
        //     'auth/rule' => '菜单',
        //     'general'   => ['new', 'purple'],
        // ], $this->view->site['fixedpage']);
        // $action = $this->request->request('action');
        // if ($this->request->isPost()) {
        //     if ($action == 'refreshmenu') {
        //         $this->success('', null, ['menulist' => $menulist, 'navlist' => $navlist]);
        //     }
        // }

        $adminNav = new AdminNav();
        $menuArr = $adminNav->getMenuTree('level','ord,id');
        $subMenu = reset($menuArr)['children'];
        $menu = json_encode($subMenu);
        $this->view->assign('menu', $menu);
        // $this->view->assign('menulist', $menulist);
        // $this->view->assign('navlist', $navlist);
        // $this->view->assign('fixedmenu', $fixedmenu);
        // $this->view->assign('referermenu', $referermenu);
        $this->view->assign('title', '后台管理中心');
        return $this->view->fetch();
    }

    /**
     * 管理员登录
     */
    public function login()
    {
        $url = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin()) {
            $this->success('你已经登录，无需重复登录', $url);
        }
        if ($this->request->isPost()) {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $keeplogin = $this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                '__token__' => 'require|token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                '__token__' => $token,
            ];
            if (Config::get('fastadmin.login_captcha')) {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [], ['username' => '用户名', 'password' => '密码', 'captcha' => '验证码']);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }
            AdminLog::setTitle('登录');
            $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);
            if ($result === true) {
                Hook::listen('admin_login_after', $this->request);
                $this->success('登录成功!', $url, ['url' => $url, 'id' => $this->auth->id, 'username' => $username, 'avatar' => $this->auth->avatar]);
            } else {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : '用户名或密码不正确';
                $this->error($msg, $url, ['token' => $this->request->token()]);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            $this->redirect($url);
        }
        $background = Config::get('fastadmin.login_background');
        $background = stripos($background, 'http') === 0 ? $background : config('site.cdnurl') . $background;
        $this->view->assign('background', $background);
        $this->view->assign('title', '登录');
        Hook::listen('admin_login_init', $this->request);
        return $this->view->fetch();
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        Hook::listen('admin_logout_after', $this->request);
        $this->success('退出成功!', 'index/login');
    }

}
