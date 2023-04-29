<?php

use ClubeLivro\HttpHelper;
use helpers\MessageHelper;
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
    public function testBuscarAssunto($assunto)
    {
        $response = $this->http->getResponseAuth('GET', "http://clube-backend/api/assuntos/buscar/{$assunto['saida']['assuntoId']}");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $outAssunto = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('iid', $outAssunto);
        $this->assertArrayHasKey('nome_assunto', $outAssunto);
        $this->assertArrayHasKey('dh_atualizacao', $outAssunto);

        $this->assertSame($outAssunto['iid'], $assunto['saida']['assuntoId']);
        $this->assertSame($outAssunto['nome_assunto'], $assunto['entrada']['nome_assunto']);
    }

    public function testBuscarAssuntoInexistente()
    {
        $response = $this->http->getResponseAuth('GET', "http://clube-backend/api/assuntos/buscar/999999999");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $assunto = (array) json_decode($response->getBody()->__toString(), true);
        
        $this->assertIsArray($assunto);

    }

    public function testAdicionarAssunto()
    {
        $faker = Faker\Factory::create('pt_BR');
        $assunto['entrada'] = [
            'nome_assunto' => $faker->city
        ];
        $response = $this->http->getResponseAuth('POST', 'http://clube-backend/api/assuntos/adicionar', [
            'form_params' => [
                'dados[nome_assunto]' => $assunto['entrada']['nome_assunto']
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $assunto['saida'] = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $assunto['saida']);
        $this->assertArrayHasKey('assuntoId', $assunto['saida']);

        return $assunto;
    }

    /**
     * @depends testAdicionarAssunto
     */
    public function testAdicionarAssuntoDuplicado($assunto)
    {
        $response = $this->http->getResponseAuth('POST', 'http://clube-backend/api/assuntos/adicionar', [
            'form_params' => [
                'dados[nome_assunto]' => $assunto['entrada']['nome_assunto']
            ]
        ]);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $assunto = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $assunto);
    }    
  
    /**
     * @depends testAdicionarAssunto
     */
    public function testAtualizarAssuntoDuplicado($inAssunto)
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

            $response = $this->http->getResponseAuth('PUT', "http://clube-backend/api/assuntos/atualizar/{$assuntos[0]['iid']}", [
                'form_params' => [
                    'dados[nome_assunto]' => $inAssunto['entrada']['nome_assunto']
                ]
            ]);
    
            $this->assertEquals(500, $response->getStatusCode());
            $this->assertJson($response->getBody()->__toString());
            $outAssunto = (array) json_decode($response->getBody()->__toString(), true);
            
            $this->assertArrayHasKey('message', $outAssunto);

            $this->assertSame($outAssunto['message'], MessageHelper::fmtMsgConst('ERR_ASSUNTO_JA_EXISTENTE'));
        }
    }

    /**
     * @depends testAdicionarAssunto
     */
    public function testAtualizarAssunto($assunto)
    {
        $response = $this->http->getResponseAuth('PUT', "http://clube-backend/api/assuntos/atualizar/{$assunto['saida']['assuntoId']}", [
            'form_params' => [
                'dados[nome_assunto]' => $assunto['entrada']['nome_assunto'] . " alterado"
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $outAssunto = (array) json_decode($response->getBody()->__toString(), true);
        
        $this->assertArrayHasKey('message', $outAssunto);

        return $assunto;
    }

    public function testAtualizarAssuntoInexistente()
    {
        $response = $this->http->getResponseAuth('PUT', "http://clube-backend/api/assuntos/atualizar/999999999", [
            'form_params' => [
                'dados[nome_assunto]' => "Lorem Ipsum"
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $outAssunto = (array) json_decode($response->getBody()->__toString(), true);
        
        $this->assertIsArray($outAssunto);

        $this->assertArrayHasKey('message', $outAssunto);
        
        $this->assertSame($outAssunto['message'], MessageHelper::fmtMsgConst('ERR_ASSUNTO_NAO_ENCONTRADO'));
    }

    /**
     * @depends testAtualizarAssunto
     */
    public function testDeletarAssunto($assunto)
    {
        $response = $this->http->getResponseAuth('DELETE', "http://clube-backend/api/assuntos/deletar/{$assunto['saida']['assuntoId']}");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $assunto = (array) json_decode($response->getBody()->__toString(), true);
        
        $this->assertArrayHasKey('message', $assunto);
    } 
 
}