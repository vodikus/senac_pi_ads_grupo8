<?php
use helpers\MessageHelper;

include_once 'models/EnderecoModel.php';

class EnderecoController extends BaseController
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
                        if ($this->isAuth()) {
                            $this->listar($this->getFieldFromToken('uid'));
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
                        if ($this->isAuth()) {
                            $this->adicionar($this->getFieldFromToken('uid'), $dados);
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
                            $this->atualizar($params['param1'], $this->getFieldFromToken('uid'), $dados);
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
                            $this->deletar($params['param1'], $this->getFieldFromToken('uid'));
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
     * @apiDefine ERR_ENDERECO_PADRAO
     *
     * @apiError (Erro 4xx) 9700 Endereço não encontrado.
     *
     */

    /**
     * @apiDefine SAIDA_LISTA_ENDERECO
     *
     * @apiSuccess {Object[]} enderecos Lista de enderecos
     * @apiSuccess {Number} enderecos.enid ID do assunto
     * @apiSuccess {Number} enderecos.uid ID do assunto
     * @apiSuccess {String} enderecos.cep CEP
     * @apiSuccess {String} enderecos.logradouro Logradouro
     * @apiSuccess {String} enderecos.numero Numero
     * @apiSuccess {String} enderecos.complemento Complemento
     * @apiSuccess {String} enderecos.bairro Bairro
     * @apiSuccess {String} enderecos.cidade Cidade
     * @apiSuccess {String} enderecos.uf UF
     * @apiSuccess {Timestamp} enderecos.dh_atualizacao  Data/Hora de atualização
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "enid": "12",
     *             "uid": "44",
     *             "cep": "08673-040",
     *             "logradouro": "Rua José Garcia de Souza",
     *             "numero": "12",
     *             "complemento": null,
     *             "bairro": "Parque Suzano",
     *             "cidade": "Suzano",
     *             "uf": "SP",
     *             "dh_atualizacao": "2023-04-30 23:30:04"
     *         }
     *     ]
     *
     */

    /**
     * @api {get} /enderecos/listar/ Listar Endereços
     * @apiName Listar
     * @apiGroup Endereços
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA_ENDERECO
     * @apiUse ERR_GENERICOS
     * 
     */
    public function listar($uid = 0)
    {
        try {
            $enderecoModel = new EnderecoModel();
            $arrEnderecos = (array) $enderecoModel->buscarEnderecos($uid);
            $responseData = json_encode($arrEnderecos);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {post} /enderecos/adicionar/ Adicionar Endereço
     * @apiName Adicionar
     * @apiGroup Endereços
     * @apiVersion 1.0.0
     *
     * @apiBody {String} cep CEP
     * @apiBody {String} logradouro Logradouro
     * @apiBody {String} [numero] Numero
     * @apiBody {String} [complemento] Complemento
     * @apiBody {String} bairro Bairro
     * @apiBody {String} cidade Cidade
     * @apiBody {String} uf UF
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_ENDERECO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9601",
     *         "mensagem": "Já existe um endereço com este nome",
     *         "detalhe": ""
     *     }
     */
    public function adicionar($uid, $dados)
    {
        try {
            $enderecoModel = new EnderecoModel();
            $enderecoId = $enderecoModel->adicionarEndereco($uid, $dados);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_ENDERECO_CADASTRO_SUCESSO', ['enderecoId' => $enderecoId]));
    }

    /**
     * @api {delete} /enderecos/deletar/:id Deletar Endereço
     * @apiName Deletar por ID
     * @apiGroup Endereços
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id ID único do endereço.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_ENDERECO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9700",
     *         "mensagem": "Endereço não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function deletar($eid, $uid)
    {
        try {
            if (is_numeric($eid) && is_numeric($uid)) {
                $enderecoModel = new EnderecoModel();
                if ($enderecoModel->deletarEndereco($eid, $uid) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ENDERECO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_ENDERECO_DELETADO_SUCESSO'));
    }

    /**
     * @api {put} /enderecos/atualizar/:id Atualizar Endereço
     * @apiName Atualizar
     * @apiGroup Endereços
     * @apiVersion 1.0.0
     * 
     * @apiParam {Number} id ID único do endereço
     *
     * @apiBody {String} cep CEP
     * @apiBody {String} logradouro Logradouro
     * @apiBody {String} [numero] Numero
     * @apiBody {String} [complemento] Complemento
     * @apiBody {String} bairro Bairro
     * @apiBody {String} cidade Cidade
     * @apiBody {String} uf UF
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_ENDERECO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9601",
     *         "mensagem": "Já existe um endereço com este nome",
     *         "detalhe": ""
     *     }
     */
    public function atualizar($eid, $uid, $dados)
    {
        try {
            if (is_numeric($eid) && is_numeric($uid)) {
                $enderecoModel = new EnderecoModel();
                if ($enderecoModel->atualizarEndereco($eid, $uid, $dados) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ENDERECO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_ENDERECO_ATUALIZADO_SUCESSO'));
    }
}