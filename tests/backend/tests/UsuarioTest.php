<?php

use ClubeLivro\HttpHelper;
use PHPUnit\Framework\TestCase;

class UsuarioTest extends TestCase
{
    private $http;
    private $token;

    public function setUp(): void
    {
        $this->http = new HttpHelper();
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
    public function testBuscarUsuario()
    {
        $response = $this->http->getResponseAuth('GET', 'http://clube-backend/api/usuarios/buscar/39');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $body = (array)json_decode($response->getBody()->__toString(),true);
        $this->assertArrayHasKey('uid', $body[0]);
        $this->assertSame("39", $body[0]['uid']);
    }
    
 
}