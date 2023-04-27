<?php
// namespace Helpers;
class StringHelper
{
    public static function addQuotes($str) {
        return sprintf("'%s'", $str);
    }
}
