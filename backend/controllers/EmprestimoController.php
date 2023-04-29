<?php
use helpers\MessageHelper;

include_once 'models/EmprestimoModel.php';

class EmprestimoController extends BaseController
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
                    case 'meus-emprestimos':
                        if ($this->isAuth()) {
                            $this->listarEmprestimos($this->getFieldFromToken('uid'), "TOMADOS");
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                    case 'meus-emprestados':
                        if ($this->isAuth()) {
                            $this->listarEmprestimos($this->getFieldFromToken('uid'), "EMPRESTADOS");
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'buscar':
                        if ($this->isAuth()) {
                            $this->buscarEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'POST':
                $dados = $this->pegarArrayPost();
                switch ($params['acao']) {
                    case 'solicitar':
                        if ($this->isAuth()) {
                            $this->solicitarEmprestimo($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'previsao':
                        if ($this->isAuth()) {
                            $this->previsaoEmprestimo($this->getFieldFromToken('uid'), $dados);
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
                $dados = $this->pegarArrayPut();
                switch ($params['acao']) {
                    case 'desistir':
                        if ($this->isAuth()) {
                            $this->desistirEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'retirar':
                        if ($this->isAuth()) {
                            $this->retirarEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'devolver':
                        if ($this->isAuth()) {
                            $this->devolverEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
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

    public function buscarEmprestimo($uid = 0, $eid = 0)
    {
        try {
            if (is_numeric($eid)) {
                $emprestimoModel = new EmprestimoModel();
                $arrEmprestimo = $emprestimoModel->buscaEmprestimo($uid, $eid);
                if (count($arrEmprestimo) > 0) {
                    $responseData = json_encode($arrEmprestimo);
                } else {
                    throw new CLException('ERR_EMPRESTIMO_NAO_LOCALIZADO');
                }
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);

    }

    public function listarEmprestimos($uid = 0, $tipo)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            $arrEmprestimo = $emprestimoModel->listarEmprestimos($uid, $tipo);
            if (count($arrEmprestimo) > 0) {
                $responseData = json_encode($arrEmprestimo);
            } else {
                throw new CLException('ERR_EMPRESTIMO_NAO_LOCALIZADO');
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);

    }

    public function solicitarEmprestimo($uid = 0, $dados)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            $emprestimoId = $emprestimoModel->solicitarEmprestimo($uid, $dados);

        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_EMPRESTIMO_SOLICITADO_SUCESSO', false), ['emprestimoId' => $emprestimoId]);
    }

    public function devolverEmprestimo($uid = 0, $eid = 0)
    {
        try {
            if (is_numeric($eid)) {
                $emprestimoModel = new EmprestimoModel();
                if (!$emprestimoModel->devolverEmprestimo($uid, $eid)) {
                    $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_EMPRESTIMO_NAO_DEVOLVIDO'));
                }
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_EMPRESTIMO_DEVOLVIDO_SUCESSO', false));
    }

    public function desistirEmprestimo($uid = 0, $eid = 0)
    {
        try {
            if (is_numeric($eid)) {
                $emprestimoModel = new EmprestimoModel();
                if (!$emprestimoModel->desistirEmprestimo($uid, $eid)) {
                    $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_EMPRESTIMO_NAO_CANCELADO'));
                }
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_EMPRESTIMO_CANCELADO_SUCESSO', false));
    }

    public function previsaoEmprestimo($uid = 0, $dados)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            if (!$emprestimoModel->previsaoEmprestimo($uid, $dados)) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_EMPRESTIMO_NAO_PREVISAO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_EMPRESTIMO_PREVISAO_SUCESSO', false));
    }

    public function retirarEmprestimo($uid = 0, $eid = 0)
    {
        try {
            if (is_numeric($eid)) {
                $emprestimoModel = new EmprestimoModel();
                if (!$emprestimoModel->retirarEmprestimo($uid, $eid)) {
                    $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_EMPRESTIMO_NAO_RETIRADO'));
                }
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_EMPRESTIMO_RETIRADA_SUCESSO', false));
    }


}