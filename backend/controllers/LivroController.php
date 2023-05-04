<?php
use Helpers\MessageHelper;

include_once 'models/LivroModel.php';
include_once 'models/LivroAutorModel.php';
include_once 'models/LivroAssuntoModel.php';
include_once 'models/LivroAvaliacaoModel.php';
include_once 'models/FavoritosModel.php';

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
                        $this->listar();
                        break;
                    case 'buscar-por-id':
                        $this->buscarId($params['param1']);
                        break;
                    case 'buscar-por-isbn':
                        $this->buscarIsbn($dados);
                        break;
                    case 'buscar-por-assunto':
                        $this->buscarAssunto($dados);
                        break;
                    case 'buscar-por-autor':
                        $this->buscarAutor($dados);
                        break;
                    case 'buscar-por-titulo':
                        $this->buscarTitulo($dados);
                        break;
                    case 'buscar-por-usuario':
                        $this->buscarUsuario($params['param1']);
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
                            $this->atualizar($params['param1'], $dados);
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
                            $this->deletar($params['param1']);
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
     * @api {get} /livros/listar/ Lista os livros
     * @apiName Listar
     * @apiGroup Livros
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA_LIVROS
     * @apiUse ERR_GENERICOS
     * 
     */
    public function listar()
    {
        try {
            $livroModel = new LivroModel();
            $arrLivros = (array) $livroModel->listarLivros();
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
     * @api {get} /livros/buscar-por-isbn/ Busca livros pelo ISBN
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
     * @api {get} /livros/buscar-por-assunto/ Busca livros pelo Assunto
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
            $livroModel = new LivroModel();
            $entrada = (array_key_exists('nome_assunto', $dados)) ? $dados['nome_assunto'] : '';
            $arrLivros = (array) $livroModel->buscarLivroPorAssunto($entrada);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-autor/ Busca livros pelo Autor
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
    public function buscarAutor($dados)
    {
        try {
            $livroModel = new LivroModel();
            $entrada = (array_key_exists('nome_autor', $dados)) ? $dados['nome_autor'] : '';
            $arrLivros = (array) $livroModel->buscarLivroPorAutor($entrada);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /livros/buscar-por-titulo/ Busca livros pelo Titulo
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
     * @api {get} /livros/buscar-por-usuario/:id Busca livros pelo Usuario
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
}