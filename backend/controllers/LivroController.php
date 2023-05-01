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
                switch ($params['acao']) {
                    case 'listar':
                        $this->listar();
                        break;
                    case 'buscar-por-id':
                        $this->buscarId($params['param1']);
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

    public function buscarIsbn($lid = 0)
    {
        try {
            if (is_numeric($lid)) {
                $livroModel = new LivroModel();
                $arrLivros = (array) $livroModel->buscarLivroPorIsbn($lid);
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
                        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_AUTOR_VINCULO_NAO_ENCONTRADO',$dado));
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