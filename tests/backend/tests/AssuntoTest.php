<?php

use ClubeLivro\HttpHelper;
use PHPUnit\Framework\TestCase;

class AssuntoTest extends TestCase
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

    public function testListarAssunto()
    {
        $response = $this->http->getResponseAuth('GET', 'http://clube-backend/api/assuntos/listar');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $assuntos = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertIsArray($assuntos);

        if (count($assuntos) > 0) {
            $this->assertArrayHasKey('iid', $assuntos[0]);
            $this->assertArrayHasKey('nome_assunto', $assuntos[0]);
            $this->assertArrayHasKey('dh_atualizacao', $assuntos[0]);
        }
    }

    /**
     * @depends testAdicionarAssunto
     */
    public function testBuscarAssunto($assuntoId)
    {
        $response = $this->http->getResponseAuth('GET', "http://clube-backend/api/assuntos/buscar/$assuntoId");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $assunto = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('iid', $assunto);
        $this->assertArrayHasKey('nome_assunto', $assunto);
        $this->assertArrayHasKey('dh_atualizacao', $assunto);

        $this->assertSame($assunto['iid'], $assuntoId);
        $this->assertSame($assunto['nome_assunto'], 'PHPUnit - Teste automatizado');

        return $assuntoId;
    }

    public function testAdicionarAssunto()
    {
        $response = $this->http->getResponseAuth('POST', 'http://clube-backend/api/assuntos/adicionar', [
            'form_params' => [
                'dados[nome_assunto]' => 'PHPUnit - Teste automatizado'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $assunto = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $assunto);
        $this->assertArrayHasKey('assuntoId', $assunto);

        return $assunto['assuntoId'];
    }

    /**
     * @depends testBuscarAssunto
     */
    public function testAtualizarAssunto($assuntoId)
    {
        $response = $this->http->getResponseAuth('PUT', "http://clube-backend/api/assuntos/atualizar/$assuntoId", [
            'form_params' => [
                'dados[nome_assunto]' => 'PHPUnit - Teste automatizado alterado'
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $assunto = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $assunto);

        return $assuntoId;
    }

    /**
     * @depends testAtualizarAssunto
     */
    public function testDeletarAssunto($assuntoId)
    {
        $response = $this->http->getResponseAuth('DELETE', "http://clube-backend/api/assuntos/deletar/$assuntoId");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $assunto = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $assunto);
    }


}