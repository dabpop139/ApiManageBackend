<?php

namespace app\admin\controller;

use think\Validate;
use app\common\controller\Backend;
use app\admin\model\AuthGroup;
use app\admin\model\AdminNav;
use fast\Tree;

/**
 * 管理组管理
 *
 * @icon fa fa-user
 */
class Sysgroup extends Backend
{

    public function index()
    {
        $model = new AuthGroup();
        $datas = $model->where(['status'=>'normal'])->order('name')->select()->toArray();
        $this->view->assign([
            'datas' => $datas,
        ]);
        return $this->view->fetch();
    }

    public function add()
    {
        $name = input('name');
        $model = new AuthGroup();
        $result = $model->addData([
            'name' => $name,
            'status' => 'normal',
        ]);

        if ($result) {
            return $this->generJson(200, '操作成功');
        }

        return $this->generJson(500, '操作失败', '');
    }

    public function update()
    {
        $rid  = input('rid');
        $name = input('name');

        $rid = intval($rid);

        if ($name == '') {
            return $this->generJson(500, '参数错误', '');
        }

        $model = new AuthGroup();
        $result = $model->editData(['id'=>$rid], [
            'name' => $name,
        ]);

        if ($result !== false) {
            return $this->generJson(200, '操作成功');
        }
        return $this->generJson(500, '操作失败', '');
    }

    public function del($ids = '')
    {
        $rid = input('rid');
        $rid = intval($rid);

        $model = new AuthGroup();
        $result = $model->deleteData(['id'=>$rid]);

        if ($result) {
            return $this->generJson(200, '操作成功');
        }

        return $this->generJson(500, '操作失败', '');
    }

    /**
     * 分配权限
     */
    public function rule()
    {
        if ($this->request->isPost()) {
            $id = input('id');
            $ruleIds = input('rule_ids/a');

            if (!is_array($ruleIds)) {
                $this->error('参数错误');
            }
            sort($ruleIds);
            $id = intval($id);
            $ruleIds = implode(',', $ruleIds);
            $ruleIds = '1,' . $ruleIds; // 压入根权限
            $model = new AuthGroup();
            $result = $model->editData(['id'=>$id], [
                'rules' => $ruleIds,
            ]);

            if ($result !== false) {
                $this->success('操作成功', Url('/sysgroup/index'));
            }
            $this->error('操作失败');
        }
        $id = input('id');
        $id = intval($id);
        $model = new AuthGroup();
        $group = $model->where(['id'=>$id])->find();
        // 超级管理员组不允许修改
        if ($group['rules']=='*') {
            $this->error('超级管理员组不允许操作');
        }
        $group['rules'] = explode(',', $group['rules']);

        $adminNav = new AdminNav();
        $menuArr = $adminNav->order('ord,id')->select()->toArray();
        foreach ($menuArr as &$row) {
            $row['orign_name'] = $row['name'];
        }
        unset($row);

        $tree = Tree::instance();
        $tree->init($menuArr);
        $menu = $tree->getTreeArray(1);
        $datas = $tree->getTreeList($menu);

        // print_r($menu);
        // print_r($datas);
        // die;

        $this->view->assign([
            'group' => $group,
            'datas' => $datas,
        ]);
        return $this->view->fetch();
    }
}
