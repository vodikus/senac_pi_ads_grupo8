<?php
require_once "includes/BaseModel.php";
require_once "models/AssuntoModel.php";

class UsuarioAssuntoModel extends BaseModel
{
    public $campos = array(
        'uid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'iid' => ['protected' => 'none', 'type' => 'int', 'visible' => true, 'required' => true]
    );


    public function listarAssuntos($uid)
    {
        return $this->select("SELECT a.iid, a.nome_assunto FROM usuarios_assuntos ua INNER JOIN assuntos a ON a.iid = ua.iid WHERE uid=:uid", ['uid' => $uid]);
    }

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
                    if (stripos($e->getMessage(), 'PRIMARY')) {
                        throw new CLConstException('ERR_USUARIO_ASSUNTO_VINCULO_EXISTENTE', "iid: {$dados['iid']}");
                    }
                    break;
            }
            throw $e;
        }
    }

    public function deletarUsuarioAssunto($uid, $entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'DELETE');
            (new AssuntoModel())->validaAssunto($dados['iid']);
            return $this->query("DELETE FROM usuarios_assuntos WHERE uid=:uid AND iid=:iid", ['uid' => $uid, 'iid' => $dados['iid']]);
        } catch (Exception $e) {
            throw $e;
        }
    }

}