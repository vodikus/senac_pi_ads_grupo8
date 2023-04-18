<?php
include_once 'includes/BaseController.php';
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
                    case 'devolver':
                        if ($this->isAuth()) {
                            $this->devolverEmprestimo($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'desistir':
                        if ($this->isAuth()) {
                            $this->desistirEmprestimo($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'previsao':
                        if ($this->isAuth()) {
                            // $this->adicionar($dados);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'retirar':
                        if ($this->isAuth()) {
                            // $this->adicionar($dados);
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
            if ($emprestimoModel->solicitarEmprestimo($uid, $dados) <= 0) {
                $this->httpResponse(200, 'Livro ou usuário não encontrado');
            }
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'PRIMARY')) {
                        $this->httpResponse(200, 'Um empréstimo para este livro já foi solicitado.');
                    } else {
                        $this->httpResponse(500, "Erro: " . $e->getCode() . " | " . $e->getMessage());
                    }
                    break;

                default:
                    $this->httpResponse(500, "Erro: " . $e->getCode() . " | " . $e->getMessage());
                    break;
            }
        } finally {
            $this->httpResponse(200, 'Empréstimo solicitado com sucesso.');
        }
    }

    public function devolverEmprestimo($uid = 0, $dados)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            if ($emprestimoModel->devolverEmprestimo($uid, $dados) <= 0) {
                $this->httpResponse(200, 'O empréstimo do livro não foi devolvido. Motivo: Livro / usuário não encontrado ou status do empréstimo inválido.');
            }
        } catch (Exception $e) {
            $this->httpResponse(500, "Erro: " . $e->getCode() . " | " . $e->getMessage());
        } finally {
            $this->httpResponse(200, 'Empréstimo devolvido com sucesso.');
        }
    }

    public function desistirEmprestimo($uid = 0, $dados)
    {
        try {
            $emprestimoModel = new EmprestimoModel();
            if ($emprestimoModel->desistirEmprestimo($uid, $dados) <= 0) {
                $this->httpResponse(200, 'O empréstimo do livro não foi cancelado. Motivo: Livro / usuário não encontrado ou status do empréstimo inválido.');
            }
        } catch (Exception $e) {
            $this->httpResponse(500, "Erro: " . $e->getCode() . " | " . $e->getMessage());
        } finally {
            $this->httpResponse(200, 'Solicitação de Empréstimo cancelada com sucesso.');
        }
    }
}