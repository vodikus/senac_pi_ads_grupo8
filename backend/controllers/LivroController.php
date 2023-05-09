<?php
use Helpers\MessageHelper;

include_once 'models/LivroModel.php';
include_once 'models/LivroAutorModel.php';
include_once 'models/LivroAssuntoModel.php';
include_once 'models/LivroAvaliacaoModel.php';
include_once 'models/FavoritosModel.php';
include_once 'models/UsuarioLivroModel.php';

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
                        $this->buscarId($params['level1']);
                        break;
                    case 'buscar-por-isbn':
                        $this->buscarIsbn($dados);
                        break;
                    case 'buscar-por-assunto':
                        $this->buscarAssunto($params['params']);
                        break;
                    case 'buscar-por-autor':
                        $this->buscarAutor($dados);
                        break;
                    case 'buscar-por-titulo':
                        $this->buscarTitulo($dados);
                        break;
                    case 'buscar-por-usuario':
                        $this->buscarUsuario($params['level1']);
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
            parse_str(substr($entrada,1), $params);            
            $ordem = (array_key_exists('ordem', $params)) ? $params['ordem'] : '';
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->listarLivros($ordem);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function buscarId($lid = 0)
    {
        try {
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
     * @api {get} /livros/buscar-por-isbn/ Buscar Livros por ISBN
     * @apiName Buscar por ISBN
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {String} isbn ISBN do livro.
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     * 
     */
    public function buscarIsbn($dados)
    {
        try {
            $livroModel = new LivroModel();
            $entrada = (array_key_exists('isbn', $dados)) ? $dados['isbn'] : '';
            $arrLivros = (array) $livroModel->buscarLivroPorIsbn($entrada);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-assunto/ Buscar Livros por Assunto
     * @apiName Buscar por Assunto
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {String} nome_assunto Assunto do livro.
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     * 
     */
    public function buscarAssunto($dados)
    {
        try {
            parse_str(substr($dados,1), $params);
            $assunto = (array_key_exists('assunto', $params)) ? $params['assunto'] : '';
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->buscarLivroPorAssunto($assunto);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-autor/ Buscar Livros por Autor
     * @apiName Buscar por Autor
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {String} nome_autor Autor do livro.
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     * 
     */
    public function buscarAutor($autor)
    {
        try {
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->buscarLivroPorAutor($autor);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-titulo/ Buscar Livros por Titulo
     * @apiName Buscar por Titulo
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiBody {String} titulo Titulo do livro.
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     * 
     */
    public function buscarTitulo($dados)
    {
        try {
            $livroModel = new LivroModel();
            $entrada = (array_key_exists('titulo', $dados)) ? $dados['titulo'] : '';
            $arrLivros = (array) $livroModel->buscarLivroPorTitulo($entrada);
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
    public function buscarUsuario($uid = 0)
    {
        try {
            if (is_numeric($uid)) {
                $livroModel = new LivroModel();
                $arrLivros = (array) $livroModel->buscarLivroPorUsuario($uid);
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
            parse_str(substr($entrada,1), $params);            
            $ordem = (array_key_exists('ordem', $params)) ? $params['ordem'] : '';
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->buscarLivrosDisponiveis($ordem);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }    
}