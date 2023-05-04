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
                            // $this->atualizar($params['param1'], $dados);
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
     * @apiDefine ERR_GENERICOS
     *
     * @apiError (Erro 4xx) 401 Não autorizado
     * @apiError (Erro 4xx) 405 Método não permitido
     * @apiError (Erro 5xx) 501 Ação Indisponível
     * @apiError (Erro 5xx) 9000 Erro não definido
     * @apiError (Erro 5xx) 9001 Identificador inválido
     * @apiError (Erro 5xx) 9004 A entrada deve ser um JSON válido
     *
     */

    /**
     * @apiDefine ERR_CHAMADO_PADRAO
     *
     * @apiError (Erro 4xx) 9500 Chamado não encontrado.
     *
     */

    /**
     * @apiDefine SAIDA_LISTA
     *
     * @apiSuccess {Object[]} chamados Lista de chamados
     * @apiSuccess {Number} chamados.iid ID do assunto
     * @apiSuccess {String} chamados.nome_assunto Nome do assunto
     * @apiSuccess {Timestamp} chamados.dh_atualizacao  Data/Hora de atualização
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "iid": "1",
     *         "nome_assunto": "Fantasia",
     *         "dh_atualizacao": "2023-04-15 20:45:26"
     *       }
     *     ]
     *
     */

    /**
     * @apiDefine SAIDA_PADRAO
     *
     * @apiSuccess {Number} codigo Código da mensagem
     * @apiSuccess {String} mensagem Mensagem de retorno
     * @apiSuccess {Object} detalhe Objeto contendo detalhes do retorno
     * @apiSuccess {Number} detalhe.chamadoId  Id do assunto inserido
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "codigo": 1234,
     *         "mensagem": "Sua operação foi realizada com sucesso",
     *         "detalhe": ""
     *     }
     *
     */

    /**
     * @api {post} /chamados/adicionar/ Adiciona chamado
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
     * @api {get} /chamados/listar/ Lista os chamados
     * @apiName Listar
     * @apiGroup Chamados
     * @apiVersion 1.0.0
     *
     * @apiBody {String} uid_origem Id do usuário reclamante.
     * @apiBody {String} uid_destino Id do usuário reclamado.
     * @apiBody {String="ABERTO","FECHADO","CANCELADO","PENDENTE"} status Status do chamado.
     * @apiBody {String="DENUNCIA","RECLAMACAO","SUGESTAO","BUG","SUPORTE"} tipo Tipo do chamado.

     * @apiUse SAIDA_LISTA
     * @apiUse ERR_GENERICOS
     * 
     */
    public function listarChamados($filtro = [], $admin = FALSE)
    {
        try {
            $chamadoModel = new ChamadoModel();
            $arrChamados = [];
            $arrChamados = (array) $chamadoModel->buscarChamados($filtro, $admin);
            $responseData = json_encode($arrChamados);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }
}