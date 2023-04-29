<?php
require_once "includes/BaseModel.php";
require_once "models/AssuntoModel.php";

class UsuarioAssuntoModel extends BaseModel
{
    public $campos = array(
        'uid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'iid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true]
    );

    public function adicionarUsuarioAssunto($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            (new AssuntoModel())->validaAssunto($dados['iid']);

            return $this->query(
                "INSERT INTO usuarios_assuntos (uid, iid) VALUES " .
                " (:uid, :iid)",
                array_merge(['uid' => $uid], $dados)
            );
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'PRIMARY')) {
                        throw New CLException('ERR_USUARIO_ASSUNTO_VINCULO_EXISTENTE');
                    }
                    break;
            }            
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    public function deletarUsuarioAssunto($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'DELETE');
            (new AssuntoModel())->validaAssunto($dados['iid']);
            return $this->query("DELETE FROM usuarios_assuntos WHERE uid=:uid AND iid=:iid", ['uid' => $uid, 'iid' => $dados['iid']]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

}