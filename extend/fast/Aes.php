<?php
namespace fast;

/**
 * Class Aes
 */
class Aes
{
    /**
     * 加密
     * @param string $String
     * @param string $key
     * @return string
     */
    public function encrypt($String='', $key=''){

        $data = openssl_encrypt($String, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        $data = strtolower(bin2hex($data));
        return $data;
    }

    /**
     * 解密
     * @param string $String
     * @param string $key
     * @return string
     */
    public function decrypt($String='', $key=''){
        if(!is_string($String)) return $String;
        $len = strlen($String);
        if ($len % 2) {
            return $String;
        }else if (strspn($String, '0123456789abcdefABCDEF') != $len) {
            return $String;
        }
        $String = hex2bin($String);
        $decrypted = openssl_decrypt($String, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return $decrypted;
    }


    /**
     * 加密数组
     * @param $data
     * @param string $secretKey
     * @param array $key
     * @return mixed
     */
    public function encryptArray($data,$secretKey='',$key=[])
    {
        if (empty($key)) return $data;
        foreach ($data as $kk => $vv) {
            if (is_array($vv)) {
                foreach ($vv as $k => $v) {
                    if (in_array($k, $key)) {
                        $data[$kk][$k] = $this->encrypt($v, $secretKey);
                    }
                }
            } else {
                if (in_array($kk, $key)) {
                    $data[$kk] = $this->encrypt($vv, $secretKey);
                }
            }
        }
        return $data;
    }


    /**
     * @param $data
     * @param string $secretKey
     * @param array $key
     */
    public function decryptArray($data,$secretKey='',$key=[])
    {
        if (empty($key)) return $data;
        foreach ($data as $kk => $vv) {
            if (is_array($vv)) {
                foreach ($vv as $k => $v) {
                    if (in_array($k, $key)) {
                        $data[$kk][$k] = $this->decrypt($v, $secretKey);
                    }
                }
            } else {
                if (in_array($kk, $key)) {
                    $data[$kk] = $this->decrypt($vv, $secretKey);
                }
            }
        }
        return $data;
    }
}