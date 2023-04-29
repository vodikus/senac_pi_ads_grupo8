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
                switch ($params['acao']) {
                    case 'meus-chamados':
                        // $this->listar();
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
                            $this->adicionarChamado($this->getFieldFromToken('uid'), $dados);
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
                            // $this->atualizar($params['param1'], $dados);
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

    public function adicionarChamado($uid, $dados)
    {
        try {
            $chamadoModel = new ChamadoModel();
            $chamadoId = $chamadoModel->adicionarChamado($uid, $dados);
            if ($chamadoId <= 0) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_CHAMADO_INCLUSAO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Chamado aberto com sucesso.', ['chamadoId' => $chamadoId]);

    }

}