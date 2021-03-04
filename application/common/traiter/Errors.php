<?php
namespace app\common\traiter;


trait Errors
{
    private $errors;

    public function setError($value)
    {
        $this->errors[] = $value;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFirstError()
    {
        return $this->errors ? reset($this->errors) : null;
    }
}