<?php
include_once 'helpers/Constantes.php';
include_once 'helpers/MessageHelper.php';
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
                                $usuario = $usuarioModel->buscaPorEmail($_POST['username']);
                                $token = [ 
                                    "access_token" => Helpers\TokenHelper::generateToken($_POST['username'], $usuario['role'], $usuario['uid'], $this->expSeconds),
                                    "expires_in" => $this->expSeconds,
                                    "token_type" => "bearer"
                                ];
                                $this->httpRawResponse(200,$token);    
                            } else {
                                $this->httpResponse(401,Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_EMAIL_SENHA_INVALIDO')));    
                            }
                        } else {
                            $this->httpResponse(401,Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_EMAIL_SENHA_REQUERIDO')));
                        }                        
                        break;
                    case 'authToken':       
                        $token = Helpers\TokenHelper::extractToken( $this->pegarAutorizacao() );
                        if ( $token ) {
                            if ( Helpers\TokenHelper::validateToken($token) ) {
                                $this->httpResponse(200,Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('MSG_TOKEN_OK')));
                            } else {
                                $this->httpResponse(401,Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_TOKEN_INVALIDO')));
                            }
                        } else {
                            $this->httpResponse(401,Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_TOKEN_REQUERIDO')));
                        }                        
                        break;
                    default:
                        $this->httpResponse(501,Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_ACAO_INDISPONIVEL')));
                        break;
                }
                break;
            default:
                $this->httpResponse(405,Helpers\MessageHelper::fmtMsgConst(helpers\Constantes::getConst('ERR_METODO_NAO_PERMITIDO')));
                break;
        }      
    }


}