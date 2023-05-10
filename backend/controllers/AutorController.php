<?php
use helpers\MessageHelper;

include_once 'models/AutorModel.php';

class AutorController extends BaseController
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
                    case 'buscar-por-nome':
                        $this->buscarPorNome($params['params']);
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
     * @apiDefine ERR_AUTOR_PADRAO
     *
     * @apiError (Erro 4xx) 9400 Autor não encontrado.
     * @apiError (Erro 4xx) 9401 Já existe um autor com este nome.
     *
     */

    /**
     * @apiDefine SAIDA_LISTA_AUTORES
     *
     * @apiSuccess {Object[]} autores Lista de autores
     * @apiSuccess {Number} autores.iid ID do autores
     * @apiSuccess {String} autores.nome_autor Nome do autores
     * @apiSuccess {Timestamp} autores.dh_atualizacao  Data/Hora de atualização
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *       {
     *         "iid": "1",
     *         "nome_autor": "Stephen King",
     *         "dh_atualizacao": "2023-04-15 20:45:26"
     *       }
     *     ]
     *
     */

    /**
     * @api {get} /autores/listar/ Listar Autores
     * @apiName Listar
     * @apiGroup Autores
     * @apiVersion 1.0.0
     *
     * @apiUse SAIDA_LISTA_AUTORES
     * @apiUse ERR_GENERICOS
     * 
     */
    public function listar($uid = 0)
    {
        try {
            $autorModel = new AutorModel();
            $arrAutores = $autorModel->listarAutores();
            $responseData = json_encode($arrAutores);
        } catch (Exception $e) {
            $this->httpRawResponse(500, $e->getMessage());
        }
        $this->montarSaidaOk($responseData);
    }

    /**
     * @api {get} /autores/buscar-por-nome/ Buscar Autor pelo Nome
     * @apiName Buscar por nome
     * @apiGroup Autores
     * @apiVersion 1.0.0
     *
     * @apiBody {String} nome_autor Nome do Autor
     *
     * @apiUse SAIDA_LISTA_AUTORES
     * @apiUse ERR_GENERICOS
     * 
     */
    public function buscarPorNome($entrada)
    {
        try {
            parse_str(substr($entrada,1), $params);
            $nome = (array_key_exists('nome', $params)) ? $params['nome'] : '';
            $autorModel = new AutorModel();
            $arrAutores = (array) $autorModel->buscarAutorPorNome($nome);
            $responseData = json_encode($arrAutores);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->montarSaidaOk($responseData);
    }    

    /**
     * @api {post} /autores/adicionar/ Adicionar Autor
     * @apiName Adicionar
     * @apiGroup Autores
     * @apiVersion 1.0.0
     *
     * @apiBody {String} nome_autor Nome do Autor
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_AUTOR_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9601",
     *         "mensagem": "Já existe um autor com este nome",
     *         "detalhe": ""
     *     }
     */    
    public function adicionar($dados)
    {
        try {
            $autorModel = new AutorModel();
            $autorId = $autorModel->adicionarAutor($dados);
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AUTOR_CADASTRO_SUCESSO', ['autorId' => $autorId]));
    }

    /**
     * @api {delete} /autores/deletar/:id Deletar Autor
     * @apiName Deletar 
     * @apiGroup Autores
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id ID único do autor.
     *
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_AUTOR_PADRAO
     * @apiError (Erro 5xx) 9402 Este autor não pode ser deletado pois está vinculado a um ou mais livros.
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9602",
     *         "mensagem": "Este AUTOR não pode ser deletado pois está vinculado a um ou mais livros",
     *         "detalhe": ""
     *     }
     */  
    public function deletar($aid)
    {
        try {
            if (is_numeric($aid)) {
                $autorModel = new AutorModel();
                if ($autorModel->deletarAutor($aid) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_AUTOR_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AUTOR_DELETADO_SUCESSO'));
    }

    /**
     * @api {put} /AUTORES/atualizar/:id Atualizar Autor
     * @apiName Atualizar
     * @apiGroup Autores
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id ID único do autor.
     * 
     * @apiBody {String} nome_autor Nome do autor.
     * 
     * @apiUse SAIDA_PADRAO
     * @apiUse ERR_GENERICOS
     * @apiUse ERR_AUTOR_PADRAO
     * 
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 500 Internal Server Error
     *     {
     *         "codigo": "9401",
     *         "mensagem": "Já existe um autor com este nome",
     *         "detalhe": ""
     *     }
     */
    public function atualizar($aid, $dados)
    {
        try {
            if (is_numeric($aid)) {
                $autorModel = new AutorModel();
                if ($autorModel->atualizarAutor($aid, $dados) == 0) {
                    $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_AUTOR_NAO_ENCONTRADO'));
                }
            } else {
                $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('ERR_ID_INVALIDO'));
            }
        } catch (Exception $e) {
            $this->httpRawResponse(500, MessageHelper::fmtException($e));
        }
        $this->httpRawResponse(200, MessageHelper::fmtMsgConstJson('MSG_AUTOR_ATUALIZADO_SUCESSO'));
    }
}