<?php
namespace helpers;

class Constantes
{
    /**
     * @apiDefine ERR_GENERICOS
     *
     * @apiError (Erro 4xx) 401 Não autorizado
     * @apiError (Erro 4xx) 405 Método não permitido
     * @apiError (Erro 5xx) 501 Ação Indisponível
     * @apiError (Erro 5xx) 9000 Erro não definido
     * @apiError (Erro 5xx) 9001 Identificador inválido
     * @apiError (Erro 5xx) 9004 A entrada deve ser um JSON válido
     *
     */

    /**
     * @apiDefine SAIDA_PADRAO
     *
     * @apiSuccess {Number} codigo Código da mensagem
     * @apiSuccess {String} mensagem Mensagem de retorno
     * @apiSuccess {Object} detalhe Objeto contendo detalhes do retorno
     * @apiSuccess {Number} detalhe.chamadoId  Id do assunto inserido
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "codigo": 1234,
     *         "mensagem": "Sua operação foi realizada com sucesso",
     *         "detalhe": ""
     *     }
     *
     */     


    /** Erros - HTTP */
    const ERR_NAO_AUTORIZADO = ['code' => 401, 'message' => 'Acesso não autorizado'];
    const ERR_NAO_ENCONTRADO = ['code' => 404, 'message' => 'Recurso não encontrado'];
    const ERR_METODO_NAO_PERMITIDO = ['code' => 405, 'message' => 'Método não permitido'];
    const ERR_ACAO_INDISPONIVEL = ['code' => 501, 'message' => 'Ação Indisponível'];

    /** Mensagens - Genéricos  */
    const MSG_TOKEN_OK = ['code' => 1000, 'message' => 'Token OK'];

    /** Erros - Genéricos  */
    const ERR_NAODEFINIDO = ['code' => 9000, 'message' => 'Erro não definido'];
    const ERR_ID_INVALIDO = ['code' => 9001, 'message' => 'Identificador inválido'];
    const ERR_TOKEN_INVALIDO = ['code' => 9002, 'message' => 'Token inválido'];
    const ERR_TOKEN_REQUERIDO = ['code' => 9003, 'message' => 'É necessário informar o token'];
    const ERR_JSON_INVALIDO = ['code' => 9004, 'message' => 'A entrada deve ser um JSON válido'];
    
    /** Mensagens - Usuários  */
    const MSG_USUARIO_CADASTRO_SUCESSO = ['code' => 1200, 'message' => 'Usuário cadastrado com sucesso'];
    const MSG_USUARIO_DELETADO_SUCESSO = ['code' => 1201, 'message' => 'Usuário deletado com sucesso'];
    const MSG_USUARIO_ATUALIZADO_SUCESSO = ['code' => 1202, 'message' => 'Usuário atualizado com sucesso'];
    const MSG_USUARIO_ASSUNTO_VINCULADO = ['code' => 1203, 'message' => 'Assunto vinculado ao usuário com sucesso'];
    const MSG_USUARIO_ASSUNTO_DESVINCULADO = ['code' => 1204, 'message' => 'Assunto desvinculado ao usuário com sucesso'];
    const MSG_USUARIO_LIVRO_VINCULADO = ['code' => 1205, 'message' => 'Livro vinculado ao usuário com sucesso'];
    const MSG_USUARIO_LIVRO_DESVINCULADO = ['code' => 1206, 'message' => 'Livro desvinculado ao usuário com sucesso'];
    const MSG_USUARIO_LIVRO_STATUS_SUCESSO = ['code' => 1207, 'message' => 'Status do livro alterado com sucesso'];
    /** Erros - Usuários  */
    const ERR_USUARIO_NAO_ENCONTRADO = ['code' => 9100, 'message' => 'Usuário não encontrado'];
    const ERR_EMAIL_SENHA_INVALIDO = ['code' => 9101, 'message' => 'E-mail ou senha não conferem'];
    const ERR_EMAIL_SENHA_REQUERIDO = ['code' => 9102, 'message' => 'É necessário informar e-mail e senha'];
    const ERR_EMAIL_EXISTENTE= ['code' => 9103, 'message' => 'E-mail já cadastrado'];
    const ERR_CPF_EXISTENTE= ['code' => 9104, 'message' => 'CPF já cadastrado'];
    const ERR_USUARIO_BLOQUEADO = ['code' => 9105, 'message' => 'Usuário bloqueado'];
    const ERR_USUARIO_ASSUNTO_VINCULO_EXISTENTE = ['code' => 9105, 'message' => 'Este assunto já está vinculado a este usuário'];
    const ERR_USUARIO_ASSUNTO_VINCULO_NAO_ENCONTRADO = ['code' => 9106, 'message' => 'Este usuário não está vinculado a este assunto'];
    const ERR_USUARIO_LIVRO_VINCULO_EXISTENTE = ['code' => 9107, 'message' => 'Este livro já está vinculado a este usuário'];
    const ERR_USUARIO_LIVRO_VINCULO_NAO_ENCONTRADO = ['code' => 9108, 'message' => 'Este livro não está vinculado a este usuário'];
    const ERR_USUARIO_LIVRO_STATUS_INVALIDO = ['code' => 9130, 'message' => 'Status informado inválido'];

    /** Mensagens - Livros  */
    const MSG_LIVRO_CADASTRO_SUCESSO = ['code' => 1200, 'message' => 'Livro cadastrado com sucesso'];
    const MSG_LIVRO_DELETADO_SUCESSO = ['code' => 1201, 'message' => 'Livro deletado com sucesso'];
    const MSG_LIVRO_ATUALIZADO_SUCESSO = ['code' => 1202, 'message' => 'Livro atualizado com sucesso'];
    const MSG_LIVRO_AVALIADO_SUCESSO = ['code' => 1203, 'message' => 'Livro avaliado com sucesso'];
    const MSG_LIVRO_FAVORITO_SUCESSO = ['code' => 1203, 'message' => 'Livro adicionado a lista de favoritos'];
    const MSG_LIVRO_DESFAVORITO_SUCESSO = ['code' => 1203, 'message' => 'Livro removido da lista de favoritos'];

    /** Erros - Livros  */
    const ERR_LIVRO_NAO_ENCONTRADO = ['code' => 9200, 'message' => 'Livro não encontrado'];
    const ERR_LIVRO_NAO_DISPONIVEL = ['code' => 9201, 'message' => 'Livro não disponivel'];
    const ERR_LIVRO_JA_EXISTENTE = ['code' => 9202, 'message' => 'Já existe um livro com este ISBN'];
    const ERR_LIVRO_JA_AVALIADO = ['code' => 9203, 'message' => 'Livro já avaliado'];
    const ERR_LIVRO_JA_FAVORITO = ['code' => 9204, 'message' => 'Este livro já foi adicionado a lista de favoritos'];

    /** Mensagens - Empréstimos */
    const MSG_EMPRESTIMO_SOLICITADO_SUCESSO = ['code' => 1300, 'message' => 'Empréstimo solicitado com sucesso'];
    const MSG_EMPRESTIMO_DEVOLVIDO_SUCESSO = ['code' => 1301, 'message' => 'Empréstimo devolvido com sucesso'];
    const MSG_EMPRESTIMO_CANCELADO_SUCESSO = ['code' => 1302, 'message' => 'Solicitação de Empréstimo cancelada com sucesso'];
    const MSG_EMPRESTIMO_PREVISAO_SUCESSO = ['code' => 1303, 'message' => 'Previsão de Empréstimo registrada com sucesso'];
    const MSG_EMPRESTIMO_RETIRADA_SUCESSO = ['code' => 1304, 'message' => 'Retirada de Empréstimo registrada com sucesso'];

    /** Erros - Empréstimos  */
    const ERR_EMPRESTIMO_NAO_CANCELADO = ['code' => 9300, 'message' => 'Empréstimo não cancelado'];
    const ERR_EMPRESTIMO_NAO_LOCALIZADO = ['code' => 9301, 'message' => 'Empréstimo não localizado'];
    const ERR_EMPRESTIMO_NAO_RETIRADO = ['code' => 9302, 'message' => 'Registro de retirada de empréstimo inválido'];
    const ERR_EMPRESTIMO_DATA_DEVOLUCAO_REQUERIDA = ['code' => 9303, 'message' => 'Data de previsão retirada / devolução é requerida'];
    const ERR_EMPRESTIMO_NAO_PREVISAO = ['code' => 9304, 'message' => 'Não foi possivel marcar a previsão deste empréstimo'];
    const ERR_EMPRESTIMO_NAO_DEVOLVIDO = ['code' => 9305, 'message' => 'Não foi possivel marcar este empréstimo como devolvido'];


    /** Mensagens - Autores  */
    const MSG_AUTOR_CADASTRO_SUCESSO = ['code' => 1400, 'message' => 'Autor cadastrado com sucesso.'];
    const MSG_AUTOR_DELETADO_SUCESSO = ['code' => 1401, 'message' => 'Autor deletado com sucesso.'];
    const MSG_AUTOR_ATUALIZADO_SUCESSO = ['code' => 1402, 'message' => 'Autor atualizado com sucesso.'];
    const MSG_AUTOR_LIVRO_VINCULADO_SUCESSO = ['code' => 1402, 'message' => 'Autor vinculado ao livro com sucesso.'];
    const MSG_AUTOR_LIVRO_DESVINCULADO_SUCESSO = ['code' => 1402, 'message' => 'Autor desvinculado ao livro com sucesso.'];

    /** Erros - Autores  */
    const ERR_AUTOR_NAO_ENCONTRADO = ['code' => 9400, 'message' => 'Autor não encontrado'];
    const ERR_AUTOR_JA_EXISTENTE = ['code' => 9401, 'message' => 'Já existe um autor com este nome'];
    const ERR_AUTOR_DELETAR_FK = ['code' => 9402, 'message' => 'Este autor não pode ser deletado pois está vinculado a um ou mais livros'];
    const ERR_AUTOR_VINCULO_EXISTE = ['code' => 9403, 'message' => 'Este Autor já está vinculado a este livro'];
    const ERR_AUTOR_VINCULO_NAO_ENCONTRADO = ['code' => 9403, 'message' => 'Este autor não está vinculado a este livro'];


    /** Mensagens - Chamados  */
    const MSG_CHAMADO_CADASTRO_SUCESSO = ['code' => 1500, 'message' => 'Chamado cadastrado com sucesso.'];
    const MSG_CHAMADO_ATUALIZADO_SUCESSO = ['code' => 1501, 'message' => 'Chamado atualizado com sucesso.'];
    const MSG_CHAMADO_DETALHE_CADASTRO_SUCESSO = ['code' => 1510, 'message' => 'Detalhe do chamado cadastrado com sucesso.'];

    /** Erros - Chamados */
    const ERR_CHAMADO_NAO_ENCONTRADO = ['code' => 9501, 'message' => 'Chamado não encontrado'];
    const ERR_CHAMADO_INCLUSAO = ['code' => 9501, 'message' => 'Erro ao incluir chamado'];
    const ERR_CHAMADO_ALTERACAO = ['code' => 9502, 'message' => 'Erro ao alterar chamado'];
    const ERR_CHAMADO_STATUS_INVALIDO = ['code' => 9503, 'message' => 'Status do chamado inválido'];
    const ERR_CHAMADO_DETALHE_INCLUSAO = ['code' => 9510, 'message' => 'Erro ao incluir detalhe do chamado'];

    /** Mensagens - Assuntos  */
    const MSG_ASSUNTO_CADASTRO_SUCESSO = ['code' => 1600, 'message' => 'Assunto cadastrado com sucesso.'];
    const MSG_ASSUNTO_DELETADO_SUCESSO = ['code' => 1601, 'message' => 'Assunto deletado com sucesso.'];
    const MSG_ASSUNTO_ATUALIZADO_SUCESSO = ['code' => 1602, 'message' => 'Assunto atualizado com sucesso.'];
    const MSG_LIVRO_ASSUNTO_VINCULADO_SUCESSO = ['code' => 1603, 'message' => 'Assunto vinculado ao livro com sucesso'];
    const MSG_LIVRO_ASSUNTO_DESVINCULADO_SUCESSO = ['code' => 1604, 'message' => 'Assunto desvinculado ao livro com sucesso'];

    /** Erros - Assuntos  */
    const ERR_ASSUNTO_NAO_ENCONTRADO = ['code' => 9600, 'message' => 'Assunto não encontrado'];
    const ERR_ASSUNTO_JA_EXISTENTE = ['code' => 9601, 'message' => 'Já existe um assunto com este nome'];
    const ERR_ASSUNTO_DELETAR_FK = ['code' => 9602, 'message' => 'Este assunto não pode ser deletado pois está vinculado a um ou mais livros'];
    const ERR_ASSUNTO_VINCULO_EXISTE = ['code' => 9602, 'message' => 'Este assunto já está vinculado a este livro'];
    const ERR_ASSUNTO_VINCULO_NAO_ENCONTRADO = ['code' => 9602, 'message' => 'Este assunto não está vinculado a este livro'];
    
    /** Mensagens - Endereços  */
    const MSG_ENDERECO_CADASTRO_SUCESSO = ['code' => 1700, 'message' => 'Endereço cadastrado com sucesso'];
    const MSG_ENDERECO_DELETADO_SUCESSO = ['code' => 1701, 'message' => 'Endereço deletado com sucesso'];
    const MSG_ENDERECO_ATUALIZADO_SUCESSO = ['code' => 1702, 'message' => 'Endereço atualizado com sucesso'];

    /** Erros - Endereços  */
    const ERR_ENDERECO_NAO_ENCONTRADO = ['code' => 9700, 'message' => 'Endereço não encontrado'];
    const ERR_ENDERECO_JA_EXISTENTE = ['code' => 9701, 'message' => 'Este endereço já está cadastrado'];

    /** Mensagens - Chat  */
    const MSG_CHAT_ENVIO_SUCESSO = ['code' => 1800, 'message' => 'Mensagem enviada com sucesso'];

    /** Erros - Chat  */
    const ERR_CHAT_ENVIO = ['code' => 9800, 'message' => 'Falha no envio da mensagem'];

    /** Mensagens - Amigos  */
    const MSG_AMIGO_ADICIONADO_SUCESSO = ['code' => 1900, 'message' => 'Amigo adicionado com sucesso'];

    /** Erros - Amigos  */
    const ERR_AMIGO_NAO_ENCONTRADO = ['code' => 9900, 'message' => 'Estes usuários não são amigos'];


    public static function getConst($const = "ERR_NAODEFINIDO")
    {
        if (!defined("self::$const")) {
            error_log("Constante de erro não definida: $const");
            return self::ERR_NAODEFINIDO;
        }
        return constant("self::$const");
    }
    public static function getFmt($const = "ERR_NAODEFINIDO")
    {
        return self::getConst($const)['code'] . ": " . self::getConst($const)['message'];
    }

    public static function getCode($const = "ERR_NAODEFINIDO")
    {
        return self::getConst($const)['code'];
    }

    public static function getMsg($const = "ERR_NAODEFINIDO")
    {
        return self::getConst($const)['message'];
    }

}