<?php

use ClubeLivro\HttpHelper;
use helpers\MessageHelper;
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

    public function testAdicionarUsuarioCorreto()
    {
        $faker = Faker\Factory::create('pt_BR');
        $usuario['entrada'] = [
            'email' => $faker->email,
            'nome' => $faker->name,
            'senha' => $faker->password,
            'cpf' => $faker->cpf(false),
            'nascimento' => '2000-01-01',
            'sexo' => 'M',
            'apelido' => $faker->userName
        ];
        $response = $this->http->getResponseAuth('POST', 'http://clube-backend/api/usuarios/adicionar', [
            'form_params' => [
                'dados[email]' => $usuario['entrada']['email'],
                'dados[nome]' => $usuario['entrada']['nome'],
                'dados[senha]' => $usuario['entrada']['senha'],
                'dados[cpf]' => $usuario['entrada']['cpf'],
                'dados[nascimento]' => $usuario['entrada']['nascimento'],
                'dados[sexo]' => $usuario['entrada']['sexo'],
                'dados[apelido]' => $usuario['entrada']['apelido']
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $usuario['saida'] = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $usuario['saida']);
        $this->assertArrayHasKey('usuarioId', $usuario['saida']);

        return $usuario;
    }
    
    /**
     * @depends testAdicionarUsuarioCorreto
     */
    public function testBuscarUsuarioExistente($inUsuario)
    {
        $response = $this->http->getResponseAuth('GET', "http://clube-backend/api/usuarios/buscar/{$inUsuario['saida']['usuarioId']}");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $outUsuario = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertIsArray($outUsuario);

        if (count($outUsuario) > 0) {
            $this->assertArrayHasKey('uid', $outUsuario[0]);
            $this->assertArrayHasKey('email', $outUsuario[0]);
            $this->assertArrayHasKey('nome', $outUsuario[0]);

            $this->assertSame($outUsuario[0]['uid'], $inUsuario['saida']['usuarioId']);
            $this->assertSame($outUsuario[0]['email'], $inUsuario['entrada']['email']);
        }

        // return $assuntoId;
    }
    
    /**
     * @depends testAdicionarUsuarioCorreto
     */
    public function testAlterarUsuarioCorreto($inUsuario)
    {
        $faker = Faker\Factory::create('pt_BR');
        $response = $this->http->getResponseAuth('PUT', "http://clube-backend/api/usuarios/atualizar/{$inUsuario['saida']['usuarioId']}", [
            'form_params' => [
                'dados[senha]' => $faker->password
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $outUsuario = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertArrayHasKey('message', $outUsuario);

        $this->assertSame($outUsuario['message'], MessageHelper::fmtMsgConst('MSG_USUARIO_ATUALIZADO_SUCESSO', false));
    }
 

    /** Testes de erros */
    public function testBuscarUsuarioInexistente()
    {
        $response = $this->http->getResponseAuth('GET', "http://clube-backend/api/usuarios/buscar/999999");
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody()->__toString());
        $outUsuario = (array) json_decode($response->getBody()->__toString(), true);

        $this->assertIsArray($outUsuario);
    }
}