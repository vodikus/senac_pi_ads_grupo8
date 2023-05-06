<?php
use helpers\MessageHelper;

include_once 'models/ChamadoModel.php';

class ChamadoController extends BaseController
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
                    case 'listar':
                        if ($this->isAuth()) {
                            if ($this->getFieldFromToken('roles') == 'admin') {
                                $this->listarChamados($dados, TRUE);
                            } else {
                                $dados['uid_origem'] = $this->getFieldFromToken('uid');
                                unset($dados['uid_destino']);
                                $this->listarChamados($dados, FALSE);
                            }
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
                            $this->adicionarChamado($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'adicionarDetalhe':
                        if ($this->isAuth()) {
                            $dados['uid'] = $this->getFieldFromToken('uid');
                            $this->adicionarChamadoDetalhe($dados);
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
                $dados = $this->pegarArrayJson();
                switch ($params['acao']) {
                    case 'alterarChamado':
                        if ($this->isAuth()) {
                            $this->alterarChamado($this->getFieldFromToken('uid'), $dados);
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
     * @apiDefine ERR_CHAMADO_PADRAO
     *
     * @apiError (Erro 4xx) 9500 Chamado não encontrado.
     *
     */

    /**
     * @apiDefine SAIDA_LISTA_CHAMADOS
     *
     * @apiSuccess {Object[]} chamados Lista de chamados
     * @apiSuccess {Number} chamados.uid_origem Id do usuário reclamante
     * @apiSuccess {Number} chamados.uid_origem Id do usuário reclamado
     * @apiSuccess {Number} chamados.lid Id do livro
     * @apiSuccess {String="SUPORTE","DUVIDA","RECLAMACAO","DENUNCIA"} chamados.tipo Tipo do chamado
     * @apiSuccess {String} chamados.assunto Assunto do chamado
     * @apiSuccess {String} chamados.motivo Motivo do chamado
     * @apiSuccess {String} chamados.texto Texto do chamado
     * @apiSuccess {Timestamp} chamados.dh_inclusao  Data/Hora de abertura do chamado
     * @apiSuccess {Timestamp} chamados.dh_atualizacao  Data/Hora de atualização
     * @apiSuccess {String="ABERTO","FECHADO","CANCELADO","PENDENTE"} chamados.status Status do chamado
     * @apiSuccess {Object[]} chamados.detalhes Lista de interações dos chamado
     * @apiSuccess {Number} chamados.detalhes.chid Id da interação
     * @apiSuccess {Number} chamados.detalhes.uid Id do usuário
     * @apiSuccess {String} chamados.detalhes.mensagem Mensagem
     * @apiSuccess {String="AVISO","MENSAGEM"} chamados.detalhes.tipo Tipo da mensagem
     * @apiSuccess {Timestamp} chamados.detalhes.dh_atualizacao  Data/Hora de atualização
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "cid": "2",
     *             "uid_origem": "44",
     *             "uid_destino": "39",
     *             "lid": "6",
     *             "tipo": "DENUNCIA",
     *             "assunto": "Um assunto sério",
     *             "motivo": "Fraude",
     *             "texto": "Lorem ipsum...",
     *             "dh_inclusao": "2023-04-23 18:03:34",
     *             "dh_atualizacao": null,
     *             "status": "FECHADO",
     *             "detalhes": [
     *                 {
     *                     "chid": "7",
     *                     "uid": "50",
     *                     "mensagem": "Aqui fica a mensagem",
     *                     "dh_atualizacao": "2023-05-03 22:49:03"
     *                 }
     *             ]
     *         }
     *     ]
     *
     */

    /**
     * @api {post} /chamados/adicionar/ Adicionar Chamado
     * @apiName Adicionar
     * @apiGroup Chamados
     * @apiVersion 1.0.0
     *
     * @apiBody {String} [uid_destino] Id do Usuário .
     * @apiBody {String} [lid] Id do Livro
     * @apiBody {String="SUPORTE","DUVIDA","RECLAMACAO","DENUNCIA"} tipo Tipo do chamado.
     * @apiBody {String} assunto Assunto do chamado.
     * @apiBody {String} motivo Motivo do chamado.
     * @apiBody {String} texto Descrição do chamado
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_CHAMADO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9501",
     *         "mensagem": "Ocorreu um erro na criação do seu chamado",
     *         "detalhe": ""
     *     }
     */
    public function adicionarChamado($uid, $dados)
    {
        try {
            $chamadoModel = new ChamadoModel();
            $chamadoId = $chamadoModel->adicionarChamado($uid, $dados);
            if ($chamadoId <= 0) {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_CHAMADO_INCLUSAO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_CHAMADO_CADASTRO_SUCESSO', ['autorId' => $chamadoId]));

    }

    /**
     * @api {get} /chamados/listar/ Listar Chamados
     * @apiName Listar
     * @apiGroup Chamados
     * @apiVersion 1.0.0
     *
     * @apiBody {String} [uid_origem] Id do usuário reclamante.
     * @apiBody {String} [uid_destino] Id do usuário reclamado.
     * @apiBody {String="ABERTO","FECHADO","CANCELADO","PENDENTE"} [status] Status do chamado.
     * @apiBody {String="DENUNCIA","RECLAMACAO","SUGESTAO","BUG","SUPORTE"} [tipo] Tipo do chamado.
     * @apiBody {Boolean} [detalhe=false] Traz detalhes do chamado.

     * @apiUse SAIDA_LISTA_CHAMADOS
     * @apiUse ERR_GENERICOS
     * 
     */
    public function listarChamados($filtro = [], $admin = FALSE)
    {
        try {
            $chamadoModel = new ChamadoModel();
            $arrChamados = [];
            $arrChamados = (array) $chamadoModel->buscarChamados( (array) $filtro, $admin);
            $responseData = json_encode($arrChamados);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {post} /chamados/adicionarDetalhe/ Adicionar detalhe do Chamado
     * @apiName Adicionar Detalhe
     * @apiGroup Chamados
     * @apiVersion 1.0.0
     *
     * @apiBody {String} cid Id do chamado.
     * @apiBody {String} mensagem Mensagem do detalhe do chamado.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_CHAMADO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9501",
     *         "mensagem": "Ocorreu um erro na criação do seu chamado",
     *         "detalhe": ""
     *     }
     */
    public function adicionarChamadoDetalhe($dados)
    {
        try {
            $chamadoDetalheModel = new ChamadoDetalheModel();
            $chamadoDetalheId = $chamadoDetalheModel->adicionarDetalhe($dados,'MENSAGEM');
            if ($chamadoDetalheId <= 0) {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_CHAMADO_DETALHE_INCLUSAO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_CHAMADO_DETALHE_CADASTRO_SUCESSO', ['chamadoDetalheId' => $chamadoDetalheId]));

    }

    /**
     * @api {put} /chamados/alterarChamado/ Alterar status do Chamado
     * @apiName Alterar Status
     * @apiGroup Chamados
     * @apiVersion 1.0.0
     *
     * @apiBody {String} cid Id do chamado.
     * @apiBody {String="ABERTO","FECHADO","CANCELADO","PENDENTE"} status Status do chamado.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_CHAMADO_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9501",
     *         "mensagem": "Ocorreu um erro na criação do seu chamado",
     *         "detalhe": ""
     *     }
     */
    public function alterarChamado($uid, $dados)
    {
        try {
            $chamadoModel = new ChamadoModel();
            $chamadoDetalheModel = new ChamadoDetalheModel();
            
            if ( $chamadoModel->atualizarChamado($uid, $dados) == 0) {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_CHAMADO_NAO_ENCONTRADO'));
            }
            $dados['uid'] = $uid;
            $dados['mensagem'] = "O status do chamado foi alterado para {$dados['status']}";
            unset($dados['status']);
            $chamadoDetalheId = $chamadoDetalheModel->adicionarDetalhe($dados,'AVISO');

            if ($chamadoDetalheId <= 0) {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_CHAMADO_DETALHE_INCLUSAO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_CHAMADO_ATUALIZADO_SUCESSO'));

    }
}