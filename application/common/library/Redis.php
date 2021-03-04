<?php

namespace app\common\library;

use think\Exception;
use think\Config;

if (!extension_loaded('redis')) {
    throw new \RuntimeException('Require redis extension!');
}

class Redis
{
    /**
     * @var object 对象实例
     */
    protected static $instance;

    protected $config = [
        'host'            => '127.0.0.1',
        'port'            => 6379,
        'timeout'         => 0,
        'prefix'          => null,
        'persistent_id'   => '',        // persistent_id 持久连接设置标识
        'unix_socket'     => '',        // eg: /tmp/redis.sock
        'password'        => '',
        'database'        => 0,         // dbindex, the database number to switch to
        'break_reconnect' => false    // 断线自动重连
    ];

    protected $handler;

    public function __construct(array $config = [])
    {
        if (!$config) {
            $config = Config::get('cache.redis');
        }

        if ($config) {
            $this->config = array_merge($this->config, $config);
        }
        // var_dump($this->config);
    }

    /**
     * 初始化
     * @access public
     * @param array $options 参数
     * @return Redis
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * 特殊处理删除(兼容老版本Redis)
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        try {
            // 调用__call
            return $this->del($name);
        } catch (\Exception $e) {
            if (false !== stripos($e->getMessage(), 'NOAUTH')) {
                throw $e;
            }
            // 调用__call
            return $this->delete($name);
        }
    }

    public function __call($fn, array $args)
    {
        try {
            return $args
                ? call_user_func_array([$this->connect(), $fn], $args)
                : $this->connect()->$fn();
        } catch (\RedisException $e) {
            if ($this->isBreak($e)) { // 断线重连
                $this->handler = null;
                return $args
                    ? call_user_func_array([$this->connect(), $fn], $args)
                    : $this->connect()->$fn();
            }
            throw $e;
        }
    }

    public function connect()
    {
        if ($this->handler) {
            return $this->handler;
        }

        $config = $this->config;
        $handler = new \Redis();

        // 优先使用unix socket
        $conn_args = $config['unix_socket']
            ? [$config['unix_socket']]
            : [$config['host'], $config['port'], $config['timeout']];

        if ($this->isPersistent()) {
            $conn_args[] = $config['persistent_id'];
            $conn = call_user_func_array([$handler, 'pconnect'], $conn_args);
        } else {
            $conn = call_user_func_array([$handler, 'connect'], $conn_args);
        }

        if (!$conn) {
            throw new Exception('Cannot connect redis');
        }

        if ($config['password'] && !$handler->auth($config['password'])) {
            throw new Exception('Invalid redis password');
        }

        if ($config['database'] && !$handler->select($config['database'])) {
            throw new Exception('Select redis database[' . $config['database'] . '] failed');
        }

        if (isset($config['prefix'])) {
            $handler->setOption(\Redis::OPT_PREFIX, $config['prefix']);
        }

        return $this->handler = $handler;
    }

    public function disconnect()
    {
        if ($this->handler instanceof \Redis) {
            $this->handler->close();
            $this->handler = null;
        }

        return $this;
    }

    protected function isPersistent()
    {
        $config = $this->config;

        return $config['persistent_id'] && !$config['unix_socket'];
    }

    /**
     * 是否断线
     * @access protected
     * @param \PDOException|\Exception  $e 异常对象
     * @return bool
     */
    protected function isBreak($e)
    {
        if (!$this->config['break_reconnect']) {
            return false;
        }

        $info = [
            'Connection refused',
            'Connection lost',
        ];

        $error = $e->getMessage();

        foreach ($info as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }
}