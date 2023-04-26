<?php
include_once 'includes/BaseController.php';
include_once 'models/EnderecoModel.php';

class EnderecoController extends BaseController
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
                            $this->listar($this->getFieldFromToken('uid'));
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
                    $dados = $this->pegarArrayPost();
                    switch ($params['acao']) {
                        case 'adicionar':
                            if ( $this->isAuth() ) {
                                $this->adicionar($this->getFieldFromToken('uid'), $dados);
                            } else {
                                $this->httpResponse(401,'Não autorizado');
                            }
                            break;
                        default:
                            $this->httpResponse(501,'Ação Indisponível');
                            break;
                    }
                break;
            case 'PUT':
                switch ($params['acao']) {
                    case 'atualizar':
                        $dados = $this->pegarArrayPut();
                        if ( $this->isAuth() ) {
                            $this->atualizar($params['param1'], $this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401,'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501,'Ação Indisponível');
                        break;
                }                
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ( $this->isAuth() ) {
                            $this->deletar($params['param1'], $this->getFieldFromToken('uid'));
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

    public function listar($uid = 0)
    {
        try {
            $enderecoModel = new EnderecoModel();
            $arrEnderecos = $enderecoModel->buscarEndereco($uid);
            $responseData = json_encode($arrEnderecos);
        } catch (Error $e) {
            $this->httpResponse(500,$e->getMessage());
        }
        $this->montarSaidaOk($responseData);
    }

    public function adicionar($uid, $dados)
    {       
        try {
            $enderecoModel = new EnderecoModel();
            $enderecoModel->adicionarEndereco($uid, $dados);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'usu_ende_cep_uk')) {
                        $this->httpResponse(200,'É permitido apenas um endereço com o mesmo CEP.');
                    } else {
                        $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    }
                    break;

                default:
                    $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    break;
            }
        } finally {
            $this->httpResponse(200,'Endereço cadastrado com sucesso.');
        }
    }

    public function deletar($eid, $uid)
    {
        try {
            if ( is_numeric($eid) && is_numeric($uid) ) {
                $enderecoModel = new EnderecoModel();            
                if ($enderecoModel->deletarEndereco($eid, $uid)>0) {
                    $this->httpResponse(200,'Endereço deletado com sucesso.');
                } else {
                    $this->httpResponse(200,'Endereço não encontrado');
                }
            } else {
                $this->httpResponse(200,'Identificador inválido');
            }
        } catch (Error $e) {
            $this->httpResponse(500,'Erro');
        }
    }

    public function atualizar($eid, $uid, $dados)
    {       
        try {
            if ( is_numeric($eid) && is_numeric($uid) ) {
                $enderecoModel = new EnderecoModel();            
                if ( $enderecoModel->atualizarEndereco($eid, $uid, $dados) <= 0) {
                    $this->httpResponse(200,'Endereço não localizado');
                }
            } else {
                $this->httpResponse(200,'Identificador inválido');
            }            
        } catch (Exception $e) {
            $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
        } finally {
            $this->httpResponse(200,'Endereço atualizado com sucesso.');
        }
    }
}