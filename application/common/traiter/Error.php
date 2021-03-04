<?php
namespace app\common\traiter;

use app\common\constant\CommConst;

trait Error
{
    private $error;
    private $_error_code = CommConst::E_ERROR;

    /**
     * 设置错误信息
     *
     * @param $error string 错误信息
     * @return mixed
     */
    public function setError($error, $code = CommConst::E_ERROR)
    {
        $this->_error = $error;
        $this->_error_code = $code;
        return $this;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->_error ? __($this->_error) : '';
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getErrorAssoc()
    {
        return [
            'msg'  => $this->_error ? __($this->_error) : '',
            'code' => $this->_error_code,
        ];
    }
}