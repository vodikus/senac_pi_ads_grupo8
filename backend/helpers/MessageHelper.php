<?php
namespace helpers;

class MessageHelper
{

    public static function fmtException($e, $formataJson=true) {
        if ($formataJson) {
            $detalhe = '';
            if ($e instanceof \CLException || $e instanceof \CLConstException) {
                $detalhe = $e->getDetalhe();                
            }
            $json = [
                "codigo" =>  $e->getCode(),
                "mensagem" => $e->getMessage(),
                "detalhe" => $detalhe
            ];
            return json_encode($json);
        } else {
            return sprintf('[%05.0f] %s', $e->getCode(), $e->getMessage());
        }
    }

    public static function fmtMsgConst($const, $showCode=true, $arr = []) {
        $msg = '';
        if (count($arr) > 0) {
            $msg = sprintf(" (%s)", implode('|',array_map(['self','imprimeChaveValor'],array_keys($arr), array_values($arr))));
        }
        
        $constante = Constantes::getConst($const);
        if ($showCode)
            return sprintf('[%05.0f] %s', $constante['code'], $constante['message'] . $msg);
        else
            return sprintf('%s',$constante['message'] . $msg);
    }

    public static function fmtMsgConstJson($const, $detalhe="") {
        $constante = Constantes::getConst($const);        
        $json = [
            "codigo" => $constante['code'],
            "mensagem" => $constante['message'],
            "detalhe" => $detalhe
        ];
        return json_encode($json);
    }

}
