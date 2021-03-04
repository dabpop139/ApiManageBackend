<?php

namespace app\common\model;

use think\Hook;
/**
 * 会员模型
 */
class User extends BaseModel
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'url',
        'prevtime_text',
        'logintime_text',
        'jointime_text'
    ];

    protected static function init()
    {
        self::beforeUpdate(function ($row) {
            $changed = $row->getChangedData();
            //如果有修改密码
            if (isset($changed['password'])) {
                if ($changed['password']) {
                    // 如果有修改密码(废弃)
                    // $salt = \fast\Random::alnum();
                    // $row->password = \app\common\library\Auth::instance()->getEncryptPassword($changed['password'], $salt);
                    // $row->salt = $salt;
                } else {
                    // 为空不修改密码
                    unset($row->password);
                }
            }
        });


        self::beforeUpdate(function ($row) {
            $changedata = $row->getChangedData();
            if (isset($changedata['money'])) {
                $origin = $row->getOriginData();
                MoneyLog::create(['user_id' => $row['id'], 'money' => $changedata['money'] - $origin['money'], 'before' => $origin['money'], 'after' => $changedata['money'], 'memo' => '管理员变更金额']);
            }
        });
    }

    /**
     * 获取个人URL
     * @param   string $value
     * @param   array  $data
     * @return string
     */
    public function getUrlAttr($value, $data)
    {
        return "/u/" . $data['id'];
    }

    /**
     * 获取头像
     * @param   string $value
     * @param   array  $data
     * @return string
     */
    public function getAvatarAttr($value, $data)
    {
        if (!$value) {
            //如果不需要启用首字母头像，请使用
            $value = '/assets/img/avatar_default.jpg';
            // $value = letter_avatar($data['nickname']);
        }
        return $value;
    }

    /**
     * 获取未读消息数
     * @param   string $value
     * @param   array  $data
     * @return string
     */
    public function getUnreadCntAttr($value, $data)
    {
        try {
            $num = \app\common\model\UserMessage::getUnreadCnt($data['id']);
        } catch (\Exception $e) {
            $num = 0;
        }
        return $num;
    }

    /**
     * 获取会员的组别
     */
    public function getGroupAttr($value, $data)
    {
        return UserGroup::get($data['group_id']);
    }

    /**
     * 获取验证字段数组值
     * @param   string $value
     * @param   array  $data
     * @return  object
     */
    public function getVerificationAttr($value, $data)
    {
        $value = array_filter((array)json_decode($value, true));
        $value = array_merge(['email' => 0, 'mobile' => 0, 'idcard' => 0, 'wechat' => 0, 'qq' => 0], $value);
        return (object)$value;
    }

    /**
     * 设置验证字段
     * @param mixed $value
     * @return string
     */
    public function setVerificationAttr($value)
    {
        $value = is_object($value) || is_array($value) ? json_encode($value) : $value;
        return $value;
    }

    /**
     * 获取扩展字段
     * @param   string $value
     * @param   array  $data
     * @return  array
     */
    public function getExtraAttr($value, $data)
    {
        $value = json_decode($value, true);
        if ($value) {
            return $value;
        } else {
            return [];
        }
    }

    /**
     * 设置验证字段
     * @param mixed $value
     * @return string
     */
    public function setExtraAttr($value)
    {
        $value = is_object($value) || is_array($value) ? json_encode($value) : $value;
        return $value;
    }

    /**
     * 变更会员余额
     * @param int    $money   余额
     * @param int    $user_id 会员ID
     * @param string $memo    备注
     */
    public static function money($money, $user_id, $memo)
    {
        $user = self::get($user_id);
        if ($user && $money != 0) {
            $before = $user->money;
            $after = $user->money + $money;
            //更新会员信息
            $user->save(['money' => $after]);
            //写入日志
            MoneyLog::create(['user_id' => $user_id, 'money' => $money, 'before' => $before, 'after' => $after, 'memo' => $memo]);
        }
    }

    /**
     * 变更会员积分(废弃)
     * @param int    $score   积分
     * @param int    $user_id 会员ID
     * @param string $memo    备注
     */
    public static function score($score, $user_id, $memo)
    {
        return false;
        $user = self::get($user_id);
        if ($user && $score != 0) {
            $before = $user->score;
            $after = $user->score + $score;
            list($level, $needscore) = self::calcLevel($after);
            //更新会员信息
            $user->save(['score' => $after, 'level' => $level]);
            //写入日志
            ScoreLog::create(['user_id' => $user_id, 'score' => $score, 'before' => $before, 'after' => $after, 'memo' => $memo]);
        }
    }

    /**
     * 根据积分计算等级
     * @param int $score 积分
     * @return array
     */
    public static function calcLevel($score = 0)
    {
        $level = 0;
        $needscore = 0;
        $userGrade = UserGrade::order('figure asc')->column('figure,needscore');
        foreach ($userGrade as $lv => $value) {
            if ($score >= $value) {
                $level = $lv;
                $needscore = $value;
            }
        }
        return [$level, $needscore];
    }

    // 获取下一等级
    public static function nextLevel($score = 0)
    {
        $level = 0;
        $needscore = 0;
        $userGrade = UserGrade::order('figure asc')->column('figure,needscore');
        foreach ($userGrade as $lv => $value) {
            if ($value > $score) {
                $level = $lv;
                $needscore = $value;
                break;
            }
        }
        return [$level, $needscore];
    }

    // 修改昵称扣除积分
    public static function changeNicknameDealScore($userid)
    {
        // 扣除积分
        $params = ['userid'=>$userid, 'act'=>'modify_nickname'];
        list($result, $scode) = Hook::listen('update_user_score', $params, null, true);
        if ($result === false) {
            if ($scode == 'SCORE_INSUFFICIENT') {
                return [500, '积分不足'];
            } else {
                return [500, '扣除积分错误'];
            }
        }

        if ($result === true) {
            return [200, ''];
        } else {
            return [500, '发生内部错误'];
        }
    }


    public static function idcardAuth($userid, $idcard, $realname) {
        // 增加积分
        $user = self::get($userid);
        if ($user->verification->idcard == 1){
            return [200, ''];
        }
        $params = ['userid' => $userid, 'act' => 'verify_realname'];
        list($result, $scode) = Hook::listen('update_user_score', $params, null, true);
        if ($result === false) {
            return [500, '增加积分错误'];
        }

        if ($result === true) {
            $verification = $user->verification;
            $verification->idcard = self::YES;

            $user->verification = $verification;
            $user->idcard       = $idcard;
            $user->realname     = $realname;

            $user->save();
            return [200, ''];
        } else {
            return [500, '发生内部错误'];
        }
    }

    public static function bindThirdDealScore($userid, $type) {
        $typeArr = [
            'wechat' => 'verify_weixin',
            'qq'     => 'verify_qicq',
        ];

        if (!isset($typeArr[$type])) {
            return [200, ''];
        }

        // 增加积分
        $user = self::get($userid);
        if ($user->verification->{$type} == 1){
            return [200, ''];
        }
        $params = ['userid'=>$userid, 'act'=>$typeArr[$type]];
        list($result, $scode) = Hook::listen('update_user_score', $params, null, true);
        if ($result === false) {
            return [500, '增加积分错误'];
        }

        if ($result === true) {
            $verification = $user->verification;
            $verification->{$type} = self::YES;
            $user->verification = $verification;
            $user->save();
            return [200, ''];
        } else {
            return [500, '发生内部错误'];
        }
    }

    public function getOriginData()
    {
        return $this->origin;
    }

    public function getGenderList()
    {
        return ['1' => __('Male'), '0' => __('Female')];
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getPrevtimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['prevtime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getLogintimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['logintime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getJointimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['jointime'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setPrevtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setLogintimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setJointimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function group()
    {
        return $this->belongsTo('UserGroup', 'group_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
