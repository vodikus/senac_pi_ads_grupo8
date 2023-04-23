<?php
namespace ClubeLivro;
class HttpHelper
{
    private $http;

    public function __construct()
    {
        $this->http = new \GuzzleHttp\Client(['http_errors' => false]);
    }

    public function __destruct()
    {
        $this->http = null;
    }

    public function getResponse($method, $url, $params = [])
    {
        $response = $this->http->request($method, $url, $params);
        return $response;
    }

    public function getResponseAuth($method, $url, $params = [])
    {
        $authHelper = new AutenticacaoHelper();
        $token = $authHelper->getToken();
        $response = $this->getResponse(
            $method,
            $url,
            array_merge(
                $params,
                [
                    'headers' => [
                        'Authorization' => "Bearer {$token['access_token']}"
                    ]
                ]
            )
        );
        return $response;
    }


}