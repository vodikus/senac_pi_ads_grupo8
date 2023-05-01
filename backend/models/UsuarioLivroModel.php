<?php
require_once "includes/BaseModel.php";
require_once "models/UsuarioModel.php";
require_once "models/LivroModel.php";

class UsuarioLivroModel extends BaseModel
{
    public $campos = array(
        'uid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'dh_cadastro' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function adicionarUsuarioLivro($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');

            (new LivroModel())->validaLivro($dados['lid']);
            (new UsuarioModel())->validaUsuario($uid);

            return $this->query(
                "INSERT INTO usuarios_livros (uid, lid) VALUES " .
                " (:uid, :lid)",
                array_merge(['uid' => $uid], $dados)
            );
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'PRIMARY')) {
                        throw new CLConstException('ERR_USUARIO_LIVRO_VINCULO_EXISTENTE', "lid: {$dados['lid']}");
                    }
                    break;
            }            
            throw $e;
        }
    }

    public function deletarUsuarioLivro($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'DELETE');

            (new LivroModel())->validaLivro($dados['lid']);
            (new UsuarioModel())->validaUsuario($uid);

            return $this->query("DELETE FROM usuarios_livros WHERE uid=:uid AND lid=:lid", ['uid' => $uid, 'lid' => $dados['lid']]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function atualizarUsuarioLivro($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');

            (new LivroModel())->validaLivro($dados['lid']);
            (new UsuarioModel())->validaUsuario($uid);


            return $this->query(
                "UPDATE usuarios_livros SET status=:status, dh_atualizacao=CURRENT_TIMESTAMP  " .
                " WHERE uid=:uid AND lid=:lid",
                array_merge(['uid' => $uid], $dados)
            );

        } catch (Exception $e) {
            switch ($e->getCode()) {
                case '01000':
                    if (stripos($e->getMessage(), "1265 Data truncated for column 'status") > 0) {
                        throw new CLConstException('ERR_USUARIO_LIVRO_STATUS_INVALIDO', $dados);
                    }
                    break;

                default:
                    throw $e;
            }
        }
    }

}