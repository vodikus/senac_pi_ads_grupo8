<?php
use helpers\MessageHelper;

include_once 'models/AssuntoModel.php';

class AssuntoController extends BaseController
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
                        $this->listar();
                        break;
                    case 'buscar':
                        $this->buscarPorId($params['param1']);
                        break;
                    case 'buscar-por-nome':
                        $dados = $this->pegarArrayJson();
                        $this->buscarPorNome($dados);
                        break;
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'POST':
                $dados = $this->pegarArrayJson();
                switch ($params['acao']) {
                    case 'buscar':
                        $this->buscarPorNome($dados);
                        break;
                    case 'adicionar':
                        if ($this->isAuth()) {
                            $this->adicionar($dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'PUT':
                switch ($params['acao']) {
                    case 'atualizar':
                        $dados = $this->pegarArrayJson();
                        if ($this->isAuth()) {
                            $this->atualizar($params['param1'], $dados);
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
                            $this->deletar($params['param1']);
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
     * @apiDefine ERR_ASSUNTO_PADRAO
     *
     * @apiError (Erro 4xx) 9600 Assunto não encontrado.
     *
     */

    /**
     * @apiDefine SAIDA_LISTA
     *
     * @apiSuccess {Object[]} assuntos Lista de assuntos
     * @apiSuccess {Number} assuntos.iid ID do assunto
     * @apiSuccess {String} assuntos.nome_assunto Nome do assunto
     * @apiSuccess {Timestamp} assuntos.dh_atualizacao  Data/Hora de atualização
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "iid": "1",
     *         "nome_assunto": "Fantasia",
     *         "dh_atualizacao": "2023-04-15 20:45:26"
     *       }
     *     ]
     *
     */

     /**
     * @apiDefine SAIDA_PADRAO
     *
     * @apiSuccess {Number} codigo Código da mensagem
     * @apiSuccess {String} mensagem Mensagem de retorno
     * @apiSuccess {Object} detalhe Objeto contendo detalhes do retorno
     * @apiSuccess {Number} detalhe.assuntoId  Id do assunto inserido
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

    /**
     * @api {get} /assuntos/listar/ Lista os assuntos
     * @apiName Listar
     * @apiGroup Assuntos
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA
     * @apiUse ERR_GENERICOS
     * 
     */
    public function listar($uid = 0)
    {
        try {
            $assuntoModel = new AssuntoModel();
            $arrAssuntos = (array) $assuntoModel->listarAssuntos();
            $responseData = json_encode($arrAssuntos);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /assuntos/buscar-por-id/:id Busca assuntos pelo Id
     * @apiName Buscar por ID
     * @apiGroup Assuntos
     * @apiVersion 1.0.0
     * 
     * @apiParam {Number} id ID único do assunto.
     *
     * @apiSuccess {Number} iid ID do assunto
     * @apiSuccess {String} nome_assunto Nome do assunto
     * @apiSuccess {Timestamp} dh_atualizacao  Data/Hora de atualização
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "iid": "1",
     *       "nome_assunto": "Fantasia",
     *       "dh_atualizacao": "2023-04-15 20:45:26"
     *     }
     *
     * 
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_ASSUNTO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *         "codigo": 9600,
     *         "mensagem": "Assunto não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function buscarPorId($iid = 0)
    {
        try {
            if (is_numeric($iid)) {
                $assuntoModel = new AssuntoModel();
                $arrAssunto = (array) $assuntoModel->buscarAssuntoPorId($iid);
                if (count($arrAssunto) == 0) {
                    $this->httpRawResponse(404, MessageHelper::fmtMsgConstJson('ERR_ASSUNTO_NAO_ENCONTRADO'));
                }
                $responseData = json_encode($arrAssunto);
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /assuntos/buscar-por-nome/ Busca assuntos pelo nome
     * @apiName Buscar por nome
     * @apiGroup Assuntos
     * @apiVersion 1.0.0
     *
     * @apiBody {String} nome_assunto Nome do assunto.
     *
     * @apiUse SAIDA_LISTA
     * @apiUse ERR_GENERICOS
     * 
     */
    public function buscarPorNome($dados)
    {
        try {
            $assuntoModel = new AssuntoModel();
            $arrAssunto = (array) $assuntoModel->buscarAssuntoPorNome($dados);
            $responseData = json_encode($arrAssunto);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {post} /assuntos/adicionar/ Adiciona assunto
     * @apiName Adicionar
     * @apiGroup Assuntos
     * @apiVersion 1.0.0
     *
     * @apiBody {String} nome_assunto Nome do assunto.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_ASSUNTO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9601",
     *         "mensagem": "Já existe um assunto com este nome",
     *         "detalhe": ""
     *     }
     */
    public function adicionar($dados)
    {
        try {
            $assuntoModel = new AssuntoModel();
            $assuntoId = $assuntoModel->adicionarAssunto($dados);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_ASSUNTO_CADASTRO_SUCESSO', ['assuntoId' => $assuntoId]));
    }

    /**
     * @api {delete} /assuntos/deletar/:id Deleta assunto pelo Id
     * @apiName Deletar por ID
     * @apiGroup Assuntos
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id ID único do assunto.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_ASSUNTO_PADRAO
     * @apiError (Erro 5xx) 9602 Um assunto não pode ser deletado devido estar vinculado a algum livro.
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9602",
     *         "mensagem": "Este assunto não pode ser deletado pois está vinculado a um ou mais livros",
     *         "detalhe": ""
     *     }
     */
    public function deletar($aid)
    {
        try {
            if (is_numeric($aid)) {
                $assuntoModel = new AssuntoModel();
                if ($assuntoModel->deletarAssunto($aid) == 0) {
                    $this->httpRawResponse(404, MessageHelper::fmtMsgConstJson('ERR_ASSUNTO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_ASSUNTO_DELETADO_SUCESSO'));
    }

    /**
     * @api {put} /assuntos/atualizar/:id Adiciona assunto
     * @apiName Adicionar
     * @apiGroup Assuntos
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id ID único do assunto.
     * 
     * @apiBody {String} nome_assunto Nome do assunto.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_ASSUNTO_PADRAO
     * @apiError (Erro 5xx) 9601 Um assunto com o mesmo nome já foi inserido.
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9601",
     *         "mensagem": "Já existe um assunto com este nome",
     *         "detalhe": ""
     *     }
     */
    public function atualizar($aid, $dados)
    {
        try {
            if (is_numeric($aid)) {
                $assuntoModel = new AssuntoModel();
                if ($assuntoModel->atualizarAssunto($aid, $dados) == 0) {
                    $this->httpRawResponse(404, MessageHelper::fmtMsgConstJson('ERR_ASSUNTO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_ASSUNTO_ATUALIZADO_SUCESSO'));
    }
}