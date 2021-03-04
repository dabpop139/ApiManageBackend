<?php

namespace app\api\controller;

use addons\cms\library\Service;
use addons\third\library\Service as ThirdService;
use fast\Random;
use app\common\controller\Api;
use app\common\constant\CommConst;
use app\common\library\Ems;
use app\common\library\Sms;
use app\common\library\Token;
use app\common\model\UserGames;
use think\Hook;
use think\Validate;

/**
 * 会员接口
 */
class User extends Api
{
    // protected $noNeedLogin = ['login', 'mobilelogin', 'renew', 'register', 'resetpwd', 'changeemail', 'changemobile', 'third'];
    protected $noNeedLogin = ['login', 'mobilelogin', 'renew', 'register', 'resetpwd', 'third'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();


        //监听注册登录注销的事件
        // $auth = $this->auth;
        // Hook::add('user_login_successed', function ($user) use ($auth) {
        //     $expire = input('post.keeplogin') ? 30 * 86400 : 0;
        //     Cookie::set('uid', $user->id, ['expire' => $expire, 'domain'=>'exsample.it']);
        //     Cookie::set('token', $auth->getToken(), ['expire' => $expire, 'domain'=>'exsample.it']);
        // });
        // Hook::add('user_register_successed', function ($user) use ($auth) {
        //     $expire = 30 * 86400;
        //     Cookie::set('uid', $user->id, ['expire' => $expire, 'domain'=>'exsample.it']);
        //     Cookie::set('token', $auth->getToken(), ['expire' => $expire, 'domain'=>'exsample.it']);
        // });
        // Hook::add('user_delete_successed', function ($user) use ($auth) {
        //     Cookie::delete('uid');
        //     Cookie::delete('token');
        // });
        // Hook::add('user_logout_successed', function ($user) use ($auth) {
        //     Cookie::delete('uid');
        //     Cookie::delete('token');
        // });
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
     *
     * @param string $account  账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->request('account');
        $password = $this->request->request('password');
        if (!$account || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $user = $this->auth->getUserinfo();
            $openid = 'gamecave' . $user['id'];
            if (!empty(CS_SOURCE)) {
                $ugame = UserGames::where(['user_id' => $user['id'], 'isassoc' => UserGames::YES])->find();
                if ($ugame) {
                    $openid = $ugame['username'];
                }
            }
            $userInfo = [
                'id'         => $user['id'],
                'gameopenid' => $openid, // 游戏用账号ID
                'username'   => $user['username'],
                'nickname'   => $user['nickname'],
                'avatar'     => $user['avatar'],
                'level'      => $user['level'],
                'token'      => $user['token'],
                'verified'   => $user['verification'],
            ];
            if (substr($userInfo['avatar'], 0, 4)!='http') {
                $userInfo['avatar'] = '';
            }
            $data = ['userinfo' => $userInfo];
            $this->success(__('Logged in successful'), $data);
        } else {
            $errs = $this->auth->getErrorAssoc();
            $this->error($errs['msg'], null, $errs['code']);
        }
    }

    /**
     * 手机验证码登录
     *
     * @param string $mobile  手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^\d+$")) {
            $this->error(__('Mobile is incorrect'), null, CommConst::E_MOBILE_FMERR);
        }

        if ($this->auth->smslogin($mobile, $captcha)) {
            $user = $this->auth->getUserinfo();
            $openid = 'gamecave' . $user['id'];
            if (!empty(CS_SOURCE)) {
                $ugame = UserGames::where(['user_id' => $user['id'], 'isassoc' => UserGames::YES])->find();
                if ($ugame) {
                    $openid = $ugame['username'];
                }
            }
            $userInfo = [
                'id'         => $user['id'],
                'gameopenid' => $openid, // 游戏用账号ID
                'username'   => $user['username'],
                'nickname'   => $user['nickname'],
                'avatar'     => $user['avatar'],
                'level'      => $user['level'],
                'token'      => $user['token'],
                'verified'   => $user['verification'],
            ];
            if (substr($userInfo['avatar'], 0, 4)!='http') {
                $userInfo['avatar'] = '';
            }
            $data = ['userinfo' => $userInfo];
            $this->success(__('Logged in successful'), $data);
        } else {
            $errs = $this->auth->getErrorAssoc();
            $this->error($errs['msg'], null, $errs['code']);
        }
    }

    /**
     * Token登录及Token更新
     * @param string $token  Token
     * @param string $ckey  校验码
     */
    public function renew()
    {
        $token = $this->request->request('token');
        $ckey = $this->request->request('ckey');
        if (empty($token) || empty($ckey)) {
            $this->error(__('Invalid parameters'));
        }
        $tokenInfo = Token::get($token);
        if (!$tokenInfo) {
            $this->error(__('You are not logged in'), CommConst::E_NOLOGINED);
        }
        // 快过期则在一定效验条件下刷新Token
        if ($tokenInfo['expires_in'] > 0 && $tokenInfo['expires_in'] < 3*86400) {
            // 刷新Token校验
            if (md5(substr($token, 4, 9)) == $ckey) {
                //删除源Token
                Token::delete($token);
                //创建新Token
                $token = Random::uuid();
                Token::set($token, $tokenInfo['user_id'], 30*86400);
            }
        }

        $this->auth->init($token);
        if (!$this->auth->isLogin()) {
            $errs = $this->auth->getErrorAssoc();
            $this->error($errs['msg'], null, $errs['code']);
        }

        $user = $this->auth->getUserinfo();
        $openid = 'gamecave' . $user['id'];
        if (!empty(CS_SOURCE)) {
            $ugame = UserGames::where(['user_id' => $user['id'], 'isassoc' => UserGames::YES])->find();
            if ($ugame) {
                $openid = $ugame['username'];
            }
        }
        $userInfo = [
            'id'         => $user['id'],
            'gameopenid' => $openid, // 游戏用账号ID
            'username'   => $user['username'],
            'nickname'   => $user['nickname'],
            'avatar'     => $user['avatar'],
            'level'      => $user['level'],
            'token'      => $user['token'],
            'verified'   => $user['verification'],
        ];
        if (substr($userInfo['avatar'], 0, 4)!='http') {
            $userInfo['avatar'] = '';
        }
        $data = ['userinfo' => $userInfo];
        $this->success(__('Logged in successful'), $data);
    }

    /**
     * 注册会员
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     * @param string $code   验证码
     */
    public function register()
    {
        exit();
        // 禁用
        $username = $this->request->request('username');
        $password = $this->request->request('password');
        $email = $this->request->request('email');
        $mobile = $this->request->request('mobile');
        $code = $this->request->request('code');
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        if ($email && !Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $ret = Sms::check($mobile, $code, 'register');
        if (!$ret) {
            $this->error(__('Captcha is incorrect'));
        }
        $ret = $this->auth->register($username, $password, $email, $mobile, []);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Sign up successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    /**
     * 修改会员个人信息
     *
     * @param string $avatar   头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio      个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->request('username');
        $nickname = $this->request->request('nickname', '', 'trim,strip_tags,htmlspecialchars');
        $bio = $this->request->request('bio', '', 'trim,strip_tags,htmlspecialchars');
        $birthday = $this->request->request('birthday', '', 'trim,strip_tags,htmlspecialchars');
        $wechat = $this->request->request('wechat', '', 'trim,strip_tags,htmlspecialchars');
        $qq = $this->request->request('qq', '', 'trim,strip_tags,htmlspecialchars');
        $avatar = $this->request->request('avatar', '', 'trim,strip_tags,htmlspecialchars');
        // if ($username) {
        //     $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
        //     if ($exists) {
        //         $this->error(__('Username already exists'));
        //     }
        //     $user->username = $username;
        // }
        $rule = [
            'nickname' => 'require',
            'birthday' => 'date',
            'wechat'   => 'length:3,30',
        ];
        $msg = [
            'nickname.require' => '昵称不能为空',
            'birthday'         => '出生日期格式错误',
            'wechat'           => '微信错误',
        ];
        $data = [
            'nickname'  => $nickname,
            'birthday'  => $birthday,
            'wechat'    => $wechat,
        ];

        if (strLength($nickname) < 2 || strLength($nickname) > 8) {
            $this->error('昵称必须2-8个字符');
        }

        $isLegal = Service::isContentLegal($nickname.'_'.$bio);
        if (!$isLegal) {
            $this->error('昵称或个人介绍中有违禁词');
        }
        // $isLegal = Service::isContentLegal($bio);
        // if (!$isLegal) {
        //     $this->error('个人介绍有违禁词');
        // }

        if (\app\common\model\User::where('nickname', $nickname)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Nickname already exist'));
        }

        if ($birthday != '') {
            $timestamp = strtotime($birthday);
            if (!$timestamp) {
                $this->error('出生日期格式错误');
            }
            $dta = (time() - $timestamp)/86400/365;
            if ($dta <= 8 || $dta >= 120) {
                $this->error('出生日期格式错误');
            }
        }
        if ($qq) {
            if (!preg_match('/^[1-9][0-9]{4,14}$/', $qq)) {
                $this->error('QQ号格式错误');
            }
        }

        $validate = new Validate($rule, $msg);
        $result = $validate->check($data);
        if (!$result) {
            $this->error($validate->getError(), null, ['token' => $this->request->token()]);
        }

        // 修改昵称称扣除积分
        if ($user->nickname != $nickname) {
            list($status, $result) = \app\common\model\User::changeNicknameDealScore($user->id);
            if ($status !== 200) {
                $this->error($result);
            }
        }

        $user->nickname = $nickname;
        $user->bio = $bio;
        if (!$user->birthday && $birthday) {
            $user->birthday = $birthday;
        }
        if (!$user->wechat && $wechat) {
            $user->wechat = $wechat;
        }
        if (!$user->qq && $qq) {
            $user->qq = $qq;
        }
        // $user->avatar = $avatar;
        $user->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @param string $email   邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->request('captcha');
        if (!$email || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verify_email = $verification->email;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        // 验证后发送积分
        if ($verify_email == 0) {
            $params  = ['userid'=>$user->id, 'act' => 'verify_email'];
            Hook::listen('update_user_score', $params, null, true);
        }

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @param string $email   手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        // if (!Validate::regex($mobile, "^1\d{10}$")) {
        if (!Validate::regex($mobile, "^\d+$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verify_mobile = $verification->mobile;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        // 验证后发送积分
        if ($verify_mobile == 0) {
            $params  = ['userid'=>$user->id, 'act' => 'verify_mobile'];
            Hook::listen('update_user_score', $params, null, true);
        }

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 修改用户字段值
     *
     * @param string $email   手机号
     * @param string $captcha 验证码
     */
    public function fields()
    {
        $user = $this->auth->getUser();
        $field = $this->request->request('field', '', 'trim,strip_tags,htmlspecialchars');
        $val = $this->request->request('val', '', 'trim,strip_tags,htmlspecialchars');
        if (!in_array($field, ['wechat', 'qq']) || !$val) {
            $this->error('参数错误');
        }

        if ($field=='wechat') {
            $user->wechat = $val;
        }
        if ($field=='qq') {
            if (!preg_match('/^[1-9][0-9]{4,14}$/', $val)) {
                $this->error('QQ号格式错误');
            }
            $user->qq = $val;
        }
        $user->save();
        // $user->avatar = $avatar;
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @param string $platform 平台名称
     * @param string $code     Code码
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->request("platform");
        $code = $this->request->request("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $thirdService = new ThirdService();
            $loginret = $thirdService->connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo'  => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 重置密码
     *
     * @param string $mobile      手机号
     * @param string $newpassword 新密码
     * @param string $captcha     验证码
     */
    public function resetpwd()
    {
        $type        = $this->request->request('type');
        $mobile      = $this->request->request('mobile');
        $email       = $this->request->request('email');
        $newpassword = $this->request->request('newpassword');
        $captcha     = $this->request->request('captcha');
        if (!$newpassword || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if ($type == 'mobile') {
            // if (!Validate::regex($mobile, "^1\d{10}$")) {
            if (!Validate::regex($mobile, "^\d+$")) {
                $this->error(__('Mobile is incorrect'), null, CommConst::E_MOBILE_FMERR);
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user) {
                $this->error(__('Account not exist'), null, CommConst::E_ACCOUNTERR);
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'), null, CommConst::E_CAPTCHAERR);
            }
            Sms::flush($mobile, 'resetpwd');
        } else {
            if (!Validate::is($email, "email")) {
                $this->error(__('Email is incorrect'), null, CommConst::E_EMAIL_FMERR);
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user) {
                $this->error(__('Account not exist'), null, CommConst::E_ACCOUNTERR);
            }
            $ret = Ems::check($email, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'), null, CommConst::E_CAPTCHAERR);
            }
            Ems::flush($email, 'resetpwd');
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $errs = $this->auth->getErrorAssoc();
            $this->error($errs['msg'], null, $errs['code']);
        }
    }
}
