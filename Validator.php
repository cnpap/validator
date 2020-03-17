<?php

namespace Suolong\Validator;

use RuntimeException;

class Validator
{
    static $handles = [];

    static function validate($data, $conditions)
    {
        foreach ($conditions as $column => $rules)
        {
            $columnInfo           = [];
            $columnInfo['column'] = $column;
            $columnInfo['item']   = explode('.', $column);
            $columnInfo['count']  = count($columnInfo['item']);
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
            $endDatum = $data[$firstPath] ?? null;

            if ($columnInfo['count'] && is_string($endDatum))
            {
                throw new ValidatorInvalidArgumentException;
            }
        }

        $emptyString = is_string($endDatum) && strlen($endDatum) === 0;
        $emptyArray  = is_array($endDatum)  && count($endDatum) === 0;

        if ($emptyString || $emptyArray || is_null($endDatum))
        {
            if (preg_match('@must@', $rules))
            {
                throw new ValidateFailException($columnInfo['column'], 'must');
            }

            return;
        }

        $rules = preg_replace('@[&]?[&]?must[&]?[&]?@', '', $rules);

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
                    self::validatePipeLine($datum, $columnInfo, explode('&&', $rules));
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
                self::validatePipeLine($endDatum, $columnInfo, explode('&&', $rules));
            }
        }
    }

    private static function validatePipeLine($data, $columnInfo, $rules)
    {
        foreach ($rules as $rule)
        {
            $ruleInfo       = explode(':', $rule);
            $ruleName       = $ruleInfo[0];
            $ruleParams     = $ruleInfo[1] ?? null;
            $method         = $ruleName . 'Check';
            $validateParams = [$data];

            if ($ruleParams !== null)
            {
                $validateParams[] = $ruleParams;
            }

            $result       = false;
            $methodExists = false;
            
            foreach (self::$handles as $handle) 
            {
                if (method_exists($handle, $method))
                {
                    $methodExists = true;
                    $result       = $handle->{$method}(...$validateParams);
                    continue;
                }
            }

            if ($methodExists === false)
            {
                throw new RuntimeException('验证器缺少验证规则:' . $ruleName);
            }

            if ($result !== true)
            {
                throw new ValidateFailException($columnInfo['column'], $ruleName, $ruleParams);
            }
        }
    }
}
