<?php

class StringHelper
{
    public static function addQuotes($str) {
        return sprintf("'%s'", $str);
    }
}
