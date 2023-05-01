<?php
// namespace Helpers;
class StringHelper
{
    public static function addQuotes($str) {
        return sprintf("'%s'", $str);
    }

    public static function imprimeChaveValor($chave, $valor, $separador=":") {
        return sprintf("%s%s%s",$chave,$separador,$valor);
    }

    public static function formataArrayChaveValor($arr) {
        if (count($arr) > 0) {
            return sprintf("%s", implode('|',array_map(['StringHelper','imprimeChaveValor'],array_keys($arr), array_values($arr))));
        }
    }
}
