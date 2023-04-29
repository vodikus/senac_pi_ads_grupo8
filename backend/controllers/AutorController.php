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
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'POST':
                $dados = $this->pegarArrayPost();
                switch ($params['acao']) {
                    case 'adicionar':
                        if ($this->isAuth()) {
                            $this->adicionar($dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'PUT':
                switch ($params['acao']) {
                    case 'atualizar':
                        $dados = $this->pegarArrayPut();
                        if ($this->isAuth()) {
                            $this->atualizar($params['param1'], $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ($this->isAuth()) {
                            $this->deletar($params['param1']);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            default:
                $this->httpResponse(405, MessageHelper::fmtMsgConst('ERR_METODO_NAO_PERMITIDO'));
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
            $this->httpResponse(500, $e->getMessage());
        }
        $this->montarSaidaOk($responseData);
    }

    public function adicionar($dados)
    {
        try {
            $autorModel = new AutorModel();
            $autorId = $autorModel->adicionarAutor($dados);
        } catch (Exception $e) {

        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_AUTOR_CADASTRO_SUCESSO'), ['autorId' => $autorId]);
    }

    public function deletar($aid)
    {
        try {
            if (is_numeric($aid)) {
                $autorModel = new AutorModel();
                if ($autorModel->deletarAutor($aid) == 0) {
                    $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_AUTOR_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_AUTOR_DELETADO_SUCESSO', false));
    }

    public function atualizar($aid, $dados)
    {
        try {
            if (is_numeric($aid)) {
                $autorModel = new AutorModel();
                if ($autorModel->atualizarAutor($aid, $dados) == 0) {
                    $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_AUTOR_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_AUTOR_ATUALIZADO_SUCESSO', false));
    }
}