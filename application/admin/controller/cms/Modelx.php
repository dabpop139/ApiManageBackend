<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;
use think\Exception;

/**
 * 内容模型表
 *
 * @icon fa fa-th
 */
class Modelx extends Backend
{

    /**
     * Model模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\cms\Modelx;
    }

    /**
     * 复制模型
     */
    public function duplicate($ids = "")
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error('记录未找到');
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error('你没有权限访问');
            }
        }
        if ($this->request->isPost()) {
            $table = $this->request->request("table");
            try {
                $data = [
                    'name'       => $row->getData('name') . '_copy',
                    'table'      => $table,
                    'fields'     => $row->fields,
                    'channeltpl' => $row->channeltpl,
                    'listtpl'    => $row->listtpl,
                    'showtpl'    => $row->showtpl,
                    'setting'    => $row->setting,
                ];
                $modelx = \app\admin\model\cms\Modelx::create($data, true);
                $fieldsList = \app\admin\model\cms\Fields::where('model_id', $row['id'])->select();
                foreach ($fieldsList as $index => $item) {
                    $data = $item->toArray();
                    $data['model_id'] = $modelx->id;
                    unset($data['id'], $data['createtime'], $data['updatetime']);
                    \app\admin\model\cms\Fields::create($data, true);
                }
            } catch (Exception $e) {
                $this->error("复制失败，错误：" . $e->getMessage());
            }
            $this->success();
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
