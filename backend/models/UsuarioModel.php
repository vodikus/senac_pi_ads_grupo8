<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class UsuarioModel extends BaseModel
{
    public $campos = array (
        'uid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'email' => ['protected' => 'update', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'nome' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'senha' => ['protected' => 'none', 'type' => 'varchar', 'transform' => 'sha256', 'visible' => false, 'required' => true],
        'cpf' => ['protected' => 'update', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'nascimento' => ['protected' => 'none', 'type' => 'date', 'visible' => true, 'required' => true],
        'sexo' => ['protected' => 'none', 'type'=>'varchar', 'visible' => true, 'required' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true],
        'dh_criacao' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true],
        'avatar' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'apelido' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'role' => ['protected' => 'all', 'type' => 'varchar', 'visible' => true]
    );

    public function buscarTodosUsuarios()
    {
        $campos = SQLHelper::montaCamposSelect($this->campos,'u');
        return $this->select("SELECT $campos FROM usuarios u");
    }
    public function buscarUsuario($id = 0)
    {
        $campos = SQLHelper::montaCamposSelect($this->campos,'u');
        return $this->select("SELECT $campos FROM usuarios u WHERE uid=:uid", ['uid'=>$id]);
    }
    public function deletarUsuario($id = 0)
    {
        return $this->query("UPDATE usuarios SET status='D' WHERE uid=:uid", ['uid'=>$id]);
    }
    public function adicionarUsuario($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->query("INSERT INTO usuarios (email, nome, senha, cpf, nascimento, sexo, apelido, dh_atualizacao) VALUES (:email, :nome, SHA2(:senha,256), :cpf, :nascimento, :sexo, :apelido, CURRENT_TIMESTAMP)", $dados);
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }
    public function atualizarUsuario($id,$entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'UPDATE');
            $campos = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE usuarios SET $campos WHERE uid=:uid", array_merge(['uid'=>$id],$dados));
        } catch (Exception $e) {
            throw New Exception( $e->getMessage(), $e->getCode() );
        }
    }
    public function validarUsuarioSenha($email, $senha)
    {
        try {
            return $this->query("SELECT 1 FROM usuarios WHERE email=:email and senha=SHA2(:senha, 256)", ['email' => $email, 'senha' => $senha]);
        } catch (Exception $e) {
            throw New Exception( );
        }
    }
    public function buscaPorEmail($email)
    {
        try {
            $uid = $this->select("SELECT uid, role FROM usuarios WHERE email=:email", ['email' => $email]);
            return $uid[0];
        } catch (Exception $e) {
            throw New Exception( );
        }
    }

}