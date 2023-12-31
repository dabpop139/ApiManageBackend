<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;

/**
 * 自定义表单数据表
 *
 * @icon fa fa-circle-o
 */
class Diydata extends Backend
{

    /**
     * 自定义表单模型对象
     */
    protected $diyform = null;
    /**
     * 定义表单数据表模型
     * @var null
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $diyform_id = $this->request->param('diyform_id');
        $this->diyform = \app\admin\model\cms\Diyform::get($diyform_id);
        if (!$this->diyform) {
            $this->error('未找到对应自定义表单');
        }
        $this->model = \think\Db::name($this->diyform['table']);
        $this->assignconfig('diyform_id', $diyform_id);
    }

    /**
     * 查看
     */
    public function index()
    {
        $fieldsList = \app\admin\model\cms\Fields::where('diyform_id', $this->diyform['id'])->where('type', '<>', 'text')->select();
        $fields = [];
        foreach ($fieldsList as $index => $item) {
            $fields[] = ['field' => $item['name'], 'title' => $item['title'], 'type' => $item['type'], 'content' => $item['content_list']];
        }
        $this->assignconfig('fields', $fields);
        $diyformList = \app\admin\model\cms\Diyform::all();
        $this->view->assign('diyform', $this->diyform);
        $this->view->assign('diyformList', $diyformList);
        return parent::index();
    }

    /**
     * 添加
     */
    public function add()
    {
        $this->assignFields();
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    $result = $this->model->insert($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error('参数name不能为空');
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->where('id', $ids)->find();
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
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    $result = $this->model->where('id', $ids)->update($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error('参数name不能为空');
        }

        $this->assignFields($ids);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $count = $this->model->where($pk, 'in', $ids)->delete();
            if ($count) {
                $this->success();
            } else {
                $this->error('未删除任何行');
            }
        }
        $this->error('参数ids不能为空');
    }

    private function assignFields($diydata_id = 0)
    {
        $values = [];
        if ($diydata_id) {
            $values = db($this->diyform['table'])->where('id', $diydata_id)->find();
        }
        $fields = \app\admin\model\cms\Fields::where('diyform_id', $this->diyform['id'])
            ->order('weigh desc,id desc')
            ->select();
        foreach ($fields as $k => $v) {
            $v->value = isset($values[$v['name']]) ? $values[$v['name']] : '';
            $v->rule = str_replace(',', '; ', $v->rule);
            if (in_array($v->type, ['checkbox', 'lists', 'images'])) {
                $checked = '';
                if ($v['minimum'] && $v['maximum']) {
                    $checked = "{$v['minimum']}~{$v['maximum']}";
                } elseif ($v['minimum']) {
                    $checked = "{$v['minimum']}~";
                } elseif ($v['maximum']) {
                    $checked = "~{$v['maximum']}";
                }
                if ($checked) {
                    $v->rule .= (';checked(' . $checked . ')');
                }
            }
            if (in_array($v->type, ['checkbox', 'radio']) && stripos($v->rule, 'required') !== false) {
                $v->rule = str_replace('required', 'checked', $v->rule);
            }
            if (in_array($v->type, ['selects'])) {
                $v->extend .= (' ' . 'data-max-options="' . $v['maximum'] . '"');
            }
        }

        $this->view->assign('fields', $fields);
        $this->view->assign('values', $values);
    }

    /**
     * 导入
     */
    public function import($ids = "")
    {
        $this->model = new \addons\cms\model\Diydata($this->diyform->table);
        return parent::import();
    }
}
