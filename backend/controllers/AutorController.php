<?php
use helpers\MessageHelper;

include_once 'models/AutorModel.php';

class AutorController extends BaseController
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

    public function listar($uid = 0)
    {
        try {
            $autorModel = new AutorModel();
            $arrAutores = $autorModel->listarAutores();
            $responseData = json_encode($arrAutores);
        } catch (Exception $e) {
            $this->httpRawResponse(500, $e->getMessage());
        }
        $this->montarSaidaOk($responseData);
    }

    public function adicionar($dados)
    {
        try {
            $autorModel = new AutorModel();
            $autorId = $autorModel->adicionarAutor($dados);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AUTOR_CADASTRO_SUCESSO', ['autorId' => $autorId]));
    }

    public function deletar($aid)
    {
        try {
            if (is_numeric($aid)) {
                $autorModel = new AutorModel();
                if ($autorModel->deletarAutor($aid) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_AUTOR_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AUTOR_DELETADO_SUCESSO'));
    }

    public function atualizar($aid, $dados)
    {
        try {
            if (is_numeric($aid)) {
                $autorModel = new AutorModel();
                if ($autorModel->atualizarAutor($aid, $dados) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_AUTOR_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AUTOR_ATUALIZADO_SUCESSO'));
    }
}