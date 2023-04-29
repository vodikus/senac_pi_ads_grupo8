<?php
namespace helpers;

class MessageHelper
{

    public static function fmtException($e) {
        return sprintf('[%05.0f] %s', $e->getCode(), $e->getMessage());
    }

    public static function fmtMsgConst($const, $showCode=true) {
        if ($showCode)
            return sprintf('[%05.0f] %s', $const['code'], $const['message']);
        else
            return sprintf('%s',$const['message']);
    }
}
