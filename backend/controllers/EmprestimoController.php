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
                $dados = $this->pegarArrayJson();
                switch ($params['acao']) {
                    case 'meus-emprestimos':
                        if ($this->isAuth()) {
                            $this->listarEmprestimos($this->getFieldFromToken('uid'), "TOMADOS", $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                    case 'meus-emprestados':
                        if ($this->isAuth()) {
                            $this->listarEmprestimos($this->getFieldFromToken('uid'), "EMPRESTADOS", $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'buscar':
                        if ($this->isAuth()) {
                            $this->buscarEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
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
                    case 'solicitar':
                        if ($this->isAuth()) {
                            $this->solicitarEmprestimo($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'previsao':
                        if ($this->isAuth()) {
                            $this->previsaoEmprestimo($this->getFieldFromToken('uid'), $dados);
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
                    case 'desistir':
                        if ($this->isAuth()) {
                            $this->desistirEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'retirar':
                        if ($this->isAuth()) {
                            $this->retirarEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'devolver':
                        if ($this->isAuth()) {
                            $this->devolverEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
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

    public function buscarEmprestimo($uid = 0, $eid = 0)
    {
        try {
            if (is_numeric($eid)) {
                $emprestimoModel = new EmprestimoModel();
                $arrEmprestimo = $emprestimoModel->buscaEmprestimo($uid, $eid);
                if (count($arrEmprestimo) > 0) {
                    $responseData = json_encode($arrEmprestimo);
                } else {
                    $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_EMPRESTIMO_NAO_LOCALIZADO'));
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);

    }

    public function listarEmprestimos($uid = 0, $tipo, $filtro = [])
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            $status = (array_key_exists('status', $filtro)) ? $filtro['status'] : '';
            $arrEmprestimo = $emprestimoModel->listarEmprestimos($uid, $tipo, filter_var($status, FILTER_SANITIZE_STRING));
            if (count($arrEmprestimo) > 0) {
                $responseData = json_encode($arrEmprestimo);
            } else {
                $this->httpRawResponse(404, MessageHelper::fmtMsgConstJson('ERR_EMPRESTIMO_NAO_LOCALIZADO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);

    }

    public function solicitarEmprestimo($uid = 0, $dados)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            $emprestimoId = $emprestimoModel->solicitarEmprestimo($uid, $dados);

        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_EMPRESTIMO_SOLICITADO_SUCESSO', ['emprestimoId' => $emprestimoId]));
    }

    public function devolverEmprestimo($uid = 0, $eid = 0)
    {
        try {
            if (is_numeric($eid)) {
                $emprestimoModel = new EmprestimoModel();
                if (!$emprestimoModel->devolverEmprestimo($uid, $eid)) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_EMPRESTIMO_NAO_DEVOLVIDO'));
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_EMPRESTIMO_DEVOLVIDO_SUCESSO'));
    }

    public function desistirEmprestimo($uid = 0, $eid = 0)
    {
        try {
            if (is_numeric($eid)) {
                $emprestimoModel = new EmprestimoModel();
                if (!$emprestimoModel->desistirEmprestimo($uid, $eid)) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_EMPRESTIMO_NAO_CANCELADO'));
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_EMPRESTIMO_CANCELADO_SUCESSO'));
    }

    public function previsaoEmprestimo($uid = 0, $dados)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            if (!$emprestimoModel->previsaoEmprestimo($uid, $dados)) {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_EMPRESTIMO_NAO_PREVISAO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_EMPRESTIMO_PREVISAO_SUCESSO'));
    }

    public function retirarEmprestimo($uid = 0, $eid = 0)
    {
        try {
            if (is_numeric($eid)) {
                $emprestimoModel = new EmprestimoModel();
                if (!$emprestimoModel->retirarEmprestimo($uid, $eid)) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_EMPRESTIMO_NAO_RETIRADO'));
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_EMPRESTIMO_RETIRADA_SUCESSO'));
    }


}