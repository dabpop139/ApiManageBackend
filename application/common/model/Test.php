<?php

namespace app\common\model;

/**
 * 测试表
 */
class Test Extends BaseModel
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    // 追加属性
    protected $append = [
    ];

    protected static function init()
    {
        self::afterWrite(function ($row) {
            $changedData = $row->getChangedData();
            dd_log(666);
            // dd_log($row['image']);
            // dd_log($changedData);
            // dd_log($row->getData());
            dd_log('=============================');
            dd_log('=============================');
            foreach ($row as $k => $v) {
                dd_log($k);
                dd_log($v);
                dd_log('=============================');
            }
        });
    }
}
