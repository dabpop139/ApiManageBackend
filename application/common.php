<?php

// 公共助手函数

if (!function_exists('isubstr')) {
    function isubstr($str, $len)
    {
        if (mb_strlen($str) > $len) {
            return mb_substr($str, 0, $len, 'utf-8').'…';
        } else {
            return $str;
        }
    }
}

if (!function_exists('__')) {

    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array  $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name) {
            return $name;
        }
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return \think\Lang::get($name, $vars, $lang);
    }
}

if (!function_exists('format_bytes')) {

    /**
     * 将字节转换为可读文本
     * @param int    $size      大小
     * @param string $delimiter 分隔符
     * @return string
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $delimiter . $units[$i];
    }
}

if (!function_exists('datetime')) {

    /**
     * 将时间戳转换为日期时间
     * @param int    $time   时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }
}

if (!function_exists('human_date')) {

    /**
     * 获取语义化时间
     * @param int $time  时间
     * @param int $local 本地时间
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \fast\Date::human($time, $local);
    }
}

if (!function_exists('cdnurl')) {

    /**
     * 获取上传资源的CDN的地址
     * @param string  $url    资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function cdnurl($url, $domain = false)
    {
        $regex = "/^((?:[a-z]+:)?\/\/|data:image\/)(.*)/i";
        $url = preg_match($regex, $url) ? $url : \think\Config::get('upload.cdnurl') . $url;
        if ($domain && !preg_match($regex, $url)) {
            $domain = is_bool($domain) ? request()->domain() : $domain;
            $url = $domain . $url;
        }
        return $url;
    }
}


if (!function_exists('is_really_writable')) {

    /**
     * 判断文件或文件夹是否可写
     * @param    string $file 文件或目录
     * @return    bool
     */
    function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === false) {
                return false;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return true;
        } elseif (!is_file($file) or ($fp = @fopen($file, 'ab')) === false) {
            return false;
        }
        fclose($fp);
        return true;
    }
}

if (!function_exists('rmdirs')) {

    /**
     * 删除文件夹
     * @param string $dirname  目录
     * @param bool   $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }
}

if (!function_exists('copydirs')) {

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest   目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            ) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DS . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DS . $iterator->getSubPathName());
            }
        }
    }
}

if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }
}

if (!function_exists('addtion')) {

    /**
     * 附加关联字段数据
     * @param array $items  数据列表
     * @param mixed $fields 渲染的来源字段
     * @return array
     */
    function addtion($items, $fields)
    {
        if (!$items || !$fields) {
            return $items;
        }
        $fieldsArr = [];
        if (!is_array($fields)) {
            $arr = explode(',', $fields);
            foreach ($arr as $k => $v) {
                $fieldsArr[$v] = ['field' => $v];
            }
        } else {
            foreach ($fields as $k => $v) {
                if (is_array($v)) {
                    $v['field'] = isset($v['field']) ? $v['field'] : $k;
                } else {
                    $v = ['field' => $v];
                }
                $fieldsArr[$v['field']] = $v;
            }
        }
        foreach ($fieldsArr as $k => &$v) {
            $v = is_array($v) ? $v : ['field' => $v];
            $v['display'] = isset($v['display']) ? $v['display'] : str_replace(['_ids', '_id'], ['_names', '_name'], $v['field']);
            $v['primary'] = isset($v['primary']) ? $v['primary'] : '';
            $v['column'] = isset($v['column']) ? $v['column'] : 'name';
            $v['model'] = isset($v['model']) ? $v['model'] : '';
            $v['table'] = isset($v['table']) ? $v['table'] : '';
            $v['name'] = isset($v['name']) ? $v['name'] : str_replace(['_ids', '_id'], '', $v['field']);
        }
        unset($v);
        $ids = [];
        $fields = array_keys($fieldsArr);
        foreach ($items as $k => $v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $ids[$n] = array_merge(isset($ids[$n]) && is_array($ids[$n]) ? $ids[$n] : [], explode(',', $v[$n]));
                }
            }
        }
        $result = [];
        foreach ($fieldsArr as $k => $v) {
            if ($v['model']) {
                $model = new $v['model'];
            } else {
                $model = $v['name'] ? \think\Db::name($v['name']) : \think\Db::table($v['table']);
            }
            $primary = $v['primary'] ? $v['primary'] : $model->getPk();
            $result[$v['field']] = $model->where($primary, 'in', $ids[$v['field']])->column("{$primary},{$v['column']}");
        }

        foreach ($items as $k => &$v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $curr = array_flip(explode(',', $v[$n]));

                    $v[$fieldsArr[$n]['display']] = implode(',', array_intersect_key($result[$n], $curr));
                }
            }
        }
        return $items;
    }
}

if (!function_exists('var_export_short')) {

    /**
     * 返回打印数组结构
     * @param string $var    数组
     * @param string $indent 缩进字符
     * @return string
     */
    function var_export_short($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : var_export_short($key) . " => ")
                        . var_export_short($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, true);
        }
    }
}

if (!function_exists('letter_avatar')) {
    /**
     * 首字母头像
     * @param $text
     * @return string
     */
    function letter_avatar($text)
    {
        $total = unpack('L', hash('adler32', $text, true))[1];
        $hue = $total % 360;
        list($r, $g, $b) = hsv2rgb($hue / 360, 0.3, 0.9);

        $bg = "rgb({$r},{$g},{$b})";
        $color = "#ffffff";
        $first = mb_strtoupper(mb_substr($text, 0, 1));
        $src = base64_encode('<svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="100" width="100"><rect fill="' . $bg . '" x="0" y="0" width="100" height="100"></rect><text x="50" y="50" font-size="50" text-copy="fast" fill="' . $color . '" text-anchor="middle" text-rights="admin" alignment-baseline="central">' . $first . '</text></svg>');
        $value = 'data:image/svg+xml;base64,' . $src;
        return $value;
    }
}

if (!function_exists('hsv2rgb')) {
    function hsv2rgb($h, $s, $v)
    {
        $r = $g = $b = 0;

        $i = floor($h * 6);
        $f = $h * 6 - $i;
        $p = $v * (1 - $s);
        $q = $v * (1 - $f * $s);
        $t = $v * (1 - (1 - $f) * $s);

        switch ($i % 6) {
            case 0:
                $r = $v;
                $g = $t;
                $b = $p;
                break;
            case 1:
                $r = $q;
                $g = $v;
                $b = $p;
                break;
            case 2:
                $r = $p;
                $g = $v;
                $b = $t;
                break;
            case 3:
                $r = $p;
                $g = $q;
                $b = $v;
                break;
            case 4:
                $r = $t;
                $g = $p;
                $b = $v;
                break;
            case 5:
                $r = $v;
                $g = $p;
                $b = $q;
                break;
        }

        return [
            floor($r * 255),
            floor($g * 255),
            floor($b * 255)
        ];
    }
}

if (!function_exists('gmtIso8601')) {
    function gmtIso8601($timestamp)
    {
        $dtStr       = date('c', $timestamp);
        $dt          = new \DateTime($dtStr);
        $newDatetime = $dt->format(\DateTime::ISO8601);
        $pos         = strpos($newDatetime, '+');
        $newDatetime = substr($newDatetime, 0, $pos);

        return $newDatetime . 'Z';
    }
}

if (!function_exists('strLength')) {
    function strLength($str, $charset='utf-8')
    {
        if ($charset == 'utf-8'){
            $str = iconv('UTF-8', 'GBK', $str);
        }
        $num = strlen($str);
        // var_dump($num);
        $cnNum = 0;
        for ($i = 0; $i < $num; $i++) {
            if (ord(substr($str, $i + 1, 1)) > 127) {
                $cnNum++;
                $i++;
            }
        }
        $enNum  = $num - ($cnNum * 2);
        $number = ($enNum / 2) + $cnNum;
        return ceil($number);
    }
}

if (!function_exists('CurlGet')) {
    function CurlGet($url, $timeout = 60){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

if (!function_exists('CurlPost')) {
    function CurlPost($curlPost, $url, $timeout = 60, $cookie = ''){
        $headers = [
            'Pragma: no-cache',
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        if ($cookie != '') {
            curl_setopt($curl,CURLOPT_COOKIE, $cookie);
        }
        $return_str = curl_exec($curl);
        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode!==200) {
            $return_str = false;
        }
        if ($return_str == 'ERROR!!!') {
            $return_str = false;
        }
        return $return_str;
    }
}

if (!function_exists('CurlSend')) {
    function CurlSend($url, $method, $body = [], $header = [], $timeout = 60, $cookie = ''){
        $rHeaders = [
            'Pragma: no-cache',
        ];
        $rHeaders = array_merge($header, $rHeaders);

        if (is_array($body)) {
            $body = http_build_query($body);
        }
        $rBody = $body;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $rHeaders);

        if (strtoupper($method) == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $rBody);
        }

        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        if ($cookie != '') {
            curl_setopt($curl,CURLOPT_COOKIE, $cookie);
        }
        $t1 = microtime(true);
        $retbody = curl_exec($curl);
        $t2 = microtime(true);

        $extime = round(($t2 - $t1) * 1000, 0);
        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);

        // var_dump($url);
        // var_dump($method);
        // var_dump($rHeaders);
        // var_dump($rBody);
        // var_dump($retbody);
        // var_dump($httpCode);
        // die();

        if ($retbody === false) {
            return false;
        }
        list($retheader, $retbody) = explode("\r\n\r\n", $retbody, 2);

        return [
            'status' => $httpCode,
            'extime' => $extime,
            'header' => $retheader,
            'raw'    => $retbody,
        ];
    }
}

if (!function_exists('debugLoger')) {
    // debugLoger自定义日志记录
    function debugLoger($raw, $cate = 'debug')
    {
        if (is_array($raw)) {
            $raw = json_encode($raw, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        $uri = LOG_PATH.$cate.'_'.date('Y-m-d').'.log';
        file_put_contents($uri, date('Y-m-d H:i:s').' '.$raw.PHP_EOL, FILE_APPEND);
    }
}

if (!function_exists('dd_log')) {
    // FirePHP方式调试打印
    function dd_log($info, $label = 'phpinfo')
    {
        \fast\FireBug::console($info, $label);
    }
}

if (!function_exists('show_stars')) {
    function show_stars($o_stars, $icontype = '')
    {
        $o_stars = floatval($o_stars);
        $stars   = number_format($o_stars / 2, 1);
        // dd_log($stars);
        $starsArr = explode('.', $stars);
        $html     = '';
        for ($i = 1; $i <= $starsArr[0]; $i++) {
            $html .= '<img alt="' . $i . '" src="/assets/img/stars/star-on' . $icontype . '.png" />';
        }
        if ($starsArr[0] < 5) {
            if ($starsArr[1] == 0) {
                $html .= '<img alt="' . $i . '" src="/assets/img/stars/star-off' . $icontype . '.png" />';
            } else {
                $html .= '<img alt="' . $i . '" src="/assets/img/stars/star-half' . $icontype . '.png" />';
            }
        }
        $left = 6 - $i;
        for ($k=1; $k < $left; $k++) {
            $html .= '<img alt="'.($i+$k).'" src="/assets/img/stars/star-off'.$icontype.'.png" />';
        }
        $html .= '<span class="num fsize15">'.number_format($o_stars,1).'</span>';
        return $html;
    }
}


if (!function_exists('normalAssetUri')) {
    function normalAssetUri($content)
    {
        $domain = \think\Request::instance()->domain();
        // $content = str_replace(
        //     '/assets/libs/neditor-next/i18n/zh-cn/images/localimage.png',
        //     $domain.'/assets/libs/neditor-next/i18n/zh-cn/images/localimage.png',
        //     $content
        // );
        $content = str_replace(
            '/assets/',
            $domain . '/assets/',
            $content
        );
        return $content;
    }
}

if (!function_exists('embedTag2Video')) {
    function embedTag2Video($content)
    {
        $matches = [];
        preg_match_all('/<embed[^>]+/i', $content, $matches);

        if (count($matches) == 1) {
            foreach (reset($matches) as $val) {
                $src = [];
                preg_match('/src="(\S+)"/i', $val, $src);
                if (count($src) == 2) {
                    $content = str_replace($val . '>', '<video class="edui-video-js" src="'.$src[1].'" data-setup="{}" controls="controls" preload="auto"></video>', $content);
                }
            }
        }
        return $content;
    }
}

if (!function_exists('replaceViaUrl')) {
    function replaceViaUrl($content, $mode)
    {
        if ($mode == \app\common\constant\CommConst::VIA_WAPWEB) {
            $content = preg_replace('/\/cms\/a\/(\d+)\.html/i', '/#/a/$1', $content);
        }
        return $content;
    }
}

if (!function_exists('xunStrip')) {
    // 讯搜xunsearch内容格式规范
    function xunStrip($content)
    {
        $content = strip_tags($content);
        $order   = ['&nbsp;', '&emsp;',  '&ensp;',  '&thinsp;', "\r\n", "\r", "\n"];
        $replace = ['',       '',        '',        '',         ' ',    '',   ' '];
        $content = str_replace($order, $replace, $content);
        $content = html_entity_decode($content);
        return $content;
    }
}

if (!function_exists('sysAuth')) {
    // PHP简单加解密
    function sysAuth($string, $operation = 'ENCODE', $key = '', $expiry = 0)
    {
        $key_length = 4;
        $key = md5($key != '' ? $key : '{!!!ENCRYPT_KEY!!!}');
        $fixedkey = md5($key);
        $egiskeys = md5(substr($fixedkey, 16, 16));
        $runtokey = $key_length ? ($operation == 'ENCODE' ? substr(md5(microtime(true)), -$key_length) : substr($string, 0, $key_length)) : '';
        $keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
        $string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));
        $i = 0;
        $result = '';
        $string_length = strlen($string);
        for ($i = 0; $i < $string_length; $i++) {
            $result .= chr(ord($string[$i]) ^ ord($keys[$i % 32]));
        }
        if ($operation == 'ENCODE') {
            return $runtokey . str_replace('=', '', base64_encode($result));
        } else {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$egiskeys), 0, 16)) {
                return substr($result, 26);
            } else {
                return null;
            }
        }
    }
}

if (!function_exists('rCache')) {
    // Redis缓存
    function rCache($name, $value, $options = null)
    {
        $redisopt = \think\Config::get('cache.redis');
        if (is_numeric($options)) {
            $redisopt['expire'] = $options;
        }

        if (is_array($options)) {
            $redisopt = array_merge($options, $redisopt);
        }

        return cache($name, $value, $redisopt);
    }
}