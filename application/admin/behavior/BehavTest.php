<?php

namespace app\admin\behavior;

class BehavTest
{
    public function actionTest(&$params)
    {
        var_dump($params);
    }
}