<?php

use ClubeLivro\HttpHelper;
use helpers\MessageHelper;
use PHPUnit\Framework\TestCase;

class AutorTest extends TestCase
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

    public function testListarAutores()
    {
        $response = $this->http->getResponseAuth('GET', 'http://clube-backend/api/autores/listar');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $autores = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertIsArray($autores);

        if (count($autores) > 0) {
            $this->assertArrayHasKey('aid', $autores[0]);
            $this->assertArrayHasKey('nome_autor', $autores[0]);
            $this->assertArrayHasKey('dh_atualizacao', $autores[0]);
        }
    }

    /**
     * @depends testAdicionarAutor
     */
    public function testBuscarAutor($autor)
    {
        $response = $this->http->getResponseAuth('GET', "http://clube-backend/api/autores/buscar/{$autor['saida']['autorId']}");
        $this->assertEquals(501, $response->getStatusCode());
        // $this->assertJson($response->getBody()->__toString());
        // $outAutor = (array) json_decode($response->getBody()->__toString(), true);

        // $this->assertArrayHasKey('aid', $outAutor);
        // $this->assertArrayHasKey('nome_autor', $outAutor);
        // $this->assertArrayHasKey('dh_atualizacao', $outAutor);

        // $this->assertSame($outAutor['aid'], $autor['saida']['autorId']);
        // $this->assertSame($outAutor['nome_autor'], $autor['entrada']['nome_autor']);
    }

    public function testBuscarAutorInexistente()
    {
        $response = $this->http->getResponseAuth('GET', "http://clube-backend/api/autores/buscar/999999999");
        $this->assertEquals(501, $response->getStatusCode());
        // $this->assertJson($response->getBody()->__toString());
        // $autor = (array) json_decode($response->getBody()->__toString(), true);

        // $this->assertIsArray($autor);
    }

    public function testAdicionarAutor()
    {
        $faker = Faker\Factory::create('pt_BR');
        $autor['entrada'] = [
            'nome_autor' => $faker->name
        ];
        $response = $this->http->getResponseAuth('POST', 'http://clube-backend/api/autores/adicionar', [
            'form_params' => [
                'dados[nome_autor]' => $autor['entrada']['nome_autor']
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $autor['saida'] = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $autor['saida']);
        $this->assertArrayHasKey('autorId', $autor['saida']);

        return $autor;
    }

    /**
     * @depends testAdicionarAutor
     */
    public function testAdicionarAutorDuplicado($autor)
    {
        $response = $this->http->getResponseAuth('POST', 'http://clube-backend/api/autores/adicionar', [
            'form_params' => [
                'dados[nome_autor]' => $autor['entrada']['nome_autor']
            ]
        ]);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $autor = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $autor);
    }

    /**
     * @depends testAdicionarAutor
     */
    public function testAtualizarAutorDuplicado($inAutor)
    {
        $response = $this->http->getResponseAuth('GET', 'http://clube-backend/api/autores/listar');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $autores = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertIsArray($autores);

        if (count($autores) > 0) {
            $this->assertArrayHasKey('aid', $autores[0]);
            $this->assertArrayHasKey('nome_autor', $autores[0]);
            $this->assertArrayHasKey('dh_atualizacao', $autores[0]);

            $response = $this->http->getResponseAuth('PUT', "http://clube-backend/api/autores/atualizar/{$autores[0]['aid']}", [
                'form_params' => [
                    'dados[nome_autor]' => $inAutor['entrada']['nome_autor']
                ]
            ]);

            $this->assertEquals(500, $response->getStatusCode());
            $this->assertJson($response->getBody()->__toString());
            $outAutor = (array) json_decode($response->getBody()->__toString(), true);

            $this->assertArrayHasKey('message', $outAutor);

            $this->assertSame($outAutor['message'], MessageHelper::fmtMsgConst('ERR_AUTOR_JA_EXISTENTE'));
        }
    }

    /**
     * @depends testAdicionarAutor
     */
    public function testAtualizarAutor($autor)
    {
        $response = $this->http->getResponseAuth('PUT', "http://clube-backend/api/autores/atualizar/{$autor['saida']['autorId']}", [
            'form_params' => [
                'dados[nome_autor]' => $autor['entrada']['nome_autor'] . " alterado"
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $outAutor = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $outAutor);

        return $autor;
    }

    public function testAtualizarAutorInexistente()
    {
        $response = $this->http->getResponseAuth('PUT', "http://clube-backend/api/autores/atualizar/999999999", [
            'form_params' => [
                'dados[nome_autor]' => "Lorem Ipsum"
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $outAutor = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertIsArray($outAutor);

        $this->assertArrayHasKey('message', $outAutor);

        $this->assertSame($outAutor['message'], MessageHelper::fmtMsgConst('ERR_AUTOR_NAO_ENCONTRADO'));
    }

    /**
     * @depends testAtualizarAutor
     */
    public function testDeletarAutor($autor)
    {
        $response = $this->http->getResponseAuth('DELETE', "http://clube-backend/api/autores/deletar/{$autor['saida']['autorId']}");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $autor = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $autor);
    }

    public function testDeletarAutorInexistente()
    {
        $response = $this->http->getResponseAuth('DELETE', "http://clube-backend/api/autores/deletar/9999999999");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $autor = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $autor);
    }

}