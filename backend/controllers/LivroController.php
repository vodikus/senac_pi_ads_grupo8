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
                    case 'buscarPorId':
                        $this->buscar($params['param1']);
                        break;
                    default:
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'POST':
                $dados = $this->pegarArrayPost();
                switch ($params['acao']) {
                    case 'adicionar':
                        if ($this->isAuth()) {
                            $this->adicionar($dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'avaliar':
                        if ($this->isAuth()) {
                            $this->avaliarLivro($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'vincularAutor':
                        if ($this->isAuth()) {
                            $this->vincularAutor($dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularAutor':
                        if ($this->isAuth()) {
                            $this->desvincularAutor($dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'vincularAssunto':
                        if ($this->isAuth()) {
                            $this->vincularAssunto($dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'desvincularAssunto':
                        if ($this->isAuth()) {
                            $this->desvincularAssunto($dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'adicionarFavorito':
                        if ($this->isAuth()) {
                            $this->adicionarFavorito($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    case 'removerFavorito':
                        if ($this->isAuth()) {
                            $this->removerFavorito($this->getFieldFromToken('uid'), $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'PUT':
                switch ($params['acao']) {
                    case 'atualizar':
                        $dados = $this->pegarArrayPut();
                        if ($this->isAuth()) {
                            $this->atualizar($params['param1'], $dados);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            case 'DELETE':
                switch ($params['acao']) {
                    case 'deletar':
                        if ($this->isAuth()) {
                            $this->deletar($params['param1']);
                        } else {
                            $this->httpResponse(401, MessageHelper::fmtMsgConst('ERR_NAO_AUTORIZADO'));
                        }
                        break;
                    default:
                        $this->httpResponse(501, MessageHelper::fmtMsgConst('ERR_ACAO_INDISPONIVEL'));
                        break;
                }
                break;
            default:
                $this->httpResponse(405, MessageHelper::fmtMsgConst('ERR_METODO_NAO_PERMITIDO'));
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
            $this->httpResponse(500, MessageHelper::fmtException($e));
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
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
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
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }

    public function adicionar($dados)
    {
        try {
            $livroModel = new LivroModel();
            $livroId = $livroModel->adicionarLivro($dados);
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_LIVRO_CADASTRO_SUCESSO', false), ['livroId' => $livroId]);
    }

    public function deletar($lid)
    {
        try {
            if (is_numeric($lid)) {
                $livroModel = new LivroModel();
                if ($livroModel->deletarLivro($lid) == 0) {
                    $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_LIVRO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Error $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_LIVRO_DELETADO_SUCESSO', false));
    }

    public function atualizar($lid, $dados)
    {
        try {
            if (is_numeric($lid)) {
                $livroModel = new LivroModel();
                if ($livroModel->atualizarLivro($lid, $dados) == 0) {
                    $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_LIVRO_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_AUTOR_LIVRO_VINCULADO_SUCESSO', false));
    }

    public function vincularAutor($dados)
    {
        try {
            $livroAutorModel = new LivroAutorModel();
            $livroAutorModel->adicionarLivroAutor($dados);
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_AUTOR_LIVRO_VINCULADO_SUCESSO', false));
    }

    public function desvincularAutor($dados)
    {
        try {
            $livroAutorModel = new LivroAutorModel();
            if ($livroAutorModel->deletarLivroAutor($dados) == 0) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_AUTOR_VINCULO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_LIVRO_DESVINCULADO_SUCESSO', false));
    }

    public function vincularAssunto($dados)
    {
        try {
            $livroAssuntoModel = new LivroAssuntoModel();
            $livroAssuntoModel->adicionarLivroAssunto($dados);
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_LIVRO_ASSUNTO_VINCULADO_SUCESSO', false));
    }

    public function desvincularAssunto($dados)
    {
        try {
            $livroAssuntoModel = new LivroAssuntoModel();
            if ($livroAssuntoModel->deletarLivroAssunto($dados) == 0) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_ASSUNTO_VINCULO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_LIVRO_ASSUNTO_DESCINVULADO_SUCESSO', false));
    }

    public function avaliarLivro($uid = 0, $dados)
    {
        try {
            $livroAvaliacaoModel = new LivroAvaliacaoModel();
            if ($livroAvaliacaoModel->adicionarLivroAvaliacao($uid, $dados) == 0) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_LIVRO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_LIVRO_AVALIADO_SUCESSO', false));
    }

    public function adicionarFavorito($uid = 0, $dados)
    {
        try {
            $favoritosModel = new FavoritosModel();
            if ($favoritosModel->adicionarFavorito($uid, $dados) == 0) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_LIVRO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_LIVRO_FAVORITO_SUCESSO', false));
    }

    public function removerFavorito($uid = 0, $dados)
    {
        try {
            $favoritosModel = new FavoritosModel();
            if ($favoritosModel->removerFavorito($uid, $dados) == 0) {
                $this->httpResponse(200, MessageHelper::fmtMsgConst('ERR_LIVRO_NAO_ENCONTRADO'));
            }
        } catch (Exception $e) {
            $this->httpResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpResponse(200, MessageHelper::fmtMsgConst('MSG_LIVRO_DESFAVORITO_SUCESSO', false));
    }
}