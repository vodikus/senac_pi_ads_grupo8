<?php
include_once 'includes/BaseController.php';
include_once 'models/LivroModel.php';
include_once 'models/LivroAutorModel.php';
include_once 'models/LivroAssuntoModel.php';
include_once 'models/LivroAvaliacaoModel.php';
include_once 'models/FavoritosModel.php';

class LivroController extends BaseController
{
    public function __construct() {
        parent::__construct();
    }
    public function processarRequisicao($metodo='', $params=[]) {
        switch ($metodo) {
            case 'GET':
                switch ($params['acao']) {
                    case 'listar':
                        $this->listar();
                        break;
                    case 'buscar':
                        $this->buscar($params['param1']);
                        break;
                    default:
                        $this->httpResponse(501,'Ação Indisponível');
                        break;
                }
                break;
                case 'POST':
                    $dados = $this->pegarArrayPost();
                    switch ($params['acao']) {
                        case 'adicionar':
                            if ( $this->isAuth() ) {
                                $this->adicionar($dados);
                            } else {
                                $this->httpResponse(401,'Não autorizado');
                            }
                            break;
                        case 'avaliar':
                            if ( $this->isAuth() ) {
                                $this->avaliarLivro($this->getFieldFromToken('uid'), $dados);
                            } else {
                                $this->httpResponse(401,'Não autorizado');
                            }
                            break;
                        case 'vincularAutor':
                            if ( $this->isAuth() ) {
                                $this->vincularAutor($dados);
                            } else {
                                $this->httpResponse(401,'Não autorizado');
                            }
                            break;
                        case 'desvincularAutor':
                            if ( $this->isAuth() ) {
                                $this->desvincularAutor($dados);
                            } else {
                                $this->httpResponse(401,'Não autorizado');
                            }
                            break;
                        case 'vincularAssunto':
                            if ( $this->isAuth() ) {
                                $this->vincularAssunto($dados);
                            } else {
                                $this->httpResponse(401,'Não autorizado');
                            }
                            break;
                        case 'desvincularAssunto':
                            if ( $this->isAuth() ) {
                                $this->desvincularAssunto($dados);
                            } else {
                                $this->httpResponse(401,'Não autorizado');
                            }
                            break;
                        case 'adicionarFavorito':
                            if ( $this->isAuth() ) {
                                $this->adicionarFavorito($this->getFieldFromToken('uid'), $dados);
                            } else {
                                $this->httpResponse(401,'Não autorizado');
                            }
                            break;
                        case 'removerFavorito':
                            if ( $this->isAuth() ) {
                                $this->removerFavorito($this->getFieldFromToken('uid'), $dados);
                            } else {
                                $this->httpResponse(401,'Não autorizado');
                            }
                            break;
                        default:
                            $this->httpResponse(501,'Ação Indisponível');
                            break;
                    }
                break;
            case 'PUT':
                switch ($params['acao']) {
                    case 'atualizar':
                        $dados = $this->pegarArrayPut();
                        if ( $this->isAuth() ) {
                            $this->atualizar($params['param1'], $dados);
                        } else {
                            $this->httpResponse(401,'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501,'Ação Indisponível');
                        break;
                }                
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ( $this->isAuth() ) {
                            $this->deletar($params['param1']);
                        } else {
                            $this->httpResponse(401,'Não autorizado');
                        }
                        break;
                    default:
                        $this->httpResponse(501,'Ação Indisponível');
                        break;
                }                
                break;
            default:
                $this->httpResponse(405,'Method Not Allowed');
                break;
        }      
    }

    public function listar()
    {
        try {
            $livroModel = new LivroModel();
            $arrLivros = $livroModel->listarLivros();
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpResponse(500,$e->getMessage());
        }
        $this->montarSaidaOk($responseData);
    }

    public function buscar($lid = 0)
    {
        try {
            $livroModel = new LivroModel();
            $arrLivros = $livroModel->buscarLivro($lid);
            $responseData = json_encode($arrLivros);
        } catch (Exception $e) {
            $this->httpResponse(500,$e->getMessage());
        }
        $this->montarSaidaOk($responseData);
    }

    public function adicionar($dados)
    {       
        try {
            $livroModel = new LivroModel();
            $livroModel->adicionarLivro($dados);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'livro_isbn_uk')) {
                        $this->httpResponse(200,'Já existe um com este ISBN');
                    } else {
                        $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    }
                    break;

                default:
                    $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    break;
            }
        } finally {
            $this->httpResponse(200,'Livro cadastrado com sucesso.');
        }
    }

    public function deletar($lid)
    {
        try {
            if ( is_numeric($lid) ) {
                $livroModel = new LivroModel();            
                if ( $livroModel->deletarLivro($lid) > 0 ) {
                    $this->httpResponse(200,'Livro deletado com sucesso.');
                } else {
                    $this->httpResponse(200,'Livro não encontrado');
                }
            } else {
                $this->httpResponse(200,'Identificador inválido');
            }
        } catch (Error $e) {
            $this->httpResponse(500,'Erro');
        }
    }

    public function atualizar($lid, $dados)
    {       
        try {
            if ( is_numeric($lid) ) {
                $livroModel = new LivroModel();            
                if ( $livroModel->atualizarLivro($lid, $dados) <= 0) {
                    $this->httpResponse(200,'Livro não localizado');
                }
            } else {
                $this->httpResponse(200,'Identificador inválido');
            }            
        } catch (Exception $e) {
            $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
        } finally {
            $this->httpResponse(200,'Livro atualizado com sucesso.');
        }
    }

    public function vincularAutor($dados)
    {       
        try {
            $livroAutorModel = new LivroAutorModel();

            if ( $livroAutorModel->adicionarLivroAutor($dados) <= 0 ) {
                $this->httpResponse(200,'Livro / Autor não encontrado');
            }
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'PRIMARY')) {
                        $this->httpResponse(200,'Este Autor já está vinculado a este Livro.');
                    } else {
                        $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    }
                    break;

                default:
                    $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    break;
            }
        } finally {
            $this->httpResponse(200,'Autor vinculado ao Livro com sucesso.');
        }
    }

    public function desvincularAutor($dados)
    {       
        try {
            $livroAutorModel = new LivroAutorModel();
            if ( $livroAutorModel->deletarLivroAutor($dados) <= 0 ) {
                $this->httpResponse(200,'Livro / Autor não encontrado');
            }
        } catch (Exception $e) {
            $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
        } finally {
            $this->httpResponse(200,'Autor desvinculado do livro com sucesso.');
        }
    }

    public function vincularAssunto($dados)
    {       
        try {
            $livroAssuntoModel = new LivroAssuntoModel();
            if ( $livroAssuntoModel->adicionarLivroAssunto($dados) <= 0 ) {
                $this->httpResponse(200,'Livro / Assunto não encontrado');
            }
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'PRIMARY')) {
                        $this->httpResponse(200,'Este Assunto já está vinculado a este Livro.');
                    } else {
                        $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    }
                    break;

                default:
                    $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    break;
            }
        } finally {
            $this->httpResponse(200,'Assunto vinculado ao Livro com sucesso.');
        }
    }

    public function desvincularAssunto($dados)
    {       
        try {
            $livroAssuntoModel = new LivroAssuntoModel();
            if ( $livroAssuntoModel->deletarLivroAssunto($dados) <= 0 ) {
                $this->httpResponse(200,'Livro / Assunto não encontrado');
            }
        } catch (Exception $e) {
            $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
        } finally {
            $this->httpResponse(200,'Assunto desvinculado do livro com sucesso.');
        }
    }

    public function avaliarLivro($uid = 0, $dados)
    {       
        try {
            $livroAvaliacaoModel = new LivroAvaliacaoModel();
            if ( $livroAvaliacaoModel->adicionarLivroAvaliacao($uid, $dados) <= 0 ) {
                $this->httpResponse(200,'Livro não encontrado');
            }
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if ( stripos($e->getMessage(),'PRIMARY') ) {
                        $this->httpResponse(200,'Este livro já está na lista de Favoritos.');
                    } else {
                        $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    }
                    break;

                default:
                    $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    break;
            }
        } finally {
            $this->httpResponse(200,'Livro avaliado com sucesso.');
        }
    }

    public function adicionarFavorito($uid = 0, $dados)
    {       
        try {
            $favoritosModel = new FavoritosModel();
            if ( $favoritosModel->adicionarFavorito($uid, $dados) <= 0 ) {
                $this->httpResponse(200,'Livro não encontrado');
            }
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if ( stripos($e->getMessage(),'PRIMARY') ) {
                        $this->httpResponse(200,'Este livro já foi adicionado a lista de favoritos.');
                    } else {
                        $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    }
                    break;

                default:
                    $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
                    break;
            }
        } finally {
            $this->httpResponse(200,'Livro adicionado a lista de Favoritos com sucesso.');
        }
    }

    public function removerFavorito($uid = 0, $dados)
    {       
        try {
            $favoritosModel = new FavoritosModel();
            if ( $favoritosModel->removerFavorito($uid, $dados) <= 0 ) {
                $this->httpResponse(200,'Livro não encontrado');
            }
        } catch (Exception $e) {
            $this->httpResponse(500,"Erro: " . $e->getCode() . " | " . $e->getMessage());
        } finally {
            $this->httpResponse(200,'Livro removido da lista de Favoritos com sucesso.');
        }
    }
}