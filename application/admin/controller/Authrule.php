<?php

namespace app\admin\controller;

use app\common\controller\Backend;

use app\admin\model\AdminNav;
use fast\Tree;
use think\Validate;

/**
 * 权限菜单管理
 *
 * @icon fa fa-user
 */
class Authrule extends Backend
{

    protected function getMenuList($ismenu = true)
    {
        $adminNav = new AdminNav();
        if ($ismenu) {
            $menuArr = $adminNav->where(['ismenu'=>AdminNav::YES])->order('ord,id')->select()->toArray();
        } else {
            $menuArr = $adminNav->order('ord,id')->select()->toArray();
        }
        foreach ($menuArr as &$row) {
            $row['orign_name'] = $row['name'];
        }
        // var_dump($menuArr);
        // die;
        $tree = Tree::instance();
        $tree->init($menuArr);
        $menu = $tree->getTreeArray(1);
        $menu = $tree->getTreeList($menu);
        return $menu;
    }
    /**
     * 菜单
     */
    public function menu()
    {
        $menu = $this->getMenuList();
        $menu = json_encode($menu);
        $this->view->assign('raw', $menu);
        return $this->view->fetch();
    }

    /**
     * 菜单
     */
    public function menu_add()
    {
        $pid  = input('pid');
        $name = input('name');
        $mca  = input('mca');
        $ico  = input('ico');

        if ($name == '') {
            return json(['code' => 500, 'message' => '参数错误']);
        }

        $adminNav = new AdminNav();
        $adminNav->addData([
            'pid'    => $pid,
            'name'   => $name,
            'mca'    => $mca,
            'ico'    => $ico,
            'ismenu' => AdminNav::YES
        ]);

        $menu = $this->getMenuList();
        return json($menu);
    }

    /**
     * 菜单
     */
    public function menu_del()
    {
        $rid = input('rid');
        $rid = intval($rid);

        $adminNav = new AdminNav();
        $result = $adminNav->deleteData(['id'=>$rid]);
        if ($result) {
            return json('OK');
        } else {
            return json(['code' => 500, 'message' => '操作失败']);
        }
    }

    /**
     * 菜单
     */
    public function menu_update()
    {
        $rid  = input('rid');
        $name = input('name');
        $mca  = input('mca');
        $ico  = input('ico');

        $rid = intval($rid);

        if ($name == '') {
            return json(['code' => 500, 'message' => '参数错误']);
        }

        $adminNav = new AdminNav();
        $result = $adminNav->editData(['id'=>$rid], [
            'name'   => $name,
            'mca'    => $mca,
            'ico'    => $ico
        ]);

        if ($result !== false) {
            $menu = $this->getMenuList();
            return json($menu);
        } else {
            return json(['code' => 500, 'message' => '操作失败']);
        }
    }

    /**
     * 菜单
     */
    public function menu_ordset()
    {
        $rid = input('rid');
        $ord = input('ord');

        $rid = intval($rid);
        $ord = intval($ord);

        $adminNav = new AdminNav();
        $result = $adminNav->editData(['id'=>$rid], [
            'ord' => $ord,
        ]);

        if ($result !== false) {
            $menu = $this->getMenuList();
            return json($menu);
        } else {
            return json(['code' => 500, 'message' => '操作失败']);
        }
    }

    /**
     * 权限
     */
    public function auth()
    {
        $menu = $this->getMenuList(false);
        $menu = json_encode($menu);
        $this->view->assign('raw', $menu);
        return $this->view->fetch();
    }

    /**
     * 权限
     */
    public function auth_add()
    {
        $pid  = input('pid');
        $name = input('name');
        $mca  = input('mca');
        $ico  = input('ico');
        $ismenu = input('ismenu');
        $isbatch = input('isbatch');
        $rawbatch = input('rawbatch');

        if ($name == '' && $isbatch == '') {
            return json(['code' => 500, 'message' => '参数错误']);
        }

        $arrBatch = [];
        if ($isbatch == 'true') {
            $arrLis = explode("\n", $rawbatch);
            foreach ($arrLis as $row) {
                $tem = explode('|', $row);
                if (count($tem) != 2) {
                    continue;
                }
                $arrBatch[] = $tem;
            }
            // 批量添加
            foreach ($arrBatch as $row) {
                $adminNav = new AdminNav();
                $adminNav->addData([
                    'pid'    => $pid,
                    'name'   => trim($row[0]),
                    'mca'    => trim($row[1]),
                    'ico'    => '',
                    'ismenu' => AdminNav::NO,
                ]);
            }
        } else {
            // 单条添加
            $adminNav = new AdminNav();
            $adminNav->addData([
                'pid'    => $pid,
                'name'   => $name,
                'mca'    => $mca,
                'ico'    => $ico,
                'ismenu' => (int)$ismenu,
            ]);
        }

        $menu = $this->getMenuList(false);
        return json($menu);
    }

    /**
     * 权限
     */
    public function auth_del()
    {
        $rid = input('rid');
        $rid = intval($rid);

        $adminNav = new AdminNav();
        $result = $adminNav->deleteData(['id'=>$rid]);
        if ($result) {
            return json('OK');
        } else {
            return json(['code' => 500, 'message' => '操作失败']);
        }
    }

    /**
     * 权限
     */
    public function auth_update()
    {
        $rid    = input('rid');
        $name   = input('name');
        $mca    = input('mca');
        $ico    = input('ico');
        $ismenu = input('ismenu');

        $rid = intval($rid);

        if ($name == '') {
            return json(['code' => 500, 'message' => '参数错误']);
        }

        $adminNav = new AdminNav();
        $result = $adminNav->editData(['id' => $rid], [
            'name'   => $name,
            'mca'    => $mca,
            'ico'    => $ico,
            'ismenu' => $ismenu
        ]);

        if ($result !== false) {
            $menu = $this->getMenuList(false);
            return json($menu);
        } else {
            return json(['code' => 500, 'message' => '操作失败']);
        }
    }

    /**
     * 权限
     */
    public function auth_ordset()
    {
        $rid = input('rid');
        $ord = input('ord');

        $rid = intval($rid);
        $ord = intval($ord);

        $adminNav = new AdminNav();
        $result = $adminNav->editData(['id'=>$rid], [
            'ord' => $ord,
        ]);

        if ($result !== false) {
            $menu = $this->getMenuList(false);
            return json($menu);
        } else {
            return json(['code' => 500, 'message' => '操作失败']);
        }
    }
}
