<?php
include_once 'includes/BaseController.php';
include_once 'models/UsuarioModel.php';
include_once 'models/UsuarioAssuntoModel.php';
include_once 'models/UsuarioLivroModel.php';
include_once 'models/AmigoModel.php';

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
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'buscar':
                        if ($this->isAuth()) {
                            $this->buscar($params['param1'], ($this->getFieldFromToken('roles') == 'admin') );
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'meu-perfil':
                        if ($this->isAuth()) {
                            $this->buscar($this->getFieldFromToken('uid'));
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'amigos':
                        if ($this->isAuth()) {
                            $this->listarAmigos($this->getFieldFromToken('uid'));
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
                        $this->adicionar($dados);
                        break;
                    case 'vincularAssunto':
                        if ($this->isAuth()) {
                            $this->vincularAssunto($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularAssunto':
                        if ($this->isAuth()) {
                            $this->desvincularAssunto($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'vincularLivro':
                        if ($this->isAuth()) {
                            $this->vincularLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularLivro':
                        if ($this->isAuth()) {
                            $this->desvincularLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'atualizarStatusLivro':
                        if ($this->isAuth()) {
                            $this->atualizarStatusLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'bloquearUsuario':
                        if ($this->isAuth()) {
                            $this->bloquearUsuario($this->getFieldFromToken('uid'), $params['param1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'adicionarAmigo':
                        if ($this->isAuth()) {
                            $this->adicionarAmigo($this->getFieldFromToken('uid'), $params['param1']);
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
                    case 'atualizar':
                        error_log($this->getFieldFromToken('roles'));
                        if ($this->isAuth() && $this->getFieldFromToken('roles') == 'admin') {
                            $this->atualizar($params['param1'], $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'meu-perfil':
                        if ($this->isAuth()) {
                            $this->atualizar($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ($this->isAuth()) {
                            $this->deletar($params['param1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desbloquear':
                        if ($this->isAuth()) {
                            $this->desbloquearUsuario($this->getFieldFromToken('uid'), $params['param1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'removerAmigo':
                        if ($this->isAuth()) {
                            $this->removerAmigo($this->getFieldFromToken('uid'), $params['param1']);
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

    public function listar()
    {
        try {
            $usuarioModel = new UsuarioModel();
            $arrUsuarios = (array) $usuarioModel->buscarTodosUsuarios();
            $responseData = json_encode($arrUsuarios);
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function buscar($id = 0, $completo = false)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                $arrUsuarios = (array) $usuarioModel->buscarUsuario($id, $completo);
                $responseData = json_encode($arrUsuarios);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function deletar($id = 0)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                if ($usuarioModel->deletarUsuario($id) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_DELETADO_SUCESSO'));
    }

    public function adicionar($dados)
    {
        try {
            $usuarioModel = new UsuarioModel();
            $usuarioId = $usuarioModel->adicionarUsuario($dados);
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_CADASTRO_SUCESSO', ['usuarioId' => $usuarioId]));

    }

    public function atualizar($id, $dados)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                $usuarioModel->atualizarUsuario($id, $dados);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_ATUALIZADO_SUCESSO'));
    }

    public function vincularAssunto($uid, $dados)
    {
        try {
            $usuarioAssuntoModel = new UsuarioAssuntoModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    $usuarioAssuntoModel->adicionarUsuarioAssunto($uid, $dado);
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (CLException | CLConstException $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_ASSUNTO_VINCULADO'));
    }

    public function desvincularAssunto($uid, $dados)
    {
        try {
            $usuarioAssuntoModel = new UsuarioAssuntoModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    if ($usuarioAssuntoModel->deletarUsuarioAssunto($uid, $dado) == 0) {
                        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_ASSUNTO_VINCULO_NAO_ENCONTRADO', StringHelper::formataArrayChaveValor($dado)));
                    }
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_ASSUNTO_DESVINCULADO'));
    }

    public function vincularLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    $usuarioLivroModel->adicionarUsuarioLivro($uid, $dado);
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_LIVRO_VINCULADO'));
    }

    public function desvincularLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    if ($usuarioLivroModel->deletarUsuarioLivro($uid, $dado) == 0) {
                        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_LIVRO_VINCULO_NAO_ENCONTRADO', $dado));
                    }
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_LIVRO_DESVINCULADO'));
    }

    public function atualizarStatusLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    if ($usuarioLivroModel->atualizarUsuarioLivro($uid, $dado) == 0) {
                        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_LIVRO_VINCULO_NAO_ENCONTRADO'));
                    }
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_LIVRO_STATUS_SUCESSO'));
    }

    public function bloquearUsuario($uid = 0, $uid_blq = 0)
    {
        try {
            if (is_numeric($uid) && is_numeric($uid_blq)) {
                $usuarioModel = new UsuarioModel();
                if ($usuarioModel->bloquearUsuario($uid,$uid_blq) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_BLOQUEIO_SUCESSO'));
    }    

    public function desbloquearUsuario($uid = 0, $uid_blq = 0)
    {
        try {
            if (is_numeric($uid) && is_numeric($uid_blq)) {
                $usuarioModel = new UsuarioModel();
                if ($usuarioModel->debloquearUsuario($uid,$uid_blq) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_DESBLOQUEIO_SUCESSO'));
    }    

    public function adicionarAmigo($uid = 0, $uid_amigo = 0)
    {
        try {
            if (is_numeric($uid) && is_numeric($uid_amigo)) {
                $amigoModel = new AmigoModel();
                $amigoModel->adicionarAmigo($uid, $uid_amigo);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AMIGO_ADICIONADO_SUCESSO'));
    }    

    public function removerAmigo($uid = 0, $uid_amigo = 0)
    {
        try {
            if (is_numeric($uid) && is_numeric($uid_amigo)) {
                $amigoModel = new AmigoModel();
                if ($amigoModel->removerAmigo($uid, $uid_amigo) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AMIGO_REMOVIDO_SUCESSO'));
    }    

    public function listarAmigos($uid = 0)
    {
        try {
            if (is_numeric($uid)) {
                $amigoModel = new AmigoModel();
                $arrUsuarios = (array) $amigoModel->listarAmigos($uid);
                $responseData = json_encode($arrUsuarios);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }    
}