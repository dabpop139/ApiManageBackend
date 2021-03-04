<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/21 0021
 * Time: 下午 1:51
 */

namespace fast\oss;

use think\Config;
use OSS\OssClient;

class AliOSS
{
    protected $config;

    protected $endpoint;

    protected $is_cname = false;

    protected $oss_client;

    public function __construct($isCName = false)
    {
        $this->config = Config::get('upload.oss')['ali'];

        if ( $isCName ) {
            $this->is_cname = true;
            $this->endpoint = $this->config['cname'];
        } else {
            $this->is_cname = false;
            $this->endpoint = $this->config['endpoint'];
        }

    }

    public static function getSourceUrl($object)
    {
        $config = Config::get('oss')['ali'];

        if ( $config['cname'] ) {
            $host = str_replace('http://', '', $config['cname']);
        } else {
            $host = str_replace('http://', '', $config['endpoint']);
        }
        
        return sprintf("http://%s/%s", trim($host, '/'), $object);
    }

    public function initOssClient()
    {
        $this->oss_client = new OssClient(
            $this->config['access_key_Id'],
            $this->config['access_key_secret'],
            $this->endpoint,
            $this->is_cname
        );

        return $this->oss_client;
    }


    public function signature($dir, $expire, $callbackUrl = '', $callbackParams = [], $maxFileSize = 1048576000)
    {
        $id= $this->config['access_key_Id'];
        $key= $this->config['access_key_secret'];
        $host = str_replace('http:', '', $this->config['bucket'] . '.' . $this->endpoint);
        $host = 'http://' . $host;#die($host);
        $expire_time = time() + $expire;
        $expire_time = gmtIso8601($expire_time);

        $conditions = [[
                'content-length-range',
                0,
                $maxFileSize
            ],[
                'starts-with',
                '$key',
                rtrim($dir, '/') . '/'
            ]];

        $policy = [
            'expiration'=>$expire_time,
            'conditions'=>$conditions
        ];

        $policy = json_encode($policy);
        $base64_policy = base64_encode($policy);

        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = [];

        if ( $callbackUrl ) {
            $callbackBody = [
                'callbackUrl'      => $callbackUrl,
                'callbackBody'     => 'bucket=${bucket}&object=${object}&size=${size}&mimeType=${mimeType}',
                'callbackBodyType' => "application/x-www-form-urlencoded",
            ];

            if ( $callbackParams ) {
                $callbackBody['callbackBody'] = $callbackBody['callbackBody'] . '&' . http_build_query($callbackParams);
            }

            $callbackBody = json_encode($callbackBody);
            $callbackBody = base64_encode($callbackBody);
            $response['callback'] =  $callbackBody;
        }
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $expire_time;


        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;

        return $response;
    }

    public static function imageResize($imageUrl, $w, $h, $m = 'pad', $limit = 1, $color = 'FFFFFF')
    {
        if ( !$imageUrl ) {
            return Config::get('site.default_img');
        }

        return sprintf(
            "%s?x-oss-process=image/resize,m_%s,w_%d,h_%d,limit_%d,color_%s",
            $imageUrl,
            $m,
            $w,
            $h,
            $limit,
            $color
        );
    }

    public static function getImageInfo($image)
    {
        $imageInfo = file_get_contents($image . "?x-oss-process=image/info");

        if ( $imageInfo ) {
            return json_decode($imageInfo, true);
        }

        return false;
    }

    public function getConfig($key = 'ALL')
    {
        return $key === 'ALL' ? $this->config : $this->config[$key];
    }
}