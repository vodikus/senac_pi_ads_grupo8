<?php
include_once 'includes/BaseController.php';
include_once 'models/UsuarioModel.php';
include_once 'models/UsuarioAssuntoModel.php';
include_once 'models/UsuarioLivroModel.php';
include_once 'models/AmigoModel.php';
include_once 'models/ImagemModel.php';
require_once 'helpers/FileHelper.php';

use helpers\MessageHelper;
use helpers\FileHelper;

class UsuarioController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function processarRequisicao($metodo = '', $params = [])
    {
        switch ($metodo) {
            case 'GET':
                switch ($params['acao']) {
                    case 'listar':
                        if ($this->isAuth() && $this->getFieldFromToken('roles') == 'admin') {
                            $this->listar();
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'buscar':
                        if ($this->isAuth(false)) {
                            $this->buscar($params['level1'], ($this->getFieldFromToken('roles') == 'admin'), $this->getFieldFromToken('uid'));
                        } else {
                            $this->buscar($params['level1'], false);
                        }
                        break;
                    case 'meu-perfil':
                        if ($this->isAuth()) {
                            $this->buscar($this->getFieldFromToken('uid'), true);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'amigos':
                        if ($this->isAuth()) {
                            $this->listarAmigos($this->getFieldFromToken('uid'));
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'POST':
                $dados = $this->pegarArrayJson();
                switch ($params['acao']) {
                    case 'adicionar':
                        $this->adicionar($dados);
                        break;
                    case 'vincularAssunto':
                        if ($this->isAuth()) {
                            $this->vincularAssunto($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularAssunto':
                        if ($this->isAuth()) {
                            $this->desvincularAssunto($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'vincularLivro':
                        if ($this->isAuth()) {
                            $this->vincularLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularLivro':
                        if ($this->isAuth()) {
                            $this->desvincularLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'bloquearUsuario':
                        if ($this->isAuth()) {
                            $this->bloquearUsuario($this->getFieldFromToken('uid'), $params['level1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'adicionarAmigo':
                        if ($this->isAuth()) {
                            $this->adicionarAmigo($this->getFieldFromToken('uid'), $params['level1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'enviarFoto':
                        if ($this->isAuth()) {
                            $this->enviarFoto($this->getFieldFromToken('uid'), $_FILES);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'PUT':
                $dados = $this->pegarArrayJson();
                switch ($params['acao']) {
                    case 'atualizar':
                        error_log($this->getFieldFromToken('roles'));
                        if ($this->isAuth() && $this->getFieldFromToken('roles') == 'admin') {
                            $this->atualizar($params['level1'], $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'meu-perfil':
                        if ($this->isAuth()) {
                            $this->atualizar($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ($this->isAuth()) {
                            $this->deletar($params['level1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desbloquearUsuario':
                        if ($this->isAuth()) {
                            $this->desbloquearUsuario($this->getFieldFromToken('uid'), $params['level1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'removerAmigo':
                        if ($this->isAuth()) {
                            $this->removerAmigo($this->getFieldFromToken('uid'), $params['level1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'removerFoto':
                        if ($this->isAuth()) {
                            $this->removerFoto($this->getFieldFromToken('uid'));
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            default:
                $this->httpRawResponse(405, MessageHelper::fmtMsgConstJson('ERR_METODO_NAO_PERMITIDO'));
                break;
        }
    }


    /**
     * @apiDefine ERR_USUARIO_PADRAO
     *
     * @apiError (Erro 4xx) 9100 Usuário não encontrado.
     *
     */

    /**
     * @apiDefine SAIDA_PADRAO_USUARIO
     *
     * @apiSuccess {Number} uid ID do usuário
     * @apiSuccess {String} email E-mail do usuário
     * @apiSuccess {String} nome Nome do usuário
     * @apiSuccess {String} cpf CPF
     * @apiSuccess {String} nascimeto Data de Nascimento
     * @apiSuccess {String} sexo Sexo
     * @apiSuccess {Timestamp} dh_atualizacao  Data/Hora de atualização
     * @apiSuccess {Timestamp} dh_criacao  Data/Hora de criação
     * @apiSuccess {String} avatar Caminho da imagem do avatar do usuário
     * @apiSuccess {String} apelido Apelido
     * @apiSuccess {String="A","D"} status Status
     * @apiSuccess {String="user","admin"} role Perfil do usuário
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *       {
     *          "uid": "39",
     *          "email": "teste@teste.com.br9",
     *          "nome": "José da Silva",
     *          "cpf": "1234562890117",
     *          "nascimento": "2000-01-01",
     *          "sexo": "M",
     *          "dh_atualizacao": "2023-04-14 20:34:48",
     *          "dh_criacao": "2023-04-14 20:34:48",
     *          "avatar": null,
     *          "apelido": null,
     *          "status": "A",
     *          "role": "user"
     *       }
     *
     */

    /**
     * @apiDefine SAIDA_LISTA_USUARIOS
     *
     * @apiSuccess {Object[]} usuarios Lista de usuários
     * @apiSuccess {Number} usuarios.uid ID do usuário
     * @apiSuccess {String} usuarios.email E-mail do usuário
     * @apiSuccess {String} usuarios.nome Nome do usuário
     * @apiSuccess {String} usuarios.cpf CPF
     * @apiSuccess {String} usuarios.nascimeto Data de Nascimento
     * @apiSuccess {String} usuarios.sexo Sexo
     * @apiSuccess {Timestamp} usuarios.dh_atualizacao  Data/Hora de atualização
     * @apiSuccess {Timestamp} usuarios.dh_criacao  Data/Hora de criação
     * @apiSuccess {String} usuarios.avatar Caminho da imagem do avatar do usuário
     * @apiSuccess {String} usuarios.apelido Apelido
     * @apiSuccess {String="A","D"} usuarios.status Status
     * @apiSuccess {String="user","admin"} usuarios.role Perfil do usuário
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *          "uid": "39",
     *          "email": "teste@teste.com.br9",
     *          "nome": "José da Silva",
     *          "cpf": "1234562890117",
     *          "nascimento": "2000-01-01",
     *          "sexo": "M",
     *          "dh_atualizacao": "2023-04-14 20:34:48",
     *          "dh_criacao": "2023-04-14 20:34:48",
     *          "avatar": null,
     *          "apelido": null,
     *          "status": "A",
     *          "role": "user"
     *       }
     *     ]
     *
     */

    /**
     * @api {get} /usuarios/listar/ Listar Usuários
     * @apiName Listar
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA_USUARIOS
     * @apiUse ERR_GENERICOS
     * 
     */
    public function listar()
    {
        try {
            $usuarioModel = new UsuarioModel();
            $arrUsuarios = (array) $usuarioModel->buscarTodosUsuarios();
            $responseData = json_encode($arrUsuarios);
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /usuarios/buscar/:id Buscar Usuário
     * @apiName Buscar
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Id do Usuário
     * 
     * @apiUse SAIDA_PADRAO_USUARIO
     * @apiUse ERR_GENERICOS
     * 
     */

    /**
     * @api {get} /usuarios/meu-perfil/ Meu Perfil
     * @apiName Meu Perfil
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_PADRAO_USUARIO
     * @apiUse ERR_GENERICOS
     * 
     */
    public function buscar($id = 0, $completo = false, $uid = 0)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                $arrUsuarios = (array) $usuarioModel->buscarUsuario($id, $completo, $uid);
                $responseData = json_encode($arrUsuarios);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {delete} /usuarios/deletar/:id Deletar Usuário
     * @apiName Deletar
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id ID único do Usuário.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9100",
     *         "mensagem": "Usuário não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function deletar($id = 0)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                if ($usuarioModel->deletarUsuario($id) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_DELETADO_SUCESSO'));
    }

    /**
     * @api {post} /usuarios/adicionar/ Adicionar Usuário
     * @apiName Adicionar
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiBody {String} email E-mail
     * @apiBody {String} nome Nome
     * @apiBody {String} senha Senha
     * @apiBody {String} cpf CPF
     * @apiBody {Date} nascimento Data de Nascimento
     * @apiBody {String} sexo Sexo
     * @apiBody {String} apelido Apelido
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9103",
     *         "mensagem": "E-mail já cadastrado",
     *         "detalhe": ""
     *     }
     */
    public function adicionar($dados)
    {
        try {
            $usuarioModel = new UsuarioModel();
            $usuarioId = $usuarioModel->adicionarUsuario($dados);
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_CADASTRO_SUCESSO', ['usuarioId' => $usuarioId]));

    }

    /**
     * @api {put} /usuarios/atualizar/:id Atualizar Usuário
     * @apiName Atualizar
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Id do Usuário
     * @apiBody {String} nome Nome
     * @apiBody {String} senha Senha
     * @apiBody {Date} nascimento Data de Nascimento
     * @apiBody {String} sexo Sexo
     * @apiBody {String} apelido Apelido
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9103",
     *         "mensagem": "E-mail já cadastrado",
     *         "detalhe": ""
     *     }
     */

    /**
     * @api {put} /usuarios/meu-perfil/ Atualizar Meu Perfil
     * @apiName Atualizar Meu Perfil
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiBody {String} nome Nome
     * @apiBody {String} senha Senha
     * @apiBody {Date} nascimento Data de Nascimento
     * @apiBody {String} sexo Sexo
     * @apiBody {String} apelido Apelido
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9103",
     *         "mensagem": "E-mail já cadastrado",
     *         "detalhe": ""
     *     }
     */
    public function atualizar($id, $dados)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                $usuarioModel->atualizarUsuario($id, $dados);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_ATUALIZADO_SUCESSO'));
    }

    /**
     * @api {post} /usuários/vincularAssunto/ Vincular Assunto
     * @apiName Vincular Assunto
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiBody {Object[]} usuarioassunto UsuarioAssunto.
     * @apiBody {Number} usuarioassunto.uid Id do Usuário.
     * @apiBody {Number} usuarioassunto.iid Id do Assunto.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9600",
     *         "mensagem": "Assunto não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function vincularAssunto($uid, $dados)
    {
        try {
            $usuarioAssuntoModel = new UsuarioAssuntoModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    $usuarioAssuntoModel->adicionarUsuarioAssunto($uid, $dado);
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (CLException | CLConstException $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_ASSUNTO_VINCULADO'));
    }

    /**
     * @api {post} /usuários/desvincularAssunto/ Desvincular Assunto
     * @apiName Vincular Assunto
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiBody {Object[]} usuarioassunto UsuarioAssunto.
     * @apiBody {Number} usuarioassunto.uid Id do Usuário.
     * @apiBody {Number} usuarioassunto.iid Id do Assunto.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9600",
     *         "mensagem": "Assunto não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function desvincularAssunto($uid, $dados)
    {
        try {
            $usuarioAssuntoModel = new UsuarioAssuntoModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    if ($usuarioAssuntoModel->deletarUsuarioAssunto($uid, $dado) == 0) {
                        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_ASSUNTO_VINCULO_NAO_ENCONTRADO', StringHelper::formataArrayChaveValor($dado)));
                    }
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_ASSUNTO_DESVINCULADO'));
    }

    /**
     * @api {post} /usuários/vincularLivro/ Vincular Livro
     * @apiName Vincular Livro
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiBody {Object[]} usuariolivro UsuarioLivro.
     * @apiBody {Number} usuariolivro.uid Id do Usuário.
     * @apiBody {Number} usuariolivro.iid Id do Livro.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9600",
     *         "mensagem": "Assunto não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function vincularLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    $usuarioLivroModel->adicionarUsuarioLivro($uid, $dado);
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_LIVRO_VINCULADO'));
    }

    /**
     * @api {post} /usuários/desvincularLivro/ Desvincular Livro
     * @apiName Desvincular Livro
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiBody {Object[]} usuariolivro UsuarioLivro.
     * @apiBody {Number} usuariolivro.uid Id do Usuário.
     * @apiBody {Number} usuariolivro.iid Id do Livro.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9600",
     *         "mensagem": "Assunto não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function desvincularLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    if ($usuarioLivroModel->deletarUsuarioLivro($uid, $dado) == 0) {
                        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_LIVRO_VINCULO_NAO_ENCONTRADO', $dado));
                    }
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_LIVRO_DESVINCULADO'));
    }

    /**
     * @api {post} /usuários/bloquearUsuario/:id Bloquear Usuário
     * @apiName Bloquear Usuário
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Id do Usuário
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9600",
     *         "mensagem": "Assunto não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function bloquearUsuario($uid = 0, $uid_blq = 0)
    {
        try {
            if (is_numeric($uid) && is_numeric($uid_blq)) {
                $usuarioModel = new UsuarioModel();
                $usuarioModel->bloquearUsuario($uid, $uid_blq);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_BLOQUEIO_SUCESSO'));
    }

    /**
     * @api {delete} /usuarios/desbloquearUsuario/:id Desbloquear Usuário
     * @apiName Desbloquear Usuário
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id ID único do Usuário.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9100",
     *         "mensagem": "Usuário não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function desbloquearUsuario($uid = 0, $uid_blq = 0)
    {
        try {
            if (is_numeric($uid) && is_numeric($uid_blq)) {
                $usuarioModel = new UsuarioModel();
                if ($usuarioModel->debloquearUsuario($uid, $uid_blq) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_DESBLOQUEIO_SUCESSO'));
    }

    /**
     * @api {post} /usuários/adicionarAmigo/:id Adicionar Amigo
     * @apiName Adicionar Amigo
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Id do Usuário
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9600",
     *         "mensagem": "Assunto não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function adicionarAmigo($uid = 0, $uid_amigo = 0)
    {
        try {
            if (is_numeric($uid) && is_numeric($uid_amigo)) {
                $amigoModel = new AmigoModel();
                $amigoModel->adicionarAmigo($uid, $uid_amigo);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AMIGO_ADICIONADO_SUCESSO'));
    }

    /**
     * @api {delete} /usuarios/removerAmigo/:id Remover Amigo
     * @apiName Remover Amigo
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id ID único do Usuário.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9100",
     *         "mensagem": "Usuário não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function removerAmigo($uid = 0, $uid_amigo = 0)
    {
        try {
            if (is_numeric($uid) && is_numeric($uid_amigo)) {
                $amigoModel = new AmigoModel();
                if ($amigoModel->removerAmigo($uid, $uid_amigo) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AMIGO_REMOVIDO_SUCESSO'));
    }

    /**
     * @api {get} /usuarios/amigos/ Listar Amigos
     * @apiName Listar Amigos
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     *
     * @apiSuccess {Number} uid ID do usuário
     * @apiSuccess {Number} uid_amigo ID do amigo
     * @apiSuccess {Timestamp} dh_criacao  Data/Hora de criação
     * @apiSuccess {String} apelido Apelido
     * @apiSuccess {String="A","D"} status Status
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *       {
     *          "uid": "39",
     *          "uid_amigo": "44",
     *          "dh_criacao": "2023-04-14 20:34:48",
     *          "nome": "José da Silva",
     *          "apelido": "Zé",
     *          "status": "A"
     *       }
     *
     * @apiUse ERR_GENERICOS
     * 
     */
    public function listarAmigos($uid = 0)
    {
        try {
            if (is_numeric($uid)) {
                $amigoModel = new AmigoModel();
                $arrUsuarios = (array) $amigoModel->listarAmigos($uid);
                $responseData = json_encode($arrUsuarios);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {post} /usuarios/enviarFoto/ Enviar Foto
     * @apiName Enviar Foto
     * @apiGroup Usuários
     * @apiVersion 1.0.0
     *
     * @apiParam {Image} imagem Stream de imagem
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9100",
     *         "mensagem": "Usuário não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function enviarFoto($uid = 0, $files = 0)
    {
        if (FileHelper::validaImagem($files)) {
            $imagemModel = new ImagemModel();
            try {
                $imagemModel->salvaFotoUsuario($uid, $files);
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('FOTO_USUARIO_SALVA_SUCESSO'));
            } catch (Exception $e) {
                $this->httpRawResponse(200, MessageHelper::fmtException($e));
            }

        } else {
            $this->httpRawResponse(415, MessageHelper::fmtMsgConstJson('TIPO_IMAGEM_NAO_SUPORTADO'));
        }
    }

    public function removerFoto($uid)
    {
        $imagemModel = new ImagemModel();
        try {
            $imagemModel->removeFotoUsuario($uid);
            $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('FOTO_USUARIO_REMOVIDA_SUCESSO'));
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
    }
}