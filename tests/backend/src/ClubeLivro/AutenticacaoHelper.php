<?php
namespace ClubeLivro;
class AutenticacaoHelper
{
    private $http;
    private $token;

    public function __construct()
    {
        $this->http = new \GuzzleHttp\Client(['http_errors' => false]);        
    }

    public function __destruct()
    {
        $this->http = null;
    }

    public function getResponse() {
        $response = $this->http->request('POST', 'http://clube-backend/api/auth/getToken', [
            'form_params' => [
                'username' => 'teste@teste.com.br',
                'password' => '1234'
            ]
        ]);
        return $response;
    }

    public function getToken() {
        $response = $this->getResponse();
        $this->token = (array)json_decode($response->getBody()->__toString(),true);
        return $this->token;
    }

}