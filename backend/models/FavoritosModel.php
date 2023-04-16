<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class FavoritosModel extends BaseModel
{
    public $campos = array (
        'uid_usuario' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'lid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true],
        'uid_dono' => ['protected' => 'none', 'type' => 'int', 'visible' => true]
    );

    public function adicionarFavorito($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            if ( $this->query("SELECT 1 FROM livros WHERE lid=:lid",  ['lid' => $dados['lid'] ]) <= 0  ) {
                    throw New Exception( "Livro não encontrado");
            }
            if ( $this->query("SELECT 1 FROM usuarios WHERE uid=:uid",  ['uid' => $dados['uid_dono'] ]) <= 0  ) {
                    throw New Exception( "Usuário não encontrado");
            }

            return $this->query("INSERT INTO favoritos (uid_usuario, lid, uid_dono) VALUES " .
            " (:uid, :lid, :uid_dono)",
            array_merge(['uid' => $uid], $dados)
        );
    } catch (Exception $e) {
        throw New Exception( $e->getMessage(), $e->getCode() );
    }
}

public function removerFavorito($uid, $entrada)
{
        SQLHelper::validaCampos($this->campos, $entrada , 'DELETE');
        try {
            return $this->query("DELETE FROM favoritos WHERE uid_usuario=:uid AND lid=:lid and uid_dono=:uid_dono", 
            ['uid' => $uid, 'lid' => $entrada['lid'], 'uid_dono' => $entrada['uid_dono'] ]);
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}