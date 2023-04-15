<?php
include_once 'helpers/TokenHelper.php';
class BaseController
{
    private $httpCodes = array (
        200 => 'HTTP/1.1 200 OK',
        400 => 'HTTP/1.1 400 Bad Request',
        401 => 'HTTP/1.1 401 Unauthorized',
        403 => 'HTTP/1.1 403 Forbidden',
        404 => 'HTTP/1.1 404 Not Found',
        405 => 'HTTP/1.1 405 Method Not Allowed',
        500 => 'HTTP/1.1 500 Internal Server Error',
        501 => 'HTTP/1.1 501 Not Implemented'
    );

    public $token = '';

    public function __call($name, $arguments)
    {
        $this->montarSaida('', array('HTTP/1.1 404 Not Found'));
    }

    public function __construct() {
        $this->token = TokenHelper::extractToken( $this->pegarAutorizacao() );
    }

    protected function pegarSegmentos()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);
        return $uri;
    }

    protected function pegarParametros()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        return $query;
    }

    protected function pegarAutorizacao()
    {
        $requestHeaders = apache_request_headers();
        return (array_key_exists('Authorization', $requestHeaders)) ? $requestHeaders['Authorization'] : '';
    }

    protected function pegarArrayPost($chave='dados')
    {
        return (array_key_exists($chave, $_POST)) ? $_POST[$chave] : [];
    }
    
    protected function pegarArrayPut($chave='dados')
    {
        parse_str(file_get_contents("php://input"), $dados);        
        return (is_array($dados)) ? $dados[$chave] : [];
    }

    protected function montarSaidaOk($dados='')
    {
        $this->montarSaida(
            $dados,
            array('Content-Type: application/json', $this->httpCodes[200])
        );
    }
    protected function montarSaida($dados='', $headers = array())
    {
        header_remove('Set-Cookie');
        if (is_array($headers) && count($headers)) {
            foreach ($headers as $header) {
                header($header);
            }
        }
        echo $dados;
        exit;
    }

    protected function httpResponse($httpCode = 200, $msg='')
    {        
        if (array_key_exists($httpCode,$this->httpCodes)) {
            $this->montarSaida(json_encode(array('message' => $msg)), 
                array('Content-Type: application/json', $this->httpCodes[$httpCode] )
            );        
        }
    }

    protected function httpRawResponse($httpCode = 200, $msg='')
    {        
        if (array_key_exists($httpCode,$this->httpCodes)) {
            $this->montarSaida(json_encode($msg), 
                array('Content-Type: application/json', $this->httpCodes[$httpCode] )
            );        
        }
    }
  
    protected function isAuth() {
        if ( $this->token ) {
            return TokenHelper::validateToken($this->token);
        } else {
            $this->httpResponse(401,'Não autorizado');
        }  
    }

    protected function getUidFromToken() {
        if ( $this->token ) {
            return TokenHelper::extractTokenField($this->token, 'uid');
        } else {
            $this->httpResponse(401,'Não autorizado');
        }
    }

}