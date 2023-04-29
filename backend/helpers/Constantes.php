<?php
namespace helpers;

class Constantes
{
    /** Mensagens - Empréstimos */
    const MSG_EMPRESTIMO_SOLICITADO_SUCESSO  = ['code'=>1301,'message'=>'Empréstimo solicitado com sucesso'];
    const MSG_EMPRESTIMO_DEVOLVIDO_SUCESSO  = ['code'=>1302,'message'=>'Empréstimo devolvido com sucesso'];
    const MSG_EMPRESTIMO_CANCELADO_SUCESSO  = ['code'=>1303,'message'=>'Solicitação de Empréstimo cancelada com sucesso'];
    const MSG_EMPRESTIMO_PREVISAO_SUCESSO  = ['code'=>1304,'message'=>'Previsão de Empréstimo registrada com sucesso'];
    const MSG_EMPRESTIMO_RETIRADA_SUCESSO  = ['code'=>1305,'message'=>'Retirada de Empréstimo registrada com sucesso'];

    /** Erros - Empréstimos  */
    const ERR_EMPRESTIMO_NAO_CANCELADO = ['code'=>9301,'message'=>'Empréstimo não cancelado'];
    const ERR_EMPRESTIMO_NAO_LOCALIZADO = ['code'=>9302,'message'=>'Empréstimo não localizado'];
    const ERR_EMPRESTIMO_NAO_RETIRADO = ['code'=>9303,'message'=>'Registro de retirada de empréstimo inválido'];
    const ERR_EMPRESTIMO_DATA_DEVOLUCAO_REQUERIDA = ['code'=>9304,'message'=>'Data de previsão retirada / devolução é requerida'];
    const ERR_EMPRESTIMO_NAO_PREVISAO = ['code'=>9305,'message'=>'Não foi possivel marcar a previsão deste empréstimo'];
    const ERR_EMPRESTIMO_NAO_DEVOLVIDO = ['code'=>9306,'message'=>'Não foi possivel marcar este empréstimo como devolvido'];   
    

    /** Erros - HTTP */
    const ERR_NAO_AUTORIZADO  = ['code'=>401,'message'=>'Acesso não autorizado'];
    const ERR_METODO_NAO_PERMITIDO  = ['code'=>405,'message'=>'Método não permitido'];
    const ERR_ACAO_INDISPONIVEL  = ['code'=>501,'message'=>'Ação Indisponível'];

    /** Erros - Genéricos  */
    const ERR_NAODEFINIDO = ['code'=>9000,'message'=>'Erro não definido'];
    const ERR_ID_INVALIDO = ['code'=>9001,'message'=>'Identificador inválido'];
    
    /** Erros - Usuários  */
    const ERR_USUARIO_NAO_ENCONTRADO = ['code'=>9100,'message'=>'Usuário não encontrado'];
    const ERR_USUARIO_LIVRO_STATUS_INVALIDO = ['code'=>9130,'message'=>'Status informado inválido'];

    /** Erros - Livros  */
    const ERR_LIVRO_NAO_ENCONTRADO = ['code'=>9200,'message'=>'Livro não encontrado'];
    const ERR_LIVRO_NAO_DISPONIVEL = ['code'=>9201,'message'=>'Livro não disponivel'];

    /** Erros - Chamados */
    const ERR_CHAMADO_INCLUSAO = ['code'=>9500,'message'=>'Erro ao incluir chamado'];

    /** Erros - Assuntos  */
    const ERR_ASSUNTO_NAO_ENCONTRADO = ['code'=>9600,'message'=>'Assunto não encontrado'];
    const ERR_ASSUNTO_JA_EXISTENTE = ['code'=>9601,'message'=>'Já existe um assunto com este nome'];
    
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