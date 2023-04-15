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
                            $this->listar($this->getUidFromToken());
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
                                $this->adicionar($this->getUidFromToken(), $dados);
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
                if ( $this->isAuth() ) {
                    $dados = $this->pegarArrayPut();
                    // $this->atualizar($params['param1'],$dados);
                } else {
                    $this->httpResponse(401,'Não autorizado');
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ( $this->isAuth() ) {
                            // $this->deletar($params['param1']);
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

}