<?php
namespace helpers;

class Constantes
{
    const ERR_NAODEFINIDO = ['code'=>9000,'message'=>'Erro não definido'];
    const ERR_USUARIO_NAO_ENCONTRADO = ['code'=>9001,'message'=>'Usuário não encontrado'];
    const ERR_LIVRO_NAO_ENCONTRADO = ['code'=>9100,'message'=>'Livro não encontrado'];
    const ERR_LIVRO_NAO_DISPONIVEL = ['code'=>9101,'message'=>'Livro não disponivel'];
    const ERR_EMPRESTIMO_NAO_CANCELADO = ['code'=>9201,'message'=>'Empréstimo não cancelado'];
    const ERR_EMPRESTIMO_NAO_LOCALIZADO = ['code'=>9202,'message'=>'Empréstimo não localizado'];
    const ERR_EMPRESTIMO_NAO_RETIRADO = ['code'=>9203,'message'=>'Registro de retirada de empréstimo inválido'];
    const ERR_EMPRESTIMO_DATA_DEVOLUCAO_REQUERIDA = ['code'=>9204,'message'=>'Data de previsão retirada / devolução é requerida'];
    const ERR_EMPRESTIMO_NAO_PREVISAO = ['code'=>9205,'message'=>'Não foi possivel marcar a previsão deste empréstimo'];
    const ERR_EMPRESTIMO_NAO_DEVOLVIDO = ['code'=>9206,'message'=>'Não foi possivel marcar este empréstimo como devolvido'];
    const ERR_USUARIO_LIVRO_STATUS_INVALIDO = ['code'=>9300,'message'=>'Status informado inválido'];
    const ERR_CHAMADO_INCLUSAO = ['code'=>9400,'message'=>'Erro ao incluir chamado'];

    const ERR_ASSUNTO_NAO_ENCONTRADO = ['code'=>9500,'message'=>'Assunto não encontrado'];
    const ERR_ASSUNTO_JA_EXISTENTE = ['code'=>9501,'message'=>'Já existe um assunto com este nome'];
    
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