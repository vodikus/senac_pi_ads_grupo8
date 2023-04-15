<?php
require_once "includes/BaseModel.php";
require_once "helpers/SQLHelper.php";
require_once "helpers/TimeDateHelper.php";

class UsuarioModel extends BaseModel
{
    private $campos = array (
        'uid' => ['protected' => 'all', 'type' => 'int'],
        'email' => ['protected' => 'update', 'type' => 'varchar'],
        'nome' => ['protected' => 'none', 'type' => 'varchar'],
        'senha' => ['protected' => 'none', 'type' => 'varchar', 'transform' => 'sha256'],
        'cpf' => ['protected' => 'update', 'type' => 'varchar'],
        'nascimento' => ['protected' => 'none', 'type' => 'date'],
        'sexo' => ['protected' => 'none', 'type'=>'varchar'],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always'],
        'dh_criacao' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never'],
        'avatar' => ['protected' => 'none', 'type' => 'varchar'],
        'status' => ['protected' => 'none', 'type' => 'varchar']
    );

    public function buscarTodosUsuarios()
    {
        return $this->select("SELECT * FROM usuarios");
    }
    public function buscarUsuario($id = 0)
    {
        return $this->select("SELECT * FROM usuarios WHERE uid=:uid", ['uid'=>$id]);
    }
    public function deletarUsuario($id = 0)
    {
        return $this->query("UPDATE usuarios SET status='D' WHERE uid=:uid", ['uid'=>$id]);
    }
    public function criarUsuario($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->query("INSERT INTO usuarios (email,nome,senha,cpf,nascimento,sexo,dh_atualizacao) VALUES (:email, :nome, SHA2(:senha,256), :cpf, :nascimento, :sexo, CURRENT_TIMESTAMP)", $dados);
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
            $uid = $this->select("SELECT uid FROM usuarios WHERE email=:email", ['email' => $email]);
            return $uid[0]['uid'];
        } catch (Exception $e) {
            throw New Exception( );
        }
    }

}