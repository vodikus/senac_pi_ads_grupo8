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
                            $this->buscarEmprestimo($this->getFieldFromToken('uid'), $params['level1']);
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
                            $this->desistirEmprestimo($this->getFieldFromToken('uid'), $params['level1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'retirar':
                        if ($this->isAuth()) {
                            $this->retirarEmprestimo($this->getFieldFromToken('uid'), $params['level1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'devolver':
                        if ($this->isAuth()) {
                            $this->devolverEmprestimo($this->getFieldFromToken('uid'), $params['level1']);
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
     * @apiDefine ERR_EMPRESTIMO_PADRAO
     *
     * @apiError (Erro 4xx) 9301 Empréstimo não localizado.
     *
     */

    /**
     * @apiDefine SAIDA_LISTA_EMPRESTIMOS
     *
     * @apiSuccess {Object[]} emprestimos Lista de emprestimos
     * @apiSuccess {Number} emprestimos.eid ID do empréstimo
     * @apiSuccess {Number} emprestimos.uid_dono ID do usuário emprestador do livro
     * @apiSuccess {Number} emprestimos.lid ID do livro
     * @apiSuccess {Number} emprestimos.uid_tomador ID do usuário tomador do empréstimo
     * @apiSuccess {Number} emprestimos.qtd_dias Quantidade de dias do empréstimo
     * @apiSuccess {Timestamp} emprestimos.retirada_prevista  Data/Hora da retirada prevista
     * @apiSuccess {Timestamp} emprestimos.devolucao_prevista  Data/Hora da devolução prevista
     * @apiSuccess {Timestamp} emprestimos.retirada_efetiva  Data/Hora da retirada efetiva
     * @apiSuccess {Timestamp} emprestimos.devolucao_efetiva  Data/Hora da devolução efetiva
     * @apiSuccess {String} emprestimos.status Nome do assunto
     * @apiSuccess {Timestamp} emprestimos.dh_solicitacao  Data/Hora de solicitação
     * @apiSuccess {Timestamp} emprestimos.dh_atualizacao  Data/Hora de atualização
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "eid": "13",
     *             "uid_dono": "39",
     *             "lid": "6",
     *             "uid_tomador": "44",
     *             "qtd_dias": "15",
     *             "retirada_prevista": "2023-04-19 12:31:00",
     *             "devolucao_prevista": "2023-05-02 12:31:00",
     *             "retirada_efetiva": "2023-04-23 14:45:48",
     *             "devolucao_efetiva": "2023-04-23 14:57:51",
     *             "status": "DEVO",
     *             "dh_solicitacao": "2023-04-23 14:45:04",
     *             "dh_atualizacao": "2023-04-23 14:57:51"
     *         }
     *     ]
     *
     */

    /**
     * @api {get} /emprestimos/buscar/:id Buscar Empréstimo
     * @apiName Buscar
     * @apiGroup Empréstimos
     * @apiVersion 1.0.0
     *
     * @apiParam {String} id Id do empréstimo
     * 
     * @apiSuccess {Number} eid ID do empréstimo
     * @apiSuccess {Number} uid_dono ID do usuário emprestador do livro
     * @apiSuccess {Number} lid ID do livro
     * @apiSuccess {Number} uid_tomador ID do usuário tomador do empréstimo
     * @apiSuccess {Number} qtd_dias Quantidade de dias do empréstimo
     * @apiSuccess {Timestamp} retirada_prevista  Data/Hora da retirada prevista
     * @apiSuccess {Timestamp} devolucao_prevista  Data/Hora da devolução prevista
     * @apiSuccess {Timestamp} retirada_efetiva  Data/Hora da retirada efetiva
     * @apiSuccess {Timestamp} devolucao_efetiva  Data/Hora da devolução efetiva
     * @apiSuccess {String="SOLI","DEVO","CANC","EMPR", "EXTR"} status Status do empréstimo
     * @apiSuccess {Timestamp} dh_solicitacao  Data/Hora de solicitação
     * @apiSuccess {Timestamp} dh_atualizacao  Data/Hora de atualização
     * 
     * @apiUse ERR_GENERICOS
     * 
     */
    public function buscarEmprestimo($uid = 0, $eid = 0)
    {
        try {
            if (is_numeric($eid)) {
                $emprestimoModel = new EmprestimoModel();
                $arrEmprestimo = $emprestimoModel->buscaEmprestimo($eid);
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

    /**
     * @api {get} /emprestimos/meus-emprestimos/ Listar Meus Empréstimos
     * @apiName Listar Meus Empréstimos
     * @apiGroup Empréstimos
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA_EMPRESTIMOS
     * @apiUse ERR_GENERICOS
     * 
     */

    /**
     * @api {get} /emprestimos/meus-emprestados/ Listar Meus Livros Emprestados
     * @apiName Listar Meus Emprestados
     * @apiGroup Empréstimos
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA_EMPRESTIMOS
     * @apiUse ERR_GENERICOS
     * 
     */
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

    /**
     * @api {post} /emprestimos/solicitar/ Solicitar Empréstimo
     * @apiName Solicitar
     * @apiGroup Empréstimos
     * @apiVersion 1.0.0
     *
     * @apiBody {Number} uid_dono Id do usuário
     * @apiBody {Number} lid Id do livro
     * @apiBody {Number} qtd_dias Quantidade de dias solicitado
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_EMPRESTIMO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9201",
     *         "mensagem": "Livro não disponivel",
     *         "detalhe": ""
     *     }
     */
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

    /**
     * @api {put} /emprestimos/devolver/:id Devolver Empréstimo
     * @apiName Devolver
     * @apiGroup Empréstimos
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Id do empréstimo
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_EMPRESTIMO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9201",
     *         "mensagem": "Livro não disponivel",
     *         "detalhe": ""
     *     }
     */
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

    /**
     * @api {put} /emprestimos/desistir/:id Desistir de Empréstimo
     * @apiName Desistir
     * @apiGroup Empréstimos
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Id do empréstimo
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_EMPRESTIMO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9201",
     *         "mensagem": "Livro não disponivel",
     *         "detalhe": ""
     *     }
     */
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

    /**
     * @api {post} /emprestimos/previsao/ Informar previsão de Empréstimo
     * @apiName Previsão
     * @apiGroup Empréstimos
     * @apiVersion 1.0.0
     *
     * @apiBody {Number} eid Id do empréstimo
     * @apiBody {Timestamp} retirada_prevista Data prevista de retirada do empréstimo
     * @apiBody {Timestamp} devolucao_prevista Data prevista de devolução do empréstimo
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_EMPRESTIMO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9201",
     *         "mensagem": "Livro não disponivel",
     *         "detalhe": ""
     *     }
     */
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

    /**
     * @api {put} /emprestimos/retirar/:id Retirar Empréstimo
     * @apiName Retirar
     * @apiGroup Empréstimos
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Id do empréstimo
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_EMPRESTIMO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9201",
     *         "mensagem": "Livro não disponivel",
     *         "detalhe": ""
     *     }
     */       
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