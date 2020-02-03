<?php

namespace Suolong\Validator;

use InvalidArgumentException;

class ValidatorInvalidArgumentException extends InvalidArgumentException
{
    function __construct()
    {
        parent::__construct('所提供数据格式有误');
    }
}