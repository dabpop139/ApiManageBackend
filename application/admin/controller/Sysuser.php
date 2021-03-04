<?php

namespace app\admin\controller;

use think\Validate;
use app\common\controller\Backend;
use app\admin\model\Admin;
use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use fast\Random;

/**
 * 管理员管理
 *
 * @icon fa fa-user
 */
class Sysuser extends Backend
{

    public function index()
    {
        $model = new Admin();
        $datas = $model->order('id')->select()->toArray();
        foreach ($datas as &$row) {
            $row['group'] = [];
            $userAccGroup = AuthGroupAccess::where(['uid'=>$row['id']])->order('group_id')->select()->toArray();
            $gids = [];
            foreach ($userAccGroup as $item) {
                $gids[] = $item['group_id'];
            }
            $uGroup = AuthGroup::where(['id'=>['in', $gids]])->select();
            foreach ($uGroup as $item) {
                $row['group'][] = $item['name'];
            }
            $row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
            $row['logintime'] = date('Y-m-d H:i:s', $row['logintime']);
        }
        unset($row);

        $this->view->assign([
            'datas' => $datas,
        ]);
        return $this->view->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $params = [];
            $params['username'] = input('username');
            $params['nickname'] = input('nickname');
            $params['email']    = input('email');
            $params['password'] = input('password');
            $params['status']   = input('status');

            if (!Validate::is($params['email'], "email")) {
                $this->error('Email格式错误');
            }
            if (!isset($params['password']) || $params['password']=='') {
                $this->error('密码不能为空');
            }
            if (!Validate::is($params['password'], "/^[\S]{6,16}$/")) {
                $this->error('密码不合法');
            }
            $params['salt'] = Random::alnum();
            $params['password'] = md5(md5($params['password']) . $params['salt']);

            $exist = Admin::where('email', $params['email'])->where(['id'=>['<>', $this->auth->id]])->find();
            if ($exist) {
                $this->error('Email已经存在');
            }

            $model = new Admin();
            $result = $model->addData($params);

            if ($result !== false) {
                $this->success('操作成功', Url('/sysuser/index'));
            }
            $this->error('操作失败');
        }
        $this->view->assign([
            'boxti' => '添加管理员',
            'data'  => [],
        ]);
        return $this->view->fetch('edit');
    }

    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $params = [];
            $rid = input('rid');
            $params['username'] = input('username');
            $params['nickname'] = input('nickname');
            $params['email']    = input('email');
            $params['password'] = input('password');
            $params['status']   = input('status');

            $rid = intval($rid);

            if (!Validate::is($params['email'], "email")) {
                $this->error('Email格式错误');
            }
            if (isset($params['password']) && $params['password']!='') {
                if (!Validate::is($params['password'], "/^[\S]{6,16}$/")) {
                    $this->error('密码不合法');
                }
                $params['salt'] = Random::alnum();
                $params['password'] = md5(md5($params['password']) . $params['salt']);
            }
            $exist = Admin::where(['email'=>$params['email'], 'id'=>['<>', $rid]])->find();
            if ($exist) {
                $this->error('Email已经存在');
            }

            $model = new Admin();
            $result = $model->editData(['id'=>$rid], $params);

            if ($result !== false) {
                $this->success('操作成功', Url('/sysuser/index'));
            }
            $this->error('操作失败');
        }

        $rid = input('id');
        $rid = intval($rid);
        $data = Admin::where(['id'=>$rid])->find();
        $this->view->assign([
            'boxti' => '编辑管理员',
            'data'  => $data,
        ]);
        return $this->view->fetch('edit');
    }

    public function del($ids = '')
    {
        $rid = input('rid');
        $rid = intval($rid);

        $model = new Admin();
        $result = $model->deleteData(['id'=>$rid]);

        if ($result) {
            return $this->generJson(200, '操作成功');
        }

        return $this->generJson(500, '操作失败', '');
    }

    public function setter()
    {
        $rid  = input('rid');
        $field = input('field');
        $val = input('val');

        $rid = intval($rid);

        if ($field == '' || $val == '') {
            return $this->generJson(500, '参数错误', '');
        }

        $allowField = ['status'];
        if (!in_array($field, $allowField)) {
            return $this->generJson(500, '非法操作', '');
        }

        if ($field == 'status' && !in_array($val, ['normal', 'hidden'])) {
            return $this->generJson(500, '参数错误:status', '');
        }

        $model = new Admin();
        $result = $model->editData(['id'=>$rid], [
            $field => $val,
        ]);

        if ($result !== false) {
            return $this->generJson(200, '操作成功', '');
        }
        return $this->generJson(500, '操作失败', '');
    }

    /**
     * 设置管理组
     */
    public function set_group()
    {
        if ($this->request->isPost()) {
            $rid = input('rid');
            $groupIds = input('group_ids/a');

            if (!is_array($groupIds)) {
                $this->error('参数错误');
            }
            sort($groupIds);
            $rid = intval($rid);
            if ($rid == 1) {
                $this->error('该管理员为创始超级管理员不允许操作');
            }

            $authGroupAccess = new AuthGroupAccess();
            $authGroupAccess->deleteData(['uid'=>$rid]);

            $result = true;
            foreach ($groupIds as $gid) {
                $fruit = $authGroupAccess->addData(['uid'=>$rid,'group_id'=>$gid]);
                if ($result !== false) {
                    $result = $fruit;
                }
            }

            if ($result !== false) {
                $this->success('操作成功', Url('/sysuser/index'));
            }
            $this->error('操作失败');
        }
        $rid = input('id');
        $rid = intval($rid);

        if ($rid == 1) {
            $this->error('该管理员为创始超级管理员不允许操作');
        }
        $data = Admin::where(['id'=>$rid])->find();

        $gids = [];
        $userGroup = AuthGroupAccess::where(['uid'=>$rid])->order('group_id')->select()->toArray();
        foreach ($userGroup as $row) {
            $gids[] = $row['group_id'];
        }
        $data['groups'] = $gids;

        // $xGroup = AuthGroup::where(['id'=>['in', $gids], 'rules'=>'*'])->find();
        // if ($xGroup) {
        //     $this->error('该管理员为超级管理员不允许操作');
        // }

        $groups = AuthGroup::where(['status'=>'normal'])->order('name')->select()->toArray();

        $this->view->assign([
            'data'   => $data,
            'groups' => $groups,
        ]);
        return $this->view->fetch();
    }
}
