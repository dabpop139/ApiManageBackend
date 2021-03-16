<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\ApiResource;
use app\common\model\ApiCategory;

/**
 * API接口管理控制器
 */
class Apictrl extends Api
{
    protected $noNeedLogin = ['handle', 'project', 'category', 'apidata', 'operate'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 接口保存
     */
    public function handle()
    {
        $post = input('post.');

        $fields = ['act', 'projectid', 'cateid', 'aid', 'apiname', 'reqscheme', 'apiuri'];
        foreach ($fields as $field) {
            if (!isset($post[$field])) {
                $this->error(__('Invalid parameters'));
            }
        }

        $projectid = intval($post['projectid']);
        $cateid    = intval($post['cateid']);
        $aid       = trim($post['aid']);
        $apiname   = trim($post['apiname']);
        $apiuri    = trim($post['apiuri']);
        $apiuri    = str_replace(['&amp;'], ['&'], $apiuri);

        $reqscheme = trim($post['reqscheme']);
        $reqmethod = trim($post['reqmethod']);

        $bodytype    = trim($post['bodytype']);
        $bodyrawtype = trim($post['bodyrawtype']);

        $rheader_chk = trim($post['rheader_chk']) == 'true' ? true : false;
        $rbody_chk   = trim($post['rbody_chk']) == 'true' ? true : false;

        $rheader = trim($post['rheader']);
        $rbody   = trim($post['rbody']);
        $rbody   = html_entity_decode($rbody);

        $act = $post['act'];
        if ($act == 'save') {
            $rawdata = [
                'reqscheme'   => $reqscheme,
                'bodytype'    => $bodytype,
                'bodyrawtype' => $bodyrawtype,
                'rheader_chk' => $rheader_chk,
                'rbody_chk'   => $rbody_chk,
                'rheader'     => $rheader,
                'rbody'       => $rbody
            ];

            $rawdata = json_encode($rawdata);
            $saveData = [
                'projectid' => $projectid,
                'cateid'    => $cateid,
                'apiname'   => $apiname,
                'apiuri'    => $apiuri,
                'reqmethod' => $reqmethod,
                'rawdata'   => $rawdata
            ];

            $apiRes = new ApiResource();
            $hasRec = ApiResource::where(['cateid' => $cateid, 'apiname' => $apiname])->find();
            if ($aid == 0) {
                if ($hasRec) {
                    $this->error('接口名称有重名');
                }
                $result = $apiRes->addData($saveData);
                $aid = $apiRes['id'];
            } else {
                if ($hasRec && $hasRec['id'] != $aid) {
                    $this->error('接口名称有重名');
                }
                $result = $apiRes->editData(['id' => $aid], $saveData);
            }

            if ($result === false) {
                $this->error('操作失败');
            } else {
                $this->success('操作成功', ['aid' => $aid]);
            }
        }

        $ContentTypeEnum = [
            // $bodytype
            'x-www-form-urlencoded' => 'application/x-www-form-urlencoded',
            'form-data'             => 'multipart/form-data',
            // $bodyrawtype
            'json'       => 'application/json',
            'xml'        => 'text/xml',
            'javascript' => 'application/javascript',
            'plain'      => 'text/plain',
            'html'       => 'text/html',
            // 'text'       => 'text/html',
        ];

        if ($act == 'send') {
            if ($reqscheme == 'HTTP') {
                $reqHeaders = [];
                if ($reqmethod == 'POST') {
                    if (isset($ContentTypeEnum[$bodytype])) {
                        $reqHeaders = ['Content-type: ' . $ContentTypeEnum[$bodytype]];
                    }
                    if ($bodytype == 'raw' && isset($ContentTypeEnum[$bodyrawtype])) {
                        $reqHeaders = ['Content-type: ' . $ContentTypeEnum[$bodyrawtype]];
                    }
                    // $reqHeaders[] = 'Accept: application/json';
                }

                if ($rheader_chk) {
                    $headerArr = explode("\n", $rheader);
                    foreach ($headerArr as $line) {
                        $line = trim($line);
                        if (trim($line) == '' || strpos($line, ':') === false) {
                            continue;
                        }
                        if (mb_substr($line, 0, 2) == '//') {
                            continue;
                        }
                        $key = mb_substr($line, 0, mb_strpos($line, ':'));
                        $val = mb_substr($line, mb_strpos($line, ':')+1);
                        $reqHeaders[] = $key.': '.$val;
                    }
                }

                $reqBody = [];
                if ($rbody_chk && $reqmethod == 'POST') {
                    if ($bodytype=='raw') {
                        $reqBody = $rbody;
                    } else {
                        $bodyArr = explode("\n", $rbody);
                        foreach ($bodyArr as $line) {
                            $line = trim($line);
                            if (trim($line) == '' || strpos($line, ':') === false) {
                                continue;
                            }
                            if (mb_substr($line, 0, 2) == '//') {
                                continue;
                            }
                            $key = mb_substr($line, 0, mb_strpos($line, ':'));
                            $val = mb_substr($line, mb_strpos($line, ':') + 1);
                            $reqBody[$key] = $val;
                        }
                    }
                }
                // print_r($apiuri);
                // print_r($reqmethod);
                // print_r($reqHeaders);
                // print_r($reqBody);
                // die;
                $result = CurlSend($apiuri, $reqmethod, $reqBody, $reqHeaders);

                if ($result === false) {
                    $this->error('请求响应错误！尝试切换请求方式');
                }

                if ($aid > 0) {
                    $apiRes = new ApiResource();
                    $apiRes->editData(['id' => $aid], ['respraw' => $result['raw']]);
                }

                $respHeader = str_replace(["\r\n", "\n"], ['<br/>', '<br/>'], $result['header']);
                $this->success('', [
                    'status' => $result['status'],
                    'extime' => $result['extime'],
                    'header' => $respHeader,
                    'raw'    => $result['raw'],
                ]);
            }
        }
    }

    public function apidata()
    {
        $get = input('get.');
        $aid = intval($get['aid']);
        $apiRes = ApiResource::where(['id' => $aid])->find();
        if (!$apiRes) {
            $this->error(__('Invalid parameters'));
        }
        $rawdata = json_decode($apiRes['rawdata'], true);
        unset($apiRes['rawdata']);
        $apiRes = array_merge($apiRes->toArray(), $rawdata);

        $this->success('', $apiRes);
    }

    public function project()
    {
        $projects = ApiCategory::where(['pid' => 0])->select();
        $this->success('', $projects);
    }

    public function category()
    {
        if ($this->request->isPost()) {
            $post = input('post.');
            $fields = ['projectid', 'act', 'cateid'];
            foreach ($fields as $field) {
                if (!isset($post[$field])) {
                    $this->error(__('Invalid parameters'));
                }
            }

            $projectid = intval($post['projectid']);
            $cateid    = intval($post['cateid']);
            $act       = trim($post['act']);
            $name      = trim($post['name']);

            if ($act == 'save') {
                if (!isset($post['name'])) {
                    $this->error(__('Invalid parameters'));
                }
                $category = new ApiCategory();
                if ($cateid == 0) {
                    $result = $category->addData([
                        'pid' => $projectid,
                        'name'=> $name,
                    ]);
                } else {
                    $result = $category->editData(['id' => $cateid], ['name'=> $name]);
                }

                if ($result === false) {
                    $this->error('操作失败');
                } else {
                    $this->success('操作成功');
                }
            }
            if ($act == 'del') {
                $apiRes = ApiResource::where(['cateid' => $cateid])->find();
                if ($apiRes) {
                    $this->error('分类类目录不为空请先清空分类目录');
                }
                ApiCategory::where(['id' => $cateid])->delete();
                $this->success('操作成功');
            }
            $this->error(__('Invalid parameters'));
        }
        $get = input('get.');
        $projectid = intval($get['projectid']);
        $keyword = trim($get['keyword']);

        $category = ApiCategory::where(['id' => $projectid])->find();
        if (!$category) {
            $this->error(__('Invalid parameters'));
        }
        $subCategorys = ApiCategory::where(['pid' => $category['id']])->select();

        $cateids = [];
        foreach ($subCategorys as $item) {
            $cateids[] = $item['id'];
        }

        $where = ['projectid' => $projectid, 'cateid' => ['in', $cateids]];
        if (!empty($keyword)) {
            $where['apiname|apiuri'] = ['like', '%'.$keyword.'%'];
        }
        $apis = ApiResource::field('id,projectid,cateid,apiname,apiuri,reqmethod,createtime,updatetime')->where($where)->order('updatetime DESC')->select();
        $apisAssoc = [];
        foreach ($apis as $item) {
            $apisAssoc[$item['cateid']][] = $item;
        }

        foreach ($subCategorys as &$item) {
            if (isset($apisAssoc[$item['id']])) {
                $item['dlists'] = $apisAssoc[$item['id']];
            } else {
                $item['dlists'] = [];
            }
        }
        unset($item);

        $this->success('', [
            'project'  => $category,
            'subcates' => $subCategorys,
        ]);

    }

    public function operate()
    {
        $post = input('post.');
        $aid = intval($post['aid']);
        $act = $post['act'];

        if (!in_array($act, ['copy', 'del'])) {
            $this->error(__('Invalid parameters'));
        }

        $apiRes = ApiResource::where(['id' => $aid])->find();
        if (!$apiRes) {
            $this->error(__('Invalid parameters'));
        }

        if ($act == 'copy') {
            $newItem = new ApiResource();
            $apiRes = $apiRes->toArray();
            unset($apiRes['id']);
            $apiRes['apiname'] = $apiRes['apiname'] . ' Copy';
            $newItem->addData($apiRes);
        }

        if ($act == 'del') {
            $apiRes->delete();
        }

        $this->success('');
    }
}
