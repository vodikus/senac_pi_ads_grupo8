<?php
include_once 'includes/BaseController.php';
include_once 'models/LivroModel.php';
include_once 'models/LivroAutorModel.php';

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
}