<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;
use app\admin\model\cms\Comment as DDComment;
/**
 * 评论管理
 *
 * @icon fa fa-comment
 */
class Comment extends Backend
{

    /**
     * Comment模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new DDComment();

        $this->view->assign("flagList", $this->model->getFlagList());
        $this->view->assign('typeList', $this->model->getTypeList());
        $this->view->assign('statusList', $this->model->getStatusList());

        $this->assignconfig("flagList", $this->model->getFlagList());
        $this->assignconfig('typeList', $this->model->getTypeList());
    }

    /**
     * 查看
     */
    public function index()
    {
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['archives', 'spage', 'user'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            // $list = $this->model
            //     ->with([
            //         'archives' => function ($query) {
            //             $query->withField('id,channel_id,title,style,flag,diyname,publishtime,status');
            //         },
            //         'spage' => function ($query) {
            //             $query->withField('id,title,flag,diyname,status');
            //         },
            //         'scoreshop' => function ($query) {
            //             $query->withField('id,title,flag,status');
            //         },
            //         'user' => function ($query) {
            //             $query->withField('id,username,nickname,avatar,jointime,logintime,prevtime');
            //         }
            //     ])
            /*$list = $this->model
                ->has('archives', [], 'title')
                ->has('spage', [], 'title')
                ->has('scoreshop', [], 'title')
                ->has('user', [], 'id,username,nickname,avatar')*/
            $list = $this->model
                ->with(['archives', 'spage', 'user', 'scoreshop'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $index => $item) {
                $item->archives->visible(['title']);
                $item->spage->visible(['title']);
                $item->scoreshop->visible(['title']);
                $item->user->visible(['id', 'username', 'nickname', 'avatar']);
            }
            $list = collection($list)->toArray();
            $result = ['total' => $total, 'rows' => $list];

            return json($result);
        }
        return $this->view->fetch();
    }
}
