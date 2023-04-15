<?php
include_once 'helpers/TokenHelper.php';
include_once 'includes/BaseController.php';
include_once 'models/UsuarioModel.php';

class AuthController extends BaseController
{
    private $expSeconds = 300;
    public function processarRequisicao($metodo='', $params=[]) {
        switch ($metodo) {
            case 'POST':
                switch ($params['acao']) {
                    case 'getToken':       
                        if ( array_key_exists('username', $_POST) && array_key_exists('password', $_POST) ) {
                            $usuarioModel = new UsuarioModel();
                            if ( $usuarioModel->validarUsuarioSenha($_POST['username'], $_POST['password']) == 1 ) {
                                $token = [ 
                                    "access_token" => TokenHelper::generateToken($_POST['username'], 'user', $usuarioModel->buscaPorEmail($_POST['username']), $this->expSeconds),
                                    "expires_in" => $this->expSeconds,
                                    "token_type" => "bearer"
                                ];
                                $this->httpRawResponse(200,$token);    
                            } else {
                                $this->httpResponse(401,'E-mail ou senha não conferem');    
                            }
                        } else {
                            $this->httpResponse(401,'É necessário informar e-mail e senha');
                        }                        
                        break;
                    case 'authToken':       
                        $token = TokenHelper::extractToken( $this->pegarAutorizacao() );
                        if ( $token ) {
                            if ( TokenHelper::validateToken($token) ) {
                                $this->httpResponse(200,'Token ok');
                            } else {
                                $this->httpResponse(401,'Token inválido');
                            }
                        } else {
                            $this->httpResponse(401,'É necessário informar o token');
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


}