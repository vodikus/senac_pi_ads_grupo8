<?php
namespace helpers;

class MessageHelper
{

    public static function fmtException($e) {
        return sprintf('[%05.0f] %s', $e->getCode(), $e->getMessage());
    }

    public static function fmtMsgConst($const, $showCode=true) {
        $constante = Constantes::getConst($const);
        if ($showCode)
            return sprintf('[%05.0f] %s', $constante['code'], $constante['message']);
        else
            return sprintf('%s',$constante['message']);
    }
}
