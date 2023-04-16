<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class UsuarioLivroModel extends BaseModel
{
    public $campos = array (
        'uid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'estado' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'dh_cadastro' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true]
    );

    public function adicionarUsuarioLivro($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            if ( $this->query("SELECT 1 FROM livros WHERE lid=:lid",  ['lid' => $dados['lid'] ]) <= 0  ) {
                    throw New Exception( "Livro não encontrado");
            }

            return $this->query("INSERT INTO usuarios_livros (uid, lid, estado) VALUES " .
            " (:uid, :lid, :estado)",
            array_merge(['uid' => $uid], $dados)
        );
    } catch (Exception $e) {
        throw New Exception( $e->getMessage(), $e->getCode() );
    }
}

public function deletarUsuarioLivro($uid, $entrada)
{
        SQLHelper::validaCampos($this->campos, $entrada , 'DELETE');
        try {
            return $this->query("DELETE FROM usuarios_livros WHERE uid=:uid AND lid=:lid", ['uid' => $uid, 'lid' => $entrada['lid']]);
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}