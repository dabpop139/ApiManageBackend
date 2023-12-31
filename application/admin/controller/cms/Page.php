<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;

/**
 * 单页表
 *
 * @icon fa fa-file
 */
class Page extends Backend
{

    /**
     * Page模型对象
     */
    protected $model = null;
    protected $noNeedRight = ['selectpage_type', 'check_element_available'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\cms\Page;
        $this->view->assign("flagList", $this->model->getFlagList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function index()
    {
        $typeArr = \app\admin\model\cms\Page::distinct('type')->column('type');
        $this->view->assign('typeList', $typeArr);
        $this->assignconfig('typeList', $typeArr);
        return parent::index();
    }

    /**
     * 查看
     */
    public function select()
    {
        $typeArr = \app\admin\model\cms\Page::distinct('type')->column('type');
        $this->view->assign('typeList', $typeArr);
        $this->assignconfig('typeList', $typeArr);
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 动态下拉选择类型
     * @internal
     */
    public function selectpage_type()
    {
        $list = [];
        $word = (array)$this->request->request("q_word/a");
        $field = $this->request->request('showField');
        $keyValue = $this->request->request('keyValue');
        if (!$keyValue) {
            if (array_filter($word)) {
                foreach ($word as $k => $v) {
                    $list[] = ['id' => $v, $field => $v];
                }
            }
            $typeArr = \app\admin\model\cms\Page::column('type');
            $typeArr = array_unique($typeArr);
            foreach ($typeArr as $index => $item) {
                $list[] = ['id' => $item, $field => $item];
            }
        } else {
            $list[] = ['id' => $keyValue, $field => $keyValue];
        }
        return json(['total' => count($list), 'list' => $list]);
    }

    /**
     * 检测元素是否可用
     * @internal
     */
    public function check_element_available()
    {
        $id = $this->request->request('id');
        $name = $this->request->request('name');
        $value = $this->request->request('value');
        $name = substr($name, 4, -1);
        if (!$name) {
            $this->error('参数name不能为空');
        }
        if ($id) {
            $this->model->where('id', '<>', $id);
        }
        $exist = $this->model->where($name, $value)->find();
        if ($exist) {
            $this->error('已经存在');
        } else {
            $this->success();
        }
    }
}
