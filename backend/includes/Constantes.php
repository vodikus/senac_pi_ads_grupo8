<?php

class Constantes
{
    const ERR_NAODEFINIDO = ['code'=>1,'message'=>'Erro não definido'];
    const ERR_USUARIO_NAO_ENCONTRADO = ['code'=>2,'message'=>'Usuário não encontrado'];
    const ERR_LIVRO_NAO_ENCONTRADO = ['code'=>100,'message'=>'Livro não encontrado'];
    const ERR_LIVRO_NAO_DISPONIVEL = ['code'=>101,'message'=>'Livro não disponivel'];
    const ERR_EMPRESTIMO_NAO_CANCELADO = ['code'=>201,'message'=>'Empréstimo não cancelado'];
    const ERR_EMPRESTIMO_NAO_LOCALIZADO = ['code'=>202,'message'=>'Empréstimo não localizado'];
    const ERR_EMPRESTIMO_NAO_RETIRADO = ['code'=>203,'message'=>'Registro de retirada de empréstimo inválido'];
    const ERR_EMPRESTIMO_DATA_DEVOLUCAO_REQUERIDA = ['code'=>204,'message'=>'Data de previsão retirada / devolução é requerida'];
    const ERR_EMPRESTIMO_NAO_PREVISAO = ['code'=>205,'message'=>'Não foi possivel marcar a previsão deste empréstimo'];
    const ERR_EMPRESTIMO_NAO_DEVOLVIDO = ['code'=>206,'message'=>'Não foi possivel marcar este empréstimo como devolvido'];
    
    public static function getConst($const = "ERR_NAODEFINIDO") {
        if ( !defined("self::$const") ) {
            error_log("Constante de erro não definida: $const");
            return self::ERR_NAODEFINIDO;
        }
        return constant("self::$const");
    }
    public static function getFmt($const = "ERR_NAODEFINIDO") {
        return self::getConst($const)['code'] . ": " . self::getConst($const)['message'];
    }
    
    public static function getCode($const = "ERR_NAODEFINIDO") {
        return self::getConst($const)['code'];
    }
    
    public static function getMsg($const = "ERR_NAODEFINIDO") {
        return self::getConst($const)['message'];
    }

}