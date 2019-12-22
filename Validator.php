<?php

namespace SuoLong\Validator;

use InvalidArgumentException;
use RuntimeException;

class ValidatorInvalidArgumentException extends InvalidArgumentException
{
    function __construct()
    {
        parent::__construct('所提供数据格式有误');
    }
}

class ValidateFailException extends RuntimeException
{
    public $path;
    public $rule;

    function __construct($path, $rule)
    {
        $this->path = $path;
        $this->rule = $rule;
    }
}

class Validator
{
    static $handle;

    static function validate($data, $conditions)
    {
        foreach ($conditions as $column => $rules)
        {
            $columnInfo = [];
            $columnInfo['column'] = $column;
            $columnInfo['item'] = explode('.', $column);
            $columnInfo['count'] = count($columnInfo['item']);
            self::validateStack($data, $columnInfo, $rules);
        }
    }

    private static function validateStack($data, $columnInfo, $rules)
    {
        $firstPath = array_shift($columnInfo['item']);
        $columnInfo['count']--;

        if ($firstPath === '*')
        {
            if (is_string($data))
            {
                throw new ValidatorInvalidArgumentException;
            }

            $endDatum = $data;
        }
        else
        {
            $endDatum = $data[$firstPath];

            if ($columnInfo['count'] && is_string($endDatum))
            {
                throw new ValidatorInvalidArgumentException;
            }
        }

        $emptyString = is_string($endDatum) && strlen($endDatum) === 0;
        $emptyArray = is_array($endDatum) && count($endDatum) === 0;

        if ($emptyString || $emptyArray)
        {
            if (preg_match('@must@', $rules))
            {
                throw new ValidateFailException($columnInfo['column'], 'must');
            }

            return;
        }

        $rules = preg_replace('@[|]?must[|]?@', '', $rules);

        if ($firstPath === '*')
        {
            if ($columnInfo['count'])
            {
                foreach ($data as $datum)
                {
                    self::validateStack($datum, $columnInfo, $rules);
                }
            }
            else
            {
                foreach ($data as $datum)
                {
                    self::validatePipeLine($datum, explode('|', $rules));
                }
            }
        }
        else
        {
            if ($columnInfo['count'])
            {
                self::validateStack($endDatum, $columnInfo, $rules);
            }
            else
            {
                self::validatePipeLine($endDatum, $columnInfo, explode('|', $rules));
            }
        }
    }

    private static function validatePipeLine($data, $columnInfo, $rules)
    {
        foreach ($rules as $rule)
        {
            $ruleInfo = explode(':', $rule);
            $method = $ruleInfo[0] . 'Check';
            $result = self::$handle->{$method}($data);

            if ($result !== true)
            {
                throw new ValidateFailException($columnInfo['column'], $ruleInfo[0]);
            }
        }
    }
}