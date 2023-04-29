<?php
include_once 'includes/BaseController.php';
include_once 'models/UsuarioModel.php';
include_once 'models/UsuarioAssuntoModel.php';
include_once 'models/UsuarioLivroModel.php';

use helpers\MessageHelper;

class UsuarioController extends BaseController
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
                        if ($this->isAuth() && $this->getFieldFromToken('roles') == 'admin') {
                            $this->listar();
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'buscar':
                        if ($this->isAuth()) {
                            $this->buscar($params['param1']);
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
                    case 'adicionar':
                        $this->adicionar($dados);
                        break;
                    case 'vincularAssunto':
                        if ($this->isAuth()) {
                            $this->vincularAssunto($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularAssunto':
                        if ($this->isAuth()) {
                            $this->desvincularAssunto($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'vincularLivro':
                        if ($this->isAuth()) {
                            $this->vincularLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularLivro':
                        if ($this->isAuth()) {
                            $this->desvincularLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'atualizarStatusLivro':
                        if ($this->isAuth()) {
                            $this->atualizarStatusLivro($this->getFieldFromToken('uid'), $dados);
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
                            $this->atualizar($params['param1'], $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ($this->isAuth()) {
                            $this->deletar($params['param1']);
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

    public function listar()
    {
        try {
            $usuarioModel = new UsuarioModel();
            $arrUsuarios = (array) $usuarioModel->buscarTodosUsuarios();
            $responseData = json_encode($arrUsuarios);
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function buscar($id = 0)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                $arrUsuarios = (array) $usuarioModel->buscarUsuario($id);
                $responseData = json_encode($arrUsuarios);
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function deletar($id = 0)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                if ($usuarioModel->deletarUsuario($id) == 0) {
                    $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_USUARIO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_USUARIO_DELETADO_SUCESSO', false));
    }

    public function adicionar($dados)
    {
        try {
            $usuarioModel = new UsuarioModel();
            $usuarioId = $usuarioModel->adicionarUsuario($dados);
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_USUARIO_CADASTRO_SUCESSO', false), ['usuarioId' => $usuarioId]);

    }

    public function atualizar($id, $dados)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                $usuarioModel->atualizarUsuario($id, $dados);
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_USUARIO_ATUALIZADO_SUCESSO', false));
    }

    public function vincularAssunto($uid, $dados)
    {
        try {
            $usuarioAssuntoModel = new UsuarioAssuntoModel();
            $usuarioAssuntoModel->adicionarUsuarioAssunto($uid, $dados);
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_USUARIO_ASSUNTO_VINCULADO', false));
    }

    public function desvincularAssunto($uid, $dados)
    {
        try {
            $usuarioAssuntoModel = new UsuarioAssuntoModel();
            if ($usuarioAssuntoModel->deletarUsuarioAssunto($uid, $dados) == 0) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_USUARIO_ASSUNTO_VINCULO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_USUARIO_ASSUNTO_DESVINCULADO', false));
    }

    public function vincularLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            $usuarioLivroModel->adicionarUsuarioLivro($uid, $dados);
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_USUARIO_LIVRO_VINCULADO', false));
    }

    public function desvincularLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if ($usuarioLivroModel->deletarUsuarioLivro($uid, $dados) == 0) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_USUARIO_LIVRO_VINCULO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_USUARIO_LIVRO_DESVINCULADO', false));
    }

    public function atualizarStatusLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if (!$usuarioLivroModel->atualizarUsuarioLivro($uid, $dados)) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_LIVRO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_USUARIO_LIVRO_STATUS_SUCESSO', false));
    }
}