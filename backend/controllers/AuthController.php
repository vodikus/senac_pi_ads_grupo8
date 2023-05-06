<?php
use helpers\MessageHelper;
use helpers\TokenHelper;

include_once 'helpers/TokenHelper.php';
include_once 'models/UsuarioModel.php';

class AuthController extends BaseController
{
    private $expSeconds = 300;


    /**
     * @api {get} /auth/getToken/ Solicita um token
     * @apiName Solicita Token
     * @apiGroup Autenticação
     * @apiVersion 1.0.0
     *
     * @apiSuccess {String} access_token Token no formato JWT
     * @apiSuccess {Number} expires_in Tempo de expiração do token (segundos)
     * @apiSuccess {String} token_type Tipo do token
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.hjGaHgUSGShGSJShGSg.TRHtshfsegerGErg43t3gg",
     *         "expires_in": 300,
     *         "token_type": "bearer"
     *     }
     * 
     * @apiUse ERR_GENERICOS
     * 
     */

    /**
     * @api {get} /auth/authToken/ Valida um token
     * @apiName Valida Token
     * @apiGroup Autenticação
     * @apiVersion 1.0.0
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * 
     */     

    public function processarRequisicao($metodo='', $params=[]) {
        switch ($metodo) {
            case 'POST':
                switch ($params['acao']) {
                    case 'getToken':  
                        $entrada = $this->pegarArrayJson();     
                        if ( array_key_exists('username', $entrada) && array_key_exists('password', $entrada) ) {
                            $usuarioModel = new UsuarioModel();
                            if ( $usuarioModel->validarUsuarioSenha($entrada['username'], $entrada['password']) == 1 ) {
                                $usuario = $usuarioModel->buscaPorEmail($entrada['username']);
                                $token = [ 
                                    "access_token" => TokenHelper::generateToken($entrada['username'], $usuario['role'], $usuario['uid'], $this->expSeconds),
                                    "expires_in" => $this->expSeconds,
                                    "token_type" => "bearer"
                                ];
                                $this->httpRawResponse(200,json_encode($token));
                            } else {
                                $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_EMAIL_SENHA_INVALIDO'));
                            }
                        } else {
                            $this->httpRawResponse(401,MessageHelper::fmtMsgConstJson('ERR_EMAIL_SENHA_REQUERIDO'));
                        }                        
                        break;
                    case 'authToken':       
                        $token = TokenHelper::extractToken( $this->pegarAutorizacao() );
                        if ( $token ) {
                            if ( TokenHelper::validateToken($token) ) {
                                $this->httpRawResponse(200,MessageHelper::fmtMsgConstJson('MSG_TOKEN_OK'));
                            } else {
                                $this->httpRawResponse(401,MessageHelper::fmtMsgConstJson('ERR_TOKEN_INVALIDO'));
                            }
                        } else {
                            $this->httpRawResponse(401,MessageHelper::fmtMsgConstJson('ERR_TOKEN_REQUERIDO'));
                        }                        
                        break;
                    default:
                        $this->httpRawResponse(501,MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            default:
                $this->httpRawResponse(405,MessageHelper::fmtMsgConstJson('ERR_METODO_NAO_PERMITIDO'));
                break;
        }      
    }


}