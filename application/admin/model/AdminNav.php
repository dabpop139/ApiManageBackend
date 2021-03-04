<?php

namespace app\admin\model;

use think\Session;
use app\admin\library\Auth;

class AdminNav extends BaseModel
{
    /**
     * 获取全部菜单
     * @param  string $type tree获取树形结构 level获取层级结构
     * @return array       	结构数据
     */
    public function getMenuTree($type='tree', $order=''){
        // 判断是否需要排序
        if (empty($order)) {
            $data = $this->where(['ismenu'=>self::YES])->select();
        } else {
            $data = $this->where(['ismenu'=>self::YES])->order('ord is null,'.$order)->select();
        }

        $data = $data->toArray();
        $dataTree = [];

        $authIns = new Auth();
        // 获取树形或者结构数据
        if ($type == 'tree') {
            // $data=\Org\Nx\Data::tree($data,'name','id','pid');
        } elseif ($type = 'level') {

            $dTree = get_tree($data);
            $ids = [];
            $getTree = function ($arr, $step) use ($authIns, &$getTree, $ids) {
                $tem = [];
                foreach ($arr as $row) {
                    $v = $row->info;
                    if(!$authIns->check($v['mca'])){
                        continue;
                    }
                    $tem['menu_'.$v['id']] = [
                        'id'         => $v['id'],
                        'pid'        => $v['pid'],
                        'name'       => $v['name'],
                        'module'     => '',
                        'controller' => '',
                        'method'     => '',
                        'icon'       => $v['ico'],
                        'url'        => Url($v['mca']),
                    ];
                    if (isset($row->childs)) {
                        $recu = $getTree($row->childs, $step+1);
                        $tem['menu_'.$v['id']]['children'] = $recu;
                    }

                }
                return $tem;
            };
            $dataTree = $getTree($dTree, 1);
            // print_r($dataTree);
            // die;

            // $data=\Org\Nx\Data::channelLevel($data,0,'&nbsp;','id');
            // 显示有权限的菜单
            // $auth = new \Think\Auth();
            // foreach ($data as $k => $v) {
            //     if ($auth->check($v['mca'], $mantk['id'])) {
            //         foreach ($v['_data'] as $m => $n) {
            //             if(!$auth->check($n['mca'], $mantk['id'])){
            //                 unset($data[$k]['_data'][$m]);
            //             }
            //         }
            //     }else{
            //         // 删除无权限的菜单
            //         unset($data[$k]);
            //     }
            // }
        }
        return $dataTree;
    }

}
