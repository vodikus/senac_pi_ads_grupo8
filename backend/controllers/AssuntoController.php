<?php
include_once 'includes/BaseController.php';
include_once 'helpers/Constantes.php';
include_once 'helpers/MessageHelper.php';
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
                        $this->buscar($params['param1']);
                        break;
                    default:
                        $this->httpResponse(501, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ACAO_INDISPONIVEL')));
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
                            $this->httpResponse(401, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_NAO_AUTORIZADO')));
                        }
                        break;
                    default:
                        $this->httpResponse(501, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ACAO_INDISPONIVEL')));
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
                            $this->httpResponse(401, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_NAO_AUTORIZADO')));
                        }
                        break;
                    default:
                        $this->httpResponse(501, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ACAO_INDISPONIVEL')));
                        break;
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ($this->isAuth()) {
                            $this->deletar($params['param1']);
                        } else {
                            $this->httpResponse(401, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_NAO_AUTORIZADO')));
                        }
                        break;
                    default:
                        $this->httpResponse(501, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ACAO_INDISPONIVEL')));
                        break;
                }
                break;
            default:
                $this->httpResponse(405, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_METODO_NAO_PERMITIDO')));
                break;
        }
    }

    public function listar($uid = 0)
    {
        try {
            $assuntoModel = new AssuntoModel();
            $arrAssuntos = $assuntoModel->listarAssuntos();
            $responseData = json_encode($arrAssuntos);
        } catch (Exception $e) {
            $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }
    public function buscar($iid = 0)
    {
        try {
            if (is_numeric($iid)) {
                $assuntoModel = new AssuntoModel();
                $arrAssunto = $assuntoModel->buscarAssunto($iid);
                if (count($arrAssunto) > 0) {
                    $responseData = json_encode($arrAssunto);
                } else {
                    throw new Exception(helpers\Constantes::getMsg('ERR_ASSUNTO_NAO_ENCONTRADO'), helpers\Constantes::getCode('ERR_ASSUNTO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpResponse(200, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ID_INVALIDO')));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function adicionar($dados)
    {
        try {
            $assuntoModel = new AssuntoModel();
            $assuntoId = $assuntoModel->adicionarAssunto($dados);
        } catch (Exception $e) {
            $this->httpResponse(500, Helpers\MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('MSG_ASSUNTO_CADASTRO_SUCESSO'),false), ['assuntoId' => $assuntoId]);
    }

    public function deletar($aid)
    {
        try {
            if (is_numeric($aid)) {
                $assuntoModel = new AssuntoModel();
                if ($assuntoModel->deletarAssunto($aid) == 0) {
                    $this->httpResponse(200, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ASSUNTO_NAO_ENCONTRADO')));
                }
            } else {
                $this->httpResponse(200, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ID_INVALIDO')));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, Helpers\MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('MSG_ASSUNTO_DELETADO_SUCESSO'),false));
    }

    public function atualizar($aid, $dados)
    {
        try {
            if (is_numeric($aid)) {
                $assuntoModel = new AssuntoModel();
                if ($assuntoModel->atualizarAssunto($aid, $dados) == 0) {
                    $this->httpResponse(200, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ASSUNTO_NAO_ENCONTRADO')));
                }
            } else {
                $this->httpResponse(200, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ID_INVALIDO')));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, Helpers\MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('MSG_ASSUNTO_ATUALIZADO_SUCESSO'),false));
    }
}