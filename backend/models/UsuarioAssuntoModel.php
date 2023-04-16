<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class UsuarioAssuntoModel extends BaseModel
{
    public $campos = array (
        'uid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'iid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true]
    );

    public function adicionarUsuarioAssunto($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            if ( $this->query("SELECT 1 FROM assuntos WHERE iid=:iid",  ['iid' => $dados['iid'] ]) <= 0  ) {
                    throw New Exception( "Assunto não encontrado");
            }

            return $this->query("INSERT INTO usuarios_assuntos (uid, iid) VALUES " .
            " (:uid, :iid)",
            array_merge(['uid' => $uid], $dados)
        );
    } catch (Exception $e) {
        throw New Exception( $e->getMessage(), $e->getCode() );
    }
}

public function deletarUsuarioAssunto($uid, $entrada)
{
        SQLHelper::validaCampos($this->campos, $entrada , 'DELETE');
        try {
            return $this->query("DELETE FROM usuarios_assuntos WHERE uid=:uid AND iid=:iid", ['uid' => $uid, 'iid' => $entrada['iid']]);
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }

}