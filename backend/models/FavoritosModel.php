<?php
require_once "includes/BaseModel.php";
require_once "models/LivroModel.php";
require_once "models/UsuarioModel.php";

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

            (new LivroModel())->validaLivro($dados['lid']);
            (new UsuarioModel())->validaUsuario($dados['uid_dono']);            

            return $this->query("INSERT INTO favoritos (uid_usuario, lid, uid_dono) VALUES " .
            " (:uid, :lid, :uid_dono)",
            array_merge(['uid' => $uid], $dados)
        );
    } catch (Exception $e) {
        switch ($e->getCode()) {
            case 23000:
                if (stripos($e->getMessage(), 'PRIMARY')) {
                    throw new CLException('ERR_AUTOR_VINCULO_EXISTE');
                }
                break;
        }
        throw new Exception($e->getMessage(), $e->getCode());
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