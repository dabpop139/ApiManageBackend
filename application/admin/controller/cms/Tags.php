<?php

namespace app\admin\controller\cms;

use think\Db;
use app\common\controller\Backend;
use app\admin\model\cms\Archives;

/**
 * 标签表
 *
 * @icon fa fa-tags
 */
class Tags extends Backend
{

    /**
     * Tags模型对象
     */
    protected $model = null;
    protected $noNeedRight = ['selectpage', 'autocomplete'];
    protected $searchFields = 'id,name';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\cms\Tags;
    }

    public function selectpage()
    {
        $response = parent::selectpage();
        $word = (array)$this->request->request("q_word/a");
        if (array_filter($word)) {
            $result = $response->getData();
            $list = [];
            foreach ($result['list'] as $index => $item) {
                $list[] = strtolower($item['name']);
            }
            foreach ($word as $k => $v) {
                if (!in_array(strtolower($v), $list)) {
                    array_unshift($result['list'], ['id' => $v, 'name' => $v]);
                }
                $result['total']++;
            }
            $response->data($result);
        }
        return $response;
    }

    public function autocomplete()
    {
        $q = $this->request->request('q');
        $list = \app\admin\model\cms\Tags::where('name', 'like', '%' . $q . '%')->column('name');
        echo json_encode($list);
        return;
    }

    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $params = input('row/a');
            $archids = $params['archives'];
            $archids = explode(',', $archids);
            $origin = $params['origin_name'];
            // dd_log($archids);
            $params = array_intersect_key($params, array_flip(['name']));
            $rid = intval($ids);

            if ($params['name'] == '') {
                $this->error('参数错误');
            }

            $result = $this->model->save($params, ['id' => $rid]);
            if (is_array($archids)) {
                foreach ($archids as $aid) {
                    $reco = Archives::find($aid);
                    $temArr = explode(',', $reco->tags);
                    $temArr = str_replace($origin, $params['name'], $temArr);
                    $temStr = implode(',', $temArr);
                    $reco->tags = $temStr;
                    $reco->save();
                }
            }

            if ($result !== false) {
                $this->success('操作成功');
            }
            $this->error('未更新任何行');
        }
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error('记录未找到');
        }

        $this->view->assign('row', $row);
        return $this->view->fetch();
    }

}
