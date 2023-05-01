<?php

use ClubeLivro\AutenticacaoHelper;
use helpers\MessageHelper;
use PHPUnit\Framework\TestCase;

class AutenticacaoTest extends TestCase
{
    private $http;
    
    public function setUp(): void
    {
        $this->http = new GuzzleHttp\Client(['http_errors' => false]);        
    }

    public function tearDown(): void
    {
        $this->http = null;
    }

    /**
     * Testa se o código de resposta é 200 e te o id do usuário é um inteiro
     *
     * @return void
     */
    public function testPegarToken()
    {
        $authHelper = new AutenticacaoHelper();
        $response = $authHelper->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $this->processToken($authHelper->getToken());

    }

    public function testValidaToken()
    {
        $authHelper = new AutenticacaoHelper();
        $token = $authHelper->getToken();
        $response = $this->http->request('POST', 'http://clube-backend/api/auth/authToken', [
            'headers' => [
                'Authorization' => "Bearer {$token['access_token']}"
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $body = (array)json_decode($response->getBody()->__toString(),true);
        $this->assertArrayHasKey('message', $body);
        $this->assertSame(MessageHelper::fmtMsgConst('MSG_TOKEN_OK'), $body['message']);
    }
    
    private function processToken($token) {
        $this->assertArrayHasKey('access_token', $token);
        $this->assertArrayHasKey('expires_in', $token);
        $this->assertArrayHasKey('token_type', $token);
    }

}