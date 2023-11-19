<?php

include_once 'models/LivroModel.php';
include_once 'models/LivroAutorModel.php';
include_once 'models/LivroAssuntoModel.php';
include_once 'models/LivroAvaliacaoModel.php';
include_once 'models/FavoritosModel.php';
include_once 'models/UsuarioLivroModel.php';
include_once 'models/ImagemModel.php';
include_once 'services/OpenLibraryService.php';
require_once 'helpers/FileHelper.php';

use helpers\MessageHelper;
use helpers\FileHelper;

class LivroController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function processarRequisicao($metodo = '', $params = [])
    {
        switch ($metodo) {
            case 'GET':
                $dados = $this->pegarArrayJson();
                switch ($params['acao']) {
                    case 'listar':
                        $this->listar($params['params']);
                        break;
                    case 'listar-disponiveis':
                        $this->listarLivrosDisponiveis($params['params']);
                        break;
                    case 'buscar-por-id':
                        $this->buscarId($params['params']);
                        break;
                    case 'buscar-por-isbn':
                        $this->buscarIsbn($params['params']);
                        break;
                    case 'buscar-por-assunto':
                        $this->buscarAssunto($params['params']);
                        break;
                    case 'buscar-por-autor':
                        $this->buscarAutor($params['params']);
                        break;
                    case 'buscar-por-titulo':
                        $this->buscarTitulo($params['params']);
                        break;
                    case 'buscar-por-usuario':
                        $this->buscarUsuario($params['params']);
                        break;
                    case 'busca-completa':
                        $this->buscaCompleta($params['params']);
                        break;
                    case 'meus-livros':
                        $entrada = sprintf("?uid=%s", $this->getFieldFromToken('uid'));
                        $this->buscarUsuario($entrada);
                        break;
                    case 'favoritos':
                        if ($this->isAuth()) {
                            $this->listarFavoritos($this->getFieldFromToken('uid'), $params['params']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'POST':
                $dados = $this->pegarArrayJson();
                switch ($params['acao']) {
                    case 'adicionar':
                        if ($this->isAuth()) {
                            $this->adicionar($dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'cadastrar':
                        if ($this->isAuth()) {
                            $this->cadastrar($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'avaliar':
                        if ($this->isAuth()) {
                            $this->avaliarLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'vincularAutor':
                        if ($this->isAuth()) {
                            $this->vincularAutor($dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularAutor':
                        if ($this->isAuth()) {
                            $this->desvincularAutor($dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'vincularAssunto':
                        if ($this->isAuth()) {
                            $this->vincularAssunto($dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularAssunto':
                        if ($this->isAuth()) {
                            $this->desvincularAssunto($dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'adicionarFavorito':
                        if ($this->isAuth()) {
                            $this->adicionarFavorito($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'removerFavorito':
                        if ($this->isAuth()) {
                            $this->removerFavorito($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'atualizarStatusLivro':
                        if ($this->isAuth()) {
                            $this->atualizarStatusLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'enviarCapa':
                        if ($this->isAuth()) {
                            $this->enviarCapa($this->getFieldFromToken('uid'), $params['params'], $_FILES);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'PUT':
                switch ($params['acao']) {
                    case 'atualizar':
                        $dados = $this->pegarArrayJson();
                        if ($this->isAuth()) {
                            $this->atualizar($params['level1'], $dados);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ($this->isAuth()) {
                            $this->deletar($params['level1']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'removerCapa':
                        if ($this->isAuth()) {
                            $this->removerCapa($params['params']);
                        } else {
                            $this->httpRawResponse(401, MessageHelper::fmtMsgConstJson('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpRawResponse(501, MessageHelper::fmtMsgConstJson('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            default:
                $this->httpRawResponse(405, MessageHelper::fmtMsgConstJson('ERR_METODO_NAO_PERMITIDO'));
                break;
        }
    }

    /**
     * @apiDefine ERR_LIVRO_PADRAO
     *
     * @apiError (Erro 4xx) 9202 Já existe um livro com este ISBN.
     *
     */

    /**
     * @apiDefine SAIDA_LISTA_LIVROS
     *
     * @apiSuccess {Object[]} livros Lista de livros
     * @apiSuccess {Number} livros.lid ID do assunto
     * @apiSuccess {String} livros.titulo Titulo do livro
     * @apiSuccess {String} livros.descricao Descrição
     * @apiSuccess {Number} livros.avaliacao Nota da avaliação
     * @apiSuccess {String} livros.capa Caminho da imagem da capa
     * @apiSuccess {String} livros.isbn ISBN
     * @apiSuccess {String} livros.status Status
     * @apiSuccess {Timestamp} livros.dh_atualizacao  Data/Hora de atualização
     * @apiSuccess {String} livros.autores Lista de autores
     * @apiSuccess {String} livros.assuntos Lista de assuntos
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "lid": "6",
     *             "titulo": "O Senhor dos Anéis - A sociedade do Anel - Volume 1",
     *             "descricao": "Um grupo destemido de hobbits vão levar o Anel até a casa do chapéu, também conhecido como Mordor.",
     *             "avaliacao": "7.5",
     *             "capa": "",
     *             "isbn": "1234",
     *             "status": "A",
     *             "dh_atualizacao": "2023-04-15 18:58:38",
     *             "autores": "J. R. R. Tolkien",
     *             "assuntos": "Aventura, Fantasia"
     *         }
     *     ]
     *
     */


    /**
     * @api {get} /livros/listar/ Listar Livros
     * @apiName Listar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     *
     */
    public function listar($entrada)
    {
        try {
            parse_str(substr($entrada, 1), $params);
            $ordem = (array_key_exists('ordem', $params)) ? $params['ordem'] : '';
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->listarLivros($ordem);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-id/:id Buscar Livros por id
     * @apiName Buscar por id
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiParam {String} id Id do livro.
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     *
     */

    public function buscarId($entrada = 0)
    {
        try {
            parse_str(substr($entrada, 1), $params);
            $lid = (array_key_exists('id', $params)) ? $params['id'] : '';
            if (is_numeric($lid)) {
                $livroModel = new LivroModel();
                $arrLivros = (array) $livroModel->buscarLivroPorId($lid);
                $responseData = json_encode($arrLivros);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-isbn/:isbn Buscar Livros por ISBN
     * @apiName Buscar por ISBN
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiParam {String} isbn ISBN do livro.
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     *
     */
    public function buscarIsbn($dados)
    {
        try {
            parse_str(substr($dados, 1), $params);
            $isbn = (array_key_exists('isbn', $params)) ? $params['isbn'] : '';
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->buscarLivroPorIsbn(strval($isbn));
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-assunto/:nome_assunto Buscar Livros por Assunto
     * @apiName Buscar por Assunto
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiParam  {String} nome_assunto Assunto do livro.
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     *
     */
    public function buscarAssunto($dados)
    {
        try {
            parse_str(substr($dados, 1), $params);
            $assunto = (array_key_exists('nome_assunto', $params)) ? $params['nome_assunto'] : '';
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->buscarLivroPorAssunto($assunto);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-autor/:nome_autor Buscar Livros por Autor
     * @apiName Buscar por Autor
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiParam  {String} nome_autor Autor do livro.
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     *
     */
    public function buscarAutor($dados)
    {
        try {
            parse_str(substr($dados, 1), $params);
            $autor = (array_key_exists('nome_autor', $params)) ? $params['nome_autor'] : '';
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->buscarLivroPorAutor($autor);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-titulo/:titulo Buscar Livros por Titulo
     * @apiName Buscar por Titulo
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiParam  {String} titulo Titulo do livro.
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     *
     */
    public function buscarTitulo($dados)
    {
        try {
            parse_str(substr($dados, 1), $params);
            $titulo = (array_key_exists('titulo', $params)) ? $params['titulo'] : '';
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->buscarLivroPorTitulo($titulo);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-usuario/:id Buscar Livros por Usuario
     * @apiName Buscar por Usuário
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Id do usuário
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     *
     */
    public function buscarUsuario($entrada = 0)
    {
        try {
            parse_str(substr($entrada, 1), $params);
            $uid = (array_key_exists('uid', $params)) ? $params['uid'] : 0;
            $lid = (array_key_exists('lid', $params)) ? $params['lid'] : 0;
            if (is_numeric($uid) && is_numeric($lid)) {
                $livroModel = new LivroModel();
                $arrLivros = (array) $livroModel->buscarLivroPorUsuario($uid, $lid);
                $responseData = json_encode($arrLivros);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {post} /livros/adicionar/ Adicionar Livro
     * @apiName Adicionar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {String} titulo Titulo do Livro.
     * @apiBody {String} descricao Descricao do Livro.
     * @apiBody {String} isbn ISBN do Livro.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9202",
     *         "mensagem": "Já existe um livro com este ISBN",
     *         "detalhe": ""
     *     }
     */
    public function adicionar($dados)
    {
        try {
            $livroModel = new LivroModel();
            $livroId = $livroModel->adicionarLivro($dados);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_LIVRO_CADASTRO_SUCESSO', ['livroId' => $livroId]));
    }

    /**
     * @api {post} /livros/cadastrar/ Cadastrar Livro
     * @apiName Cadastrar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {String} titulo Titulo do Livro.
     * @apiBody {String} descricao Descricao do Livro.
     * @apiBody {String} isbn ISBN do Livro.
     * @apiBody {Autor} autor[] Autores do Livro.
     * @apiBody {Assunto} assunto[] Assuntos do Livro.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9202",
     *         "mensagem": "Já existe um livro com este ISBN",
     *         "detalhe": ""
     *     }
     */
    public function cadastrar($uid, $dados)
    {
        try {
            // Separar pedaços para não quebrar validações dos modelos
            $livro = $dados;
            unset($livro['autores']);
            unset($livro['assuntos']);

            // Adicionar livro
            $livroModel = new LivroModel();
            $livroId = $livroModel->adicionarLivro($livro);

            if ($livroId > 0) {
                // Adiciona capa ao livro
                $capaUrl = 'https://covers.openlibrary.org/b/isbn/'.$livro['isbn'].'-S.jpg';
                $capaTemp = tempnam(sys_get_temp_dir(), $livro['isbn']);
                FileHelper::downloadImagem($capaUrl,  $capaTemp);
                if (filesize($capaTemp) > 999) {
                    $imagemModel = new ImagemModel(); 
                    $imagemModel->salvaCapaLivro($livroId,['imagem' => ['tmp_name' => $capaTemp]]);
                }

                // Autores
                if (array_key_exists('autores', $dados)) {
                    $livroAutorModel = new LivroAutorModel();
                    foreach ($dados['autores'] as $key => $value) {
                        if (!array_key_exists('aid', $value)) {
                            // Cadastrar autor
                            $autorModel = new AutorModel();
                            $autorId = $autorModel->adicionarAutor($value);
                        } else {
                            $autorId = $value['aid'];
                        }
                        // Vincula autor ao livro
                        if ($autorId > 0)
                            $livroAutorModel->adicionarLivroAutor(['lid' => $livroId, 'aid' => $autorId]);
                    }
                }

                // Assuntos
                if (array_key_exists('assuntos', $dados)) {
                    $livroAssuntoModel = new LivroAssuntoModel();
                    foreach ($dados['assuntos'] as $key => $value) {
                        if (!array_key_exists('iid', $value)) {
                            // Cadastrar assunto
                            $assuntoModel = new AssuntoModel();
                            $assuntoId = $assuntoModel->adicionarAssunto($value);
                        } else {
                            $assuntoId = $value['iid'];
                        }
                        // Vincula assunto ao livro
                        if ($assuntoId > 0)
                            $livroAssuntoModel->adicionarLivroAssunto(['lid' => $livroId, 'iid' => $assuntoId]);
                    }
                }

                // Vincula usuário ao livro e torna o livro disponível
                $usuarioLivroModel = new UsuarioLivroModel();
                $usuarioLivroModel->adicionarUsuarioLivro($uid, ['lid'=>$livroId]);
                $usuarioLivroModel->atualizarUsuarioLivro($uid, ['lid'=>$livroId, 'status'=>'D']);
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_LIVRO_CADASTRO_SUCESSO', ['livroId' => $livroId]));
    }

    /**
     * @api {delete} /livros/deletar/:id Deletar Livro
     * @apiName Deletar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id ID único do livro.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function deletar($lid)
    {
        try {
            if (is_numeric($lid)) {
                $livroModel = new LivroModel();
                if ($livroModel->deletarLivro($lid) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_LIVRO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Error $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_LIVRO_DELETADO_SUCESSO'));
    }

    /**
     * @api {put} /livros/atualizar/:id Atualizar Livro
     * @apiName Atualizar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Id do Livro
     * @apiBody {String} titulo Titulo do Livro.
     * @apiBody {String} descricao Descricao do Livro.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function atualizar($lid, $dados)
    {
        try {
            if (is_numeric($lid)) {
                $livroModel = new LivroModel();
                if ($livroModel->atualizarLivro($lid, $dados) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_LIVRO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_LIVRO_ATUALIZADO_SUCESSO'));
    }

    /**
     * @api {post} /livros/vincularAutor/ Vincular Autor
     * @apiName Vincular Autor
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {Object[]} autorlivro AutorLivro.
     * @apiBody {Number} autorlivro.lid Id do Livro.
     * @apiBody {Number} autorlivro.aid Id do Autor.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function vincularAutor($dados)
    {
        try {
            $livroAutorModel = new LivroAutorModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    $livroAutorModel->adicionarLivroAutor($dado);
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AUTOR_LIVRO_VINCULADO_SUCESSO'));
    }

    /**
     * @api {post} /livros/desvincularAutor/ Desvincular Autor
     * @apiName Desvincular Autor
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {Object[]} autorlivro AutorLivro.
     * @apiBody {Number} autorlivro.lid Id do Livro.
     * @apiBody {Number} autorlivro.aid Id do Autor.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function desvincularAutor($dados)
    {
        try {
            $livroAutorModel = new LivroAutorModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    if ($livroAutorModel->deletarLivroAutor($dado) == 0) {
                        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_AUTOR_VINCULO_NAO_ENCONTRADO', $dado));
                    }
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AUTOR_LIVRO_DESVINCULADO_SUCESSO'));
    }

    /**
     * @api {post} /livros/vincularAssunto/ Vincular Assunto
     * @apiName Vincular Assunto
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {Object[]} assuntolivro AssuntoLivro.
     * @apiBody {Number} assuntolivro.lid Id do Livro.
     * @apiBody {Number} assuntolivro.iid Id do Assunto.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function vincularAssunto($dados)
    {
        try {
            $livroAssuntoModel = new LivroAssuntoModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    $livroAssuntoModel->adicionarLivroAssunto($dado);
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_LIVRO_ASSUNTO_VINCULADO_SUCESSO'));
    }

    /**
     * @api {post} /livros/desvincularAssunto/ Desvincular Assunto
     * @apiName Desvincular Assunto
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {Object[]} assuntolivro AssuntoLivro.
     * @apiBody {Number} assuntolivro.lid Id do Livro.
     * @apiBody {Number} assuntolivro.iid Id do Assunto.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function desvincularAssunto($dados)
    {
        try {
            $livroAssuntoModel = new LivroAssuntoModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    if ($livroAssuntoModel->deletarLivroAssunto($dado) == 0) {
                        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ASSUNTO_VINCULO_NAO_ENCONTRADO', StringHelper::formataArrayChaveValor($dado)));
                    }
                }
            } else {
                $this->httpRawResponse(500, MessageHelper::fmtMsgConstJson('ERR_JSON_INVALIDO'));
            }
        } catch (CLException | CLConstException $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_LIVRO_ASSUNTO_DESVINCULADO_SUCESSO'));
    }

    /**
     * @api {post} /livros/avaliar/ Avaliar Livro
     * @apiName Avaliar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {Number} lid Id do Livro.
     * @apiBody {Number} nota Nota dada ao Livro.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function avaliarLivro($uid = 0, $dados)
    {
        try {
            $livroAvaliacaoModel = new LivroAvaliacaoModel();
            if ($livroAvaliacaoModel->adicionarLivroAvaliacao($uid, $dados) == 0) {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_LIVRO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_LIVRO_AVALIADO_SUCESSO'));
    }

    /**
     * @api {post} /livros/adicionarFavorito/ Favoritar Livro
     * @apiName Favoritar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {Number} lid Id do Livro.
     * @apiBody {Number} uid_dono Id do Usuário dono do Livro.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function adicionarFavorito($uid = 0, $dados)
    {
        try {
            $favoritosModel = new FavoritosModel();
            if ($favoritosModel->adicionarFavorito($uid, $dados) == 0) {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_LIVRO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_LIVRO_FAVORITO_SUCESSO'));
    }

    /**
     * @api {post} /livros/removerFavorito/ Desfavoritar Livro
     * @apiName Desfavoritar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {Number} lid Id do Livro.
     * @apiBody {Number} uid_dono Id do Usuário dono do Livro.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_LIVRO_PADRAO
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function removerFavorito($uid = 0, $dados)
    {
        try {
            $favoritosModel = new FavoritosModel();
            if ($favoritosModel->removerFavorito($uid, $dados) == 0) {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_LIVRO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_LIVRO_DESFAVORITO_SUCESSO'));
    }

    /**
     * @api {post} /livros/atualizarStatusLivro/ Atualizar Status Livro
     * @apiName Atualizar Status Livro
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {Object[]} usuariolivro UsuarioLivro.
     * @apiBody {Number} usuariolivro.uid Id do Usuário.
     * @apiBody {Number} usuariolivro.iid Id do Livro.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9200",
     *         "mensagem": "Livro não encontrado",
     *         "detalhe": ""
     *     }
     */
    public function atualizarStatusLivro($uid, $dados)
    {
        try {
            $usuarioLivroModel = new UsuarioLivroModel();
            if (is_array($dados) && count($dados) > 0) {
                foreach ($dados as $dado) {
                    if ($usuarioLivroModel->atualizarUsuarioLivro($uid, $dado) == 0) {
                        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_USUARIO_LIVRO_VINCULO_NAO_ENCONTRADO'));
                    }
                }
            } else {
                $this->httpResponse(500, MessageHelper::fmtMsgConst('ERR_JSON_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(200, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_USUARIO_LIVRO_STATUS_SUCESSO'));
    }

    /**
     * @api {get} /livros/listar/ Listar Livros
     * @apiName Listar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     *
     */
    public function listarLivrosDisponiveis($entrada)
    {
        try {
            parse_str(substr($entrada, 1), $params);
            $ordem = (array_key_exists('ordem', $params)) ? $params['ordem'] : '';
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->buscarLivrosDisponiveis($ordem);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/favoritos/ Listar Favoritos
     * @apiName Listar Favoritos
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     *
     */
    public function listarFavoritos($uid, $entrada)
    {
        try {
            parse_str(substr($entrada, 1), $params);
            $ordem = (array_key_exists('ordem', $params)) ? $params['ordem'] : '';
            $favoritoModel = new FavoritosModel();
            $arrLivros = (array) $favoritoModel->listarFavoritos($uid, $ordem);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function enviarCapa($uid, $entrada = 0, $files = 0)
    {
        try {
            parse_str(substr($entrada, 1), $params);
            $id = (array_key_exists('id', $params)) ? $params['id'] : '';
            if ((is_numeric($id)) && (FileHelper::validaImagem($files))) {
                $imagemModel = new ImagemModel();
                $imagemModel->salvaCapaLivro($id, $files);
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('CAPA_LIVRO_SALVA_SUCESSO'));
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
    }

    public function removerCapa($entrada = 0)
    {
        try {
            parse_str(substr($entrada, 1), $params);
            $id = (array_key_exists('id', $params)) ? $params['id'] : '';
            if (is_numeric($id)) {
                $imagemModel = new ImagemModel();
                $imagemModel->removeCapaLivro($id);
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('CAPA_LIVRO_REMOVIDA_SUCESSO'));
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
    }
    public function buscaCompleta($entrada)
    {
        try {
            parse_str(substr($entrada, 1), $params);
            $scope = (array_key_exists('scope', $params)) ? $params['scope'] : '';
            $key = (array_key_exists('key', $params)) ? $params['key'] : '';
            $value = (array_key_exists('value', $params)) ? $params['value'] : '';

            if (!empty($scope) && !empty($key) && !empty($value)) {
                $ols = new OpenLibraryService();
                switch ($scope) {
                    case 'search':
                        $arrLivros = $ols->requestSearch($key, $value);
                        break;
                    case 'books':
                        $arrLivros = $ols->requestBooks($key, $value);
                        break;

                }
                $responseData = json_encode($arrLivros);
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }

        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }
}