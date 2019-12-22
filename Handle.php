<?php

class Handle
{
    function intCheck($data)
    {
        if (is_int($data)) 
        {
            return true;
        }
    }

    function numtricCheck($data)
    {
        if (is_numeric($data))
        {
            return true;
        }
    }

    function stringCheck($data)
    {
        if (is_string($data) || is_numeric($data))
        {
            return true;
        }
    }

    function arrayCheck($data)
    {
        if (is_array($data))
        {
            return true;
        }
    }
}