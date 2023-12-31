<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;

/**
 * 自定义表单表
 *
 * @icon fa fa-list
 */
class Diyform extends Backend
{

    /**
     * Model模型对象
     */
    protected $model = null;
    protected $noNeedRight = ['check_element_available'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\cms\Diyform;
        $this->view->assign("statusList", $this->model->getStatusList());
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
