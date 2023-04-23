<?php
include_once 'helpers/MessageHelper.php';
include_once 'includes/BaseController.php';
include_once 'includes/Constantes.php';
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
                            // $this->listarEmprestimos($this->getFieldFromToken('uid'));
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'buscar':
                        if ($this->isAuth()) {
                            // $this->buscarEmprestimo($this->getFieldFromToken('uid'), $params['param1'], $params['param2']);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501, 'Ação Indisponível');
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
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'previsao':
                        if ($this->isAuth()) {
                            $this->previsaoEmprestimo($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501, 'Ação Indisponível');
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
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'retirar':
                        if ($this->isAuth()) {
                            $this->retirarEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'devolver':
                        if ($this->isAuth()) {
                            $this->devolverEmprestimo($this->getFieldFromToken('uid'), $params['param1']);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501, 'Ação Indisponível');
                        break;
                }
                break;
            default:
                $this->httpResponse(405, 'Method Not Allowed');
                break;
        }
    }

    public function solicitarEmprestimo($uid = 0, $dados)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            if (!$emprestimoModel->solicitarEmprestimo($uid, $dados)) {
                $this->httpResponse(200, 'Livro ou usuário não encontrado');
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Empréstimo solicitado com sucesso.');

    }

    public function devolverEmprestimo($uid = 0, $eid = 0)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            if (!$emprestimoModel->devolverEmprestimo($uid, $eid)) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst(Constantes::getConst('ERR_EMPRESTIMO_NAO_DEVOLVIDO')));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Empréstimo devolvido com sucesso.');
    }

    public function desistirEmprestimo($uid = 0, $eid = 0)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            if (!$emprestimoModel->desistirEmprestimo($uid, $eid)) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst(Constantes::getConst('ERR_EMPRESTIMO_NAO_CANCELADO')));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Solicitação de Empréstimo cancelada com sucesso.');
    }

    public function previsaoEmprestimo($uid = 0, $dados)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            if (!$emprestimoModel->previsaoEmprestimo($uid, $dados)) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst(Constantes::getConst('ERR_EMPRESTIMO_NAO_PREVISAO')));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Previsão de Empréstimo registrada com sucesso.');
    }

    public function retirarEmprestimo($uid = 0, $eid = 0)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            if (!$emprestimoModel->retirarEmprestimo($uid, $eid)) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst(Constantes::getConst('ERR_EMPRESTIMO_NAO_RETIRADO')));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Retirada de Empréstimo registrada com sucesso.');
    }

// @TODO Validar estados antes de realizar os updates / inserts devido a retirada da chave primária
}