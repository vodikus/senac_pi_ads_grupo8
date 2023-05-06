<?php
require_once "includes/BaseModel.php";

class UsuarioModel extends BaseModel
{
    public $campos = array(
        'uid' => ['protected' => 'all', 'type' => 'int', 'visible' => true],
        'email' => ['protected' => 'update', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'nome' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'senha' => ['protected' => 'none', 'type' => 'varchar', 'transform' => 'sha256', 'visible' => false, 'required' => true],
        'cpf' => ['protected' => 'update', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'nascimento' => ['protected' => 'none', 'type' => 'date', 'visible' => true, 'required' => true],
        'sexo' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'dh_atualizacao' => ['protected' => 'all', 'type' => 'timestamp', 'transform' => 'current_timestamp', 'update' => 'always', 'visible' => true],
        'dh_criacao' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true],
        'avatar' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'apelido' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true, 'required' => true],
        'status' => ['protected' => 'none', 'type' => 'varchar', 'visible' => true],
        'role' => ['protected' => 'all', 'type' => 'varchar', 'visible' => true]
    );

    public function validaUsuario($uid)
    {
        if ($this->query("SELECT 1 FROM usuarios WHERE uid=:uid", ['uid' => $uid]) <= 0) {
            throw new CLConstException('ERR_USUARIO_NAO_ENCONTRADO');
        }
        return true;
    }

    public function validaBloqueio($uid, $uid_blq)
    {
        if ($this->query("SELECT 1 FROM usuarios_bloqueio WHERE uid=:uid AND uid_blq=:uid_blq", ['uid' => $uid, 'uid_blq' => $uid_blq]) > 0) {
            throw new CLConstException('ERR_USUARIO_BLOQUEADO');
        }
        return false;
    }
    public function buscarTodosUsuarios()
    {
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos, 'u');
            return $this->select("SELECT $campos FROM usuarios u");
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function buscarUsuario($id = 0)
    {
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos, 'u');
            return $this->select("SELECT $campos FROM usuarios u WHERE uid=:uid", ['uid' => $id]);
        } catch (Exception $e) {
            throw $e;
        }

    }
    public function deletarUsuario($id = 0)
    {
        try {
            return $this->query("UPDATE usuarios SET status='D' WHERE uid=:uid", ['uid' => $id]);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function adicionarUsuario($entrada)
    {
        try {
            $dados = SQLHelper::validaCampos($this->campos, $entrada, 'INSERT');
            return $this->insert("INSERT INTO usuarios (email, nome, senha, cpf, nascimento, sexo, apelido, dh_atualizacao) VALUES (:email, :nome, SHA2(:senha,256), :cpf, :nascimento, :sexo, :apelido, CURRENT_TIMESTAMP)", $dados);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'email_uk')) {
                        throw new CLConstException('ERR_EMAIL_EXISTENTE');
                    }
                    if (stripos($e->getMessage(),'cpf_uk')) {
                        throw new CLConstException('ERR_CPF_EXISTENTE');
                    }
                    break;
            }            
            throw $e;
        }
    }
    public function atualizarUsuario($id, $entrada)
    {
        try {
            $this->validaUsuario($id);

            $campos = array_filter(SQLHelper::sobrescrevePropriedades($this->campos, [
                'email' => ['required' => false],
                'nome' => ['required' => false],
                'cpf' => ['required' => false],
                'nascimento' => ['required' => false],
                'sexo' => ['required' => false],
                'apelido' => ['required' => false],
                'senha' =>  ['required' => false]
            ]), ['SQLHelper', 'limpaCamposProtegidos']);

            $dados = SQLHelper::validaCampos($campos, $entrada, 'UPDATE');
            $campos = SQLHelper::montaCamposUpdate($this->campos, $dados);
            return $this->query("UPDATE usuarios SET $campos WHERE uid=:uid", array_merge(['uid' => $id], $dados));
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'email_uk')) {
                        throw new CLConstException('ERR_EMAIL_EXISTENTE');
                    }
                    if (stripos($e->getMessage(),'cpf_uk')) {
                        throw new CLConstException('ERR_CPF_EXISTENTE');
                    }
                    break;
            }            
            throw $e;
        }
    }
    public function validarUsuarioSenha($email, $senha)
    {
        try {
            return $this->query("SELECT 1 FROM usuarios WHERE email=:email and senha=SHA2(:senha, 256)", ['email' => $email, 'senha' => $senha]);
        } catch (Exception $e) {
            throw new Exception();
        }
    }
    public function buscaPorEmail($email)
    {
        try {
            $uid = $this->select("SELECT uid, role FROM usuarios WHERE email=:email", ['email' => $email]);
            return $uid[0];
        } catch (Exception $e) {
            throw new Exception();
        }
    }

    public function bloquearUsuario($uid, $uid_blq)
    {
        try {
            return $this->insert("INSERT INTO usuarios_bloqueio (uid, uid_blq) VALUES (:uid,:uid_blq)", ['uid'=>$uid,'uid_blq'=>$uid_blq]);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'PRIMARY')) {
                        throw new CLConstException('ERR_USUARIO_JA_BLOQUEADO');
                    }
                    break;
            }            
            throw $e;
        }
    }

    public function debloquearUsuario($uid, $uid_blq)
    {
        try {
            return $this->query("DELETE FROM usuarios_bloqueio WHERE uid=:uid AND uid_blq=:uid_blq", ['uid'=>$uid,'uid_blq'=>$uid_blq]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}