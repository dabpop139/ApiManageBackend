<?php

namespace app\admin\controller;

use think\Hook;
use think\Validate;
use app\common\controller\Backend;
use app\common\model\User as DDUser;
use app\common\model\UserMessage;
use app\common\model\UserGames;
use fast\Random;

/**
 * 管理员管理
 *
 * @icon fa fa-user
 */
class User extends Backend
{

    public function index()
    {
        $model = new DDUser();
        $kword = input('keyword');
        $orderby = input('orderby');
        if ($kword != '') {
            $model = $model->where([
                'username|nickname|mobile|email|memo' => ['like', '%' . $kword . '%'],
            ]);
        }
        if ($orderby) {
            if (in_array($orderby, ['createtime', 'logintime', 'activetime'])) {
                $orderby = $orderby.' desc';
            } else {
                $orderby = 'totalscore desc';
            }
        } else {
            $orderby = 'totalscore desc';
        }
        $pager = $model->order($orderby)->paginate(50, false, ['query' => input('param.')]);
        // $datas = $pager->items();
        $datas = $pager->toArray();
        $datas = $datas['data'];
        foreach ($datas as &$row) {
            $row['gamebinded'] = '';
            $gamenum = UserGames::field('id')->where(['user_id' => $row['id'], 'isbind' => UserGames::YES])->count();
            if ($gamenum) {
                $row['gamebinded'] = '是('.$gamenum.')';
            }
            // print_r($row);
            $row['mobile'] = $row['mobile'] ? substr_replace($row['mobile'], '****', 3, 4) : '';
            $row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
            $row['logintime'] = date('Y-m-d H:i:s', $row['logintime']);
        }
        unset($row);

        $this->view->assign([
            'keyword' => $kword,
            'datas'   => $datas,
            'pager'   => $pager,
        ]);
        return $this->view->fetch();
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
            $params['memo']     = input('memo', '', 'trim');

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
            $exist = DDUser::where(['email'=>$params['email'], 'id'=>['<>', $rid]])->find();
            if ($exist) {
                $this->error('Email已经存在');
            }

            $model = new DDUser();
            $result = $model->editData(['id'=>$rid], $params);

            if ($result !== false) {
                $this->success('操作成功', Url('/user/index'));
            }
            $this->error('操作失败');
        }

        $rid = input('id');
        $rid = intval($rid);
        $data = DDUser::where(['id'=>$rid])->find();
        $this->view->assign([
            'boxti' => '编辑管理员',
            'data'  => $data,
        ]);
        return $this->view->fetch('edit');
    }

    public function view()
    {
        $rid = input('rid');
        $rid = intval($rid);
        $data = DDUser::where(['id'=>$rid])->find();
        $user = $data->toArray();
        unset($user['password']);

        $games = [];
        $gamecodes = ['HHBY'];
        $reco = UserGames::where(['user_id' => $user['id'], 'gamecode' => ['in', $gamecodes], 'isbind' => UserGames::YES])->select();
        foreach ($reco as $row) {
            $ginfo = getGameInfo($row);
            $games[] = [
                'server_id'  => $ginfo['server_id'],
                'server'     => $ginfo['server'],
                'name'       => $ginfo['name'],
                'roleid'     => $ginfo['roleid'],
                'vip_level'  => $ginfo['vip_level'],
                'role_level' => $ginfo['role_level'],
                'vip_exp'    => $ginfo['vip_exp'],
                'iszun'      => $row['iszun'] == 1 ? '是':'否',
            ];
        }
        $user['games'] = $games;

        return $this->generJson(900, '操作成功', '', $user);
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

        $model = new DDUser();
        $result = $model->editData(['id'=>$rid], [
            $field => $val,
        ]);

        if ($result !== false) {
            return $this->generJson(200, '操作成功', '');
        }
        return $this->generJson(500, '操作失败', '');
    }

    public function score_change()
    {
        $rid  = input('rid');
        $score = input('change_score');
        $isadd_exp = input('add_scoreexp');
        $remark = input('remark');
        $remark = trim($remark);

        if ($isadd_exp == 'true') {
            $isadd_exp = true;
        } else {
            $isadd_exp = false;
        }
        $rid = intval($rid);

        if ($score == '' || $remark == '') {
            return $this->generJson(500, '参数错误', '');
        }
        
        $params = [
            'userid'    => $rid,
            'score'     => $score,
            'isadd_exp' => $isadd_exp,
            'remark'    => $remark,
            'operator'  => $this->adminNickName,
        ];
        list($result, $scode) = Hook::listen('operate_user_score', $params, null, true);
        if ($result !== false) {
            if ($score >= 0) {
                $tip = '增加';
            } else {
                $tip = '减少';
            }
            $params = [
                'userid'  => $rid,
                'type'    => UserMessage::TYPE_SYSTEM,
                'title'   => '您的积分有变动 原由:'.$remark,
                'content' => '积分'.$tip.'<span class="text-danger">'.$score.'</span>分 原由:'.$remark,
                'url'     => '',
            ];
            Hook::listen('message_to_user', $params);
            return $this->generJson(200, '操作成功');
        }
        $message = '操作失败';
        if ($scode=='SCORE_INSUFFICIENT') {
            $message = '用户积分不足';
        }
        return $this->generJson(500, $message, '');
    }
}
