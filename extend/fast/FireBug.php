<?php
namespace fast;

use think\Config;

class FireBug {
    /**
     * 将php调试信息打印到控制台
     * @param mixed $object : 待输出的数据,类型可以是字符串、数组或者对象
     * @param string $label : 标题
     * @param boolean $showTrace : 是否显示调用跟踪信息
     */
    public static function console($object, $label=null, $showTrace=false){

        //开发与生产模式的开关标识，我们只在开发模式下调试脚本
        if (!Config::get('app_debug') || Config::get('app_debug') == false) {
            return;
        }
        try {
            // 让系统自动加载FireShowPageTrace.class.php文件
            $fireShow = new FireShowPageTrace();
            unset($fireShow);

            $label = $label ? $label : time();
            FB::log($object, $label);
            // if (is_array($object) || is_object($object)) {
            //     reset($object);
            //     $headers = array_keys($object);
            //     if (is_array($headers)) {
            //         array_unshift($object, $headers);
            //         FB::table($label, $object);
            //     }else{
            //         FB::table($label, [array_keys($object), $object]);
            //     }
            // }else if(is_object($object)){
            //     FB::table($label, $object);
            // }
            if ($showTrace) {
                FB::trace($label);
            }
        } catch (Exception $e) {
            echo '!!!You must have Output Buffering enabled via ob_start()';
        }
    }
}