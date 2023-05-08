<?php
use helpers\MessageHelper;

include_once 'models/ChatModel.php';

class ChatController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function processarRequisicao($metodo = '', $params = [])
    {
        switch ($metodo) {
            case 'GET':
                $dados = $this->pegarArrayJson();
                switch ($params['acao']) {
                    case 'listar':
                        if ($this->isAuth()) {
                            $this->listarMensagens($this->getFieldFromToken('uid'), $params['level1']);
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
                    case 'enviar':
                        if ($this->isAuth()) {
                            $dados['uid'] = $this->getFieldFromToken('uid');
                            $this->enviarMensagem($dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
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
     * @apiDefine SAIDA_LISTA_CHAT
     *
     * @apiSuccess {Object[]} mensagens Lista de mensagens
     * @apiSuccess {Number} mensagens.uid Id do usuário
     * @apiSuccess {String} mensagens.apelido Apelido do usuário
     * @apiSuccess {Number} mensagens.uid_amigo Id do amigo
     * @apiSuccess {String} mensagens.apelido_amigo Apelido do amigo
     * @apiSuccess {String} mensagens.mensagem Mensagem
     * @apiSuccess {Timestamp} mensagens.dh_criacao  Data/Hora de envio da mensagem
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "uid": "44",
     *             "apelido": "José",
     *             "uid_amigo": "39",
     *             "apelido_amigo": "João",
     *             "mensagem": "Olá!",
     *             "dh_criacao": "2023-05-06 10:30:46",
     *         }
     *     ]
     *
     */

    /**
     * @api {post} /chat/enviar/ Enviar Mensagem
     * @apiName Enviar Mensagem
     * @apiGroup Chat
     * @apiVersion 1.0.0
     *
     * @apiBody {String} uid_amigo Id do Usuário amigo.
     * @apiBody {String} mensagem Texto da mensagem.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9800",
     *         "mensagem": "Falha no envio da mensagem",
     *         "detalhe": ""
     *     }
     */
    public function enviarMensagem($dados)
    {
        try {
            $chatModel = new ChatModel();
            $msgId = $chatModel->adicionarMensagem($dados);
            if ($msgId <= 0) {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_CHAT_ENVIO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_CHAT_ENVIO_SUCESSO', ['msgId' => $msgId]));

    }

    /**
    * @api {get} /chat/listar/:uid Listar Mensagens do Usuário
    * @apiName Listar Mensagens
    * @apiGroup Chat
    * @apiVersion 1.0.0
    *
    * @apiParam {String} uid Id do usuário.

    * @apiUse SAIDA_LISTA_CHAT
    * @apiUse ERR_GENERICOS
    * 
    */
    public function listarMensagens($uid, $uid_amigo)
    {
        try {
            $chatModel = new ChatModel();
            $arrMensagens = [];
            $arrMensagens = (array) $chatModel->listarMensagens($uid, $uid_amigo);
            $responseData = json_encode($arrMensagens);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

}