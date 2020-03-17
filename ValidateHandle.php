<?php

namespace Suolong\Validator;

class ValidateHandle
{
    function intCheck($data)
    {
        return is_numeric($data);
    }

    function intInCheck($data, $rules)
    {
        $i = explode(',', $rules);

        return is_numeric($data) && in_array($data, $i);
    }

    function intMaxCheck($data, $rule)
    {
        if (is_numeric($data)) {
            return $data <= (int) $rule;
        }
    }

    function intBetweenCheck($data, $rules)
    {
        if (is_numeric($data)) {
            list($min, $max) = explode(',', $rules);

            return $min <= $data && $data <= $max;
        }
    }

    function stringCheck($data)
    {
        return is_string($data);
    }

    function stringInCheck($data, $rules)
    {
        $i = explode(',', $rules);

        return is_string($data) && in_array($data, $i);
    }

    function stringLengthCheck($data, $rule)
    {
        if (is_string($data)) {
            return strlen($data) === (int) $rule;
        }
    }

    function stringMaxCheck($data, $rule)
    {
        if (is_string($data)) {
            return strlen($data) <= (int) $rule;
        }
    }

    function stringBetweenCheck($data, $rules)
    {
        if (is_string($data)) {
            list($min, $max) = explode(',', $rules);

            return $min <= strlen($data) && strlen($data) <= $max;
        }
    }

    function arrayCheck($data)
    {
        return is_array($data);
    }

    function arrayMaxCheck($data, $rule)
    {
        return count($data) <= (int) $rule;
    }

    function arrayBetweenCheck($data, $rules)
    {
        if (is_array($data)) {
            list($min, $max) = explode(',', $rules);

            return $min <= count($data) && count($data) <= $max;
        }
    }

    function safeCheck($data)
    {
        if (is_numeric($data) || is_string($data)) {
            return preg_match('@^[\S]+$@', $data) === 1;
        }
    }

    function phoneCheck($data)
    {
        if (is_numeric($data) || is_string($data)) {
            return preg_match('@^(086|86)?1[3-9]{2}\d{8}$@', $data) === 1;
        }
    }

    function eqCheck($data, $rules)
    {
        return $data == $rules;
    }

    function notEqCheck($data, $rules)
    {
        return $data != $rules;
    }
}