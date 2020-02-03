<?php

namespace Suolong\Validator;

use RuntimeException;

class ValidateFailException extends RuntimeException
{
    public $path;
    public $ruleName;
    public $ruleParams;

    function __construct($path, $ruleName, $ruleParams = null)
    {
        $this->path = $path;
        $this->ruleName = $ruleName;
        $this->ruleParams = $ruleParams;
    }
}