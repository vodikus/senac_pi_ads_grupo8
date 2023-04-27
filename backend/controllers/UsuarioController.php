<?php
include_once 'includes/BaseController.php';
include_once 'helpers/Constantes.php';
include_once 'helpers/MessageHelper.php';
include_once 'models/UsuarioModel.php';
include_once 'models/UsuarioAssuntoModel.php';
include_once 'models/UsuarioLivroModel.php';

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
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'buscar':
                        if ($this->isAuth()) {
                            $this->buscar($params['param1']);
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
                    case 'adicionar':
                        $this->adicionar($dados);
                        break;
                    case 'vincularAssunto':
                        if ($this->isAuth()) {
                            $this->vincularAssunto($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'desvincularAssunto':
                        if ($this->isAuth()) {
                            $this->desvincularAssunto($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'vincularLivro':
                        if ($this->isAuth()) {
                            $this->vincularLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'desvincularLivro':
                        if ($this->isAuth()) {
                            $this->desvincularLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, 'Não autorizado');
                        }
                        break;
                    case 'atualizarStatusLivro':
                        if ($this->isAuth()) {
                            $this->atualizarStatusLivro($this->getFieldFromToken('uid'), $dados);
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

    public function listar()
    {
        try {
            $usuarioModel = new UsuarioModel();
            $arrUsuarios = $usuarioModel->buscarTodosUsuarios();
            $responseData = json_encode($arrUsuarios);
        } catch (Exception $e) {
            $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function buscar($id = 0)
    {
        try {
            $usuarioModel = new UsuarioModel();
            $arrUsuarios = $usuarioModel->buscarUsuario($id);
            if ( count($arrUsuarios) > 0 ) {
                $responseData = json_encode($arrUsuarios);
            } else {
                throw New Exception( helpers\Constantes::getMsg('ERR_USUARIO_NAO_ENCONTRADO'), helpers\Constantes::getCode('ERR_USUARIO_NAO_ENCONTRADO') );
            }
        } catch (Exception $e) {
            $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function deletar($id = 0)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                if ($usuarioModel->deletarUsuario($id) > 0) {
                    $this->httpResponse(200, 'Usuário deletado com sucesso.');
                } else {
                    $this->httpResponse(200, 'Usuário não encontrado');
                }
            } else {
                $this->httpResponse(200, 'Identificador inválido');
            }
        } catch (Exception $e) {
            $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
        }
    }

    public function adicionar($dados)
    {
        try {
            $usuarioModel = new UsuarioModel();
            $usuarioId = $usuarioModel->adicionarUsuario($dados);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'email')) {
                        $this->httpResponse(200, 'E-mail já cadastrado.');
                    } elseif (stripos($e->getMessage(), 'cpf')) {
                        $this->httpResponse(200, 'CPF já cadastrado.');
                    } else {
                        $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
                    }
                    break;

                default:
                    $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
                    break;
            }
        }
        $this->httpResponse(200, 'Usuário cadastrado com sucesso.', ['usuarioId' => $usuarioId]);

    }

    public function atualizar($id, $dados)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();
                $usuarioModel->atualizarUsuario($id, $dados);
            } else {
                $this->httpResponse(200, 'Identificador inválido');
            }
        } catch (Exception $e) {
            $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Usuário atualizado com sucesso.');
    }

    public function vincularAssunto($uid, $dados)
    {
        try {
            $usuarioAssuntoModel = new UsuarioAssuntoModel();
            if ($usuarioAssuntoModel->adicionarUsuarioAssunto($uid, $dados) <= 0) {
                $this->httpResponse(200, 'Assunto não encontrado');
            }
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'PRIMARY')) {
                        $this->httpResponse(200, 'Este Assunto já está vinculado a este usuário.');
                    } else {
                        $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
                    }
                    break;

                default:
                    $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
                    break;
            }
        }
        $this->httpResponse(200, 'Assunto vinculado ao usuário com sucesso.');
    }

    public function desvincularAssunto($uid, $dados)
    {
        try {
            $usuarioAssuntoModel = new UsuarioAssuntoModel();
            if ($usuarioAssuntoModel->deletarUsuarioAssunto($uid, $dados) <= 0) {
                $this->httpResponse(200, 'Assunto não encontrado');
            }
        } catch (Exception $e) {
            $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Assunto desvinculado do usuário com sucesso.');
    }

    public function vincularLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if ($usuarioLivroModel->adicionarUsuarioLivro($uid, $dados) <= 0) {
                $this->httpResponse(200, 'Livro não encontrado');
            }
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(), 'PRIMARY')) {
                        $this->httpResponse(200, 'Este Livro já está vinculado a este usuário.');
                    } else {
                        $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
                    }
                    break;

                default:
                    $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
                    break;
            }
        }
        $this->httpResponse(200, 'Livro vinculado ao usuário com sucesso.');
    }

    public function desvincularLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if ($usuarioLivroModel->deletarUsuarioLivro($uid, $dados) <= 0) {
                $this->httpResponse(200, 'Livro não encontrado');
            }
        } catch (Exception $e) {
            $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Livro desvinculado do usuário com sucesso.');
    }

    public function atualizarStatusLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if (!$usuarioLivroModel->atualizarUsuarioLivro($uid, $dados)) {
                $this->httpResponse(200, Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_LIVRO_NAO_ENCONTRADO')));
            }
        } catch (Exception $e) {
            $this->httpResponse(200, Helpers\MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, 'Status do livro alterado com sucesso.');
    }
}