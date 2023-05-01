<?php
use helpers\MessageHelper;

include_once 'models/EnderecoModel.php';

class EnderecoController extends BaseController
{
    public function __construct() {
        parent::__construct();
    }
    public function processarRequisicao($metodo='', $params=[]) {
        switch ($metodo) {
            case 'GET':
                switch ($params['acao']) {
                    case 'listar':
                        if ( $this->isAuth() ) {
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
                            if ( $this->isAuth() ) {
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
                        if ( $this->isAuth() ) {
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
                        if ( $this->isAuth() ) {
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

    public function adicionar($uid, $dados)
    {       
        try {
            $enderecoModel = new EnderecoModel();
            $enderecoId = $enderecoModel->adicionarEndereco($uid, $dados);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_ENDERECO_CADASTRO_SUCESSO',['enderecoId' => $enderecoId]));
    }

    public function deletar($eid, $uid)
    {
        try {
            if ( is_numeric($eid) && is_numeric($uid) ) {
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

    public function atualizar($eid, $uid, $dados)
    {       
        try {
            if ( is_numeric($eid) && is_numeric($uid) ) {
                $enderecoModel = new EnderecoModel();            
                if ( $enderecoModel->atualizarEndereco($eid, $uid, $dados) == 0) {
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