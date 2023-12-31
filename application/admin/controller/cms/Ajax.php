<?php

namespace app\admin\controller\cms;

use addons\cms\library\aip\AipContentCensor;
// use addons\cms\library\SensitiveHelper;
use addons\cms\library\Service;
use app\common\controller\Backend;

/**
 * Ajax
 *
 * @icon fa fa-circle-o
 * @internal
 */
class Ajax extends Backend
{

    /**
     * 模型对象
     */
    protected $model = null;
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取模板列表
     * @internal
     */
    public function get_template_list()
    {
        $files = [];
        $keyValue = $this->request->request("keyValue");
        if (!$keyValue) {
            $type = $this->request->request("type");
            $name = $this->request->request("name");
            if ($name) {
                //$files[] = ['name' => $name . '.html'];
            }
            //设置过滤方法
            $this->request->filter(['strip_tags']);
            $config = get_addon_config('cms');
            $themeDir = ADDON_PATH . 'cms' . DS . 'view' . DS . $config['theme'] . DS;
            $dh = opendir($themeDir);
            while (false !== ($filename = readdir($dh))) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }
                if ($type) {
                    $rule = $type == 'channel' ? '(channel|list)' : $type;
                    if (!preg_match("/^{$rule}(.*)/i", $filename)) {
                        continue;
                    }
                }
                $files[] = ['name' => $filename];
            }
        } else {
            $files[] = ['name' => $keyValue];
        }
        return $result = ['total' => count($files), 'list' => $files];
    }

    /**
     * 检查内容是否包含违禁词
     * @throws \Exception
     */
    public function check_content_islegal()
    {
        $config = get_addon_config('cms');
        $content = $this->request->post('content');
        if (!$content) {
            $this->error('请输入检测内容');
        }
        if ($config['audittype'] == 'local') {
            // // 敏感词过滤
            // $handle = SensitiveHelper::init()->setTreeByFile(ADDON_PATH . 'cms/data/words.dic');
            // //首先检测是否合法
            // $arr = $handle->getBadWord($content);
            $arr = Service::getIllegalWord($content);
            if ($arr) {
                $this->error('发现违禁词', null, $arr);
            } else {
                $this->success('未发现违禁词');
            }
        } else {
            $client = new AipContentCensor($config['aip_appid'], $config['aip_apikey'], $config['aip_secretkey']);
            $result = $client->antiSpam($content);
            if (isset($result['result']) && $result['result']['spam'] > 0) {
                $arr = [];
                foreach (array_merge($result['result']['review'], $result['result']['reject']) as $index => $item) {
                    $arr[] = $item['hit'];
                }
                $this->error('发现违禁词', null, $arr);
            } else {
                $this->success('未发现违禁词');
            }
        }
    }

    /**
     * 获取关键字
     * @throws \Exception
     */
    public function get_content_keywords()
    {
        $config = get_addon_config('cms');
        $title = $this->request->post('title');
        $tags = $this->request->post('tags', '');
        $content = $this->request->post('content');
        if (!$content) {
            $this->error('请输入检测内容');
        }
        $keywords = Service::getContentTags($title);
        $keywords = in_array($title, $keywords) ? [] : $keywords;
        $keywords = array_filter(array_merge([$tags], $keywords));
        $description = mb_substr(strip_tags($content), 0, 200);
        $data = [
            "keywords"    => implode(',', $keywords),
            "description" => $description
        ];
        $this->success("提取成功", null, $data);
    }
}
