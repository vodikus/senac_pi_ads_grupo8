<?php
include_once 'includes/BaseController.php';
include_once 'models/UsuarioModel.php';

class UsuarioController extends BaseController
{
    public function __construct() {
        parent::__construct();
    }
    public function processarRequisicao($metodo='', $params=[]) {
        switch ($metodo) {
            case 'GET':
                switch ($params['acao']) {
                    case 'listar':
                        if ( $this->isAuth() ) {
                            $this->listar();
                        } else {
                            $this->httpResponse(401,'Não autorizado');
                        }
                        break;
                    case 'buscar':
                        if ( $this->isAuth() ) {
                            $this->buscar($params['param1']);
                        } else {
                            $this->httpResponse(401,'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501,'Ação Indisponível');
                        break;
                }
                break;
            case 'POST':
                if ( $this->isAuth() ) {
                    $dados = $this->pegarArrayPost();
                    $this->cadastrar($dados);
                } else {
                    $this->httpResponse(401,'Não autorizado');
                }
                break;
            case 'PUT':
                if ( $this->isAuth() ) {
                    $dados = $this->pegarArrayPut();
                    $this->atualizar($params['param1'],$dados);
                } else {
                    $this->httpResponse(401,'Não autorizado');
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ( $this->isAuth() ) {
                            $this->deletar($params['param1']);
                        } else {
                            $this->httpResponse(401,'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501,'Ação Indisponível');
                        break;
                }                
                break;
            default:
                $this->httpResponse(405,'Method Not Allowed');
                break;
        }      
    }

    public function listar()
    {
        try {
            $usuarioModel = new UsuarioModel();
            $arrUsuarios = $usuarioModel->buscarTodosUsuarios();
            $responseData = json_encode($arrUsuarios);
        } catch (Error $e) {
            $this->httpResponse(500,'Erro');
        }
        $this->montarSaidaOk($responseData);
    }
    public function buscar($id=0)
    {
        try {
            $usuarioModel = new UsuarioModel();
            $arrUsuarios = $usuarioModel->buscarUsuario($id);
            $responseData = json_encode($arrUsuarios);
        } catch (Error $e) {
            $this->httpResponse(500,'Erro');
        }
        $this->montarSaidaOk($responseData);
    }
    public function deletar($id=0)
    {
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();            
                if ($usuarioModel->deletarUsuario($id)>0) {
                    $this->httpResponse(200,'Usuário deletado com sucesso.');
                } else {
                    $this->httpResponse(200,'Usuário não encontrado');
                }
            } else {
                $this->httpResponse(200,'Identificador inválido');
            }
        } catch (Error $e) {
            $this->httpResponse(500,'Erro');
        }
    }
    public function cadastrar($dados)
    {       
        try {
            $usuarioModel = new UsuarioModel();
            $usuarioModel->criarUsuario($dados);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'email')) {
                        $this->httpResponse(200,'E-mail já cadastrado.');
                    } elseif (stripos($e->getMessage(),'cpf')) {
                        $this->httpResponse(200,'CPF já cadastrado.');
                    } else {
                        $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    }
                    break;

                default:
                    $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    break;
            }
        } finally {
            $this->httpResponse(200,'Usuário cadastrado com sucesso.');
        }
    }
    public function atualizar($id, $dados)
    {       
        try {
            if (is_numeric($id)) {
                $usuarioModel = new UsuarioModel();            
                $usuarioModel->atualizarUsuario($id, $dados);
            } else {
                $this->httpResponse(200,'Identificador inválido');
            }            
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'email')) {
                        $this->httpResponse(200,'E-mail já cadastrado.');
                    } elseif (stripos($e->getMessage(),'cpf')) {
                        $this->httpResponse(200,'CPF já cadastrado.');
                    } else {
                        $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    }
                    break;

                default:
                    $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    break;
            }
        } finally {
            $this->httpResponse(200,'Usuário atualizado com sucesso.');
        }
    }
}