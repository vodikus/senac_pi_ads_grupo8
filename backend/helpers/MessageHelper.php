<?php
namespace helpers;

class MessageHelper
{

    public static function fmtException($e) {
        return sprintf('[%05.0f] %s', $e->getCode(), $e->getMessage());
    }

    public static function fmtMsgConst($const) {
        return sprintf('[%05.0f] %s', $const['code'], $const['message']);
    }
}
