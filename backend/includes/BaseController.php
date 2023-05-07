<?php
include_once 'helpers/TokenHelper.php';
include_once 'helpers/MessageHelper.php';

use helpers\MessageHelper;
use helpers\TokenHelper;

class BaseController
{
    private $httpCodes = array(
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

    public function __construct()
    {
        $this->token = TokenHelper::extractToken($this->pegarAutorizacao());
    }

    protected function validaJSON($str)
    {
        json_decode($str);
        return json_last_error() == JSON_ERROR_NONE;
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

    protected function pegarArrayJson()
    {
        $raw_data = file_get_contents("php://input");

        if (strlen($raw_data) > 0) {
            if ($this->validaJSON($raw_data)) {
                $arrJson = json_decode($raw_data, true);
                return $arrJson;
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_JSON_INVALIDO'));
            }
        }
    }

    protected function pegarArrayPost($chave = 'dados')
    {
        return (array_key_exists($chave, $_POST)) ? $_POST[$chave] : [];
    }

    protected function pegarArrayPut($chave = 'dados')
    {
        parse_str(file_get_contents("php://input"), $dados);
        return (isset($dados) && is_array($dados) && array_key_exists($chave, $dados)) ? $dados[$chave] : [];
    }

    protected function montarSaidaOk($dados = '')
    {
        $this->montarSaida(
            $dados,
            array($this->httpCodes[200])
        );
    }
    protected function montarSaida($dados = '', $headers = array())
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

    protected function httpResponse($httpCode = 200, $msg = '', $arrData = [])
    {
        if (array_key_exists($httpCode, $this->httpCodes)) {
            $this->montarSaida(
                json_encode(array_merge(['mensagem' => $msg], $arrData)),
                array($this->httpCodes[$httpCode])
            );
        }
    }

    protected function httpRawResponse($httpCode = 200, $msg = '')
    {
        if (array_key_exists($httpCode, $this->httpCodes)) {
            $this->montarSaida(
                $msg,
                array($this->httpCodes[$httpCode])
            );
        }
    }

    protected function isAuth($trataErro = true)
    {
        if ($this->token) {
            return TokenHelper::validateToken($this->token);
        } else {
            if ($trataErro)
                $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
        }
    }

    protected function getFieldFromToken($field)
    {
        if ($this->token) {
            return Helpers\TokenHelper::extractTokenField($this->token, $field);
        } else {
            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
        }
    }


}