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
                        $this->httpResponse(501, 'Ação Indisponível');
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
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501, 'Ação Indisponível');
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
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501, 'Ação Indisponível');
                        break;
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ($this->isAuth()) {
                            $this->deletar($params['param1']);
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
            $assuntoModel = new AssuntoModel();
            $arrAssunto = $assuntoModel->buscarAssunto($iid);
            if (count($arrAssunto) > 0) {
                $responseData = json_encode($arrAssunto);
            } else {
                throw new Exception(helpers\Constantes::getMsg('ERR_ASSUNTO_NAO_ENCONTRADO'), helpers\Constantes::getCode('ERR_ASSUNTO_NAO_ENCONTRADO'));
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
        $this->httpResponse(200, 'Assunto cadastrado com sucesso.', ['assuntoId' => $assuntoId]);
    }

    public function deletar($aid)
    {
        try {
            if (is_numeric($aid)) {
                $assuntoModel = new AssuntoModel();
                if ($assuntoModel->deletarAssunto($aid) > 0) {
                    $this->httpResponse(200, 'Assunto deletado com sucesso.');
                } else {
                    $this->httpResponse(200, 'Assunto não encontrado');
                }
            } else {
                $this->httpResponse(200, 'Identificador inválido');
            }
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'fk_la_assuntos')) {
                        $this->httpResponse(200, 'Este assunto não pode ser deletado pois está vinculado a um ou mais livros');
                    } else {
                        $this->httpResponse(500, Helpers\MessageHelper::fmtException($e));
                    }
                    break;

                default:
                    $this->httpResponse(500, Helpers\MessageHelper::fmtException($e));
                    break;
            }
        }
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
                $this->httpResponse(200, 'Identificador inválido');
            }
        } catch (Exception $e) {
            $this->httpResponse(500, Helpers\MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Assunto atualizado com sucesso.');
    }
}