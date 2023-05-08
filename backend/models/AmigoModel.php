<?php
require_once "includes/BaseModel.php";
require_once "models/UsuarioModel.php";

class AmigoModel extends BaseModel
{
    public $campos = array(
        'uid' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'uid_amigo' => ['protected' => 'none', 'type' => 'int', 'visible' => true],
        'dh_criacao' => ['protected' => 'all', 'type' => 'timestamp', 'update' => 'never', 'visible' => true]
    );

    public function validaAmigo($uid, $uid_amigo) {
        if ( $this->query("SELECT 1 FROM amigos WHERE uid=:uid AND uid_amigo=:uid_amigo",  ['uid' => $uid, 'uid_amigo' => $uid_amigo ]) <= 0  ) {
            throw new CLConstException('ERR_AMIGO_NAO_ENCONTRADO', "uid: $uid | uid_amigo: $uid_amigo");
        }
        return true;
    }    

    public function listarAmigos($uid)
    {
        try {
            $campos = SQLHelper::montaCamposSelect($this->campos, 'a');
            $amigos = $this->select("SELECT $campos, u.nome, u.apelido, u.status_chat, u.avatar FROM amigos a " .
            "INNER JOIN usuarios u ON a.uid_amigo=u.uid " . 
            "WHERE a.uid=:uid ORDER BY u.nome", 
            ['uid' => $uid]);
            return $amigos;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function adicionarAmigo($uid, $uid_amigo)
    {
        try {
            (new UsuarioModel())->validaUsuario($uid);
            (new UsuarioModel())->validaUsuario($uid_amigo);

            if ($uid == $uid_amigo) {
                throw new CLConstException('ERR_AMIGO_MESMO_USUARIO', "uid: $uid uid_amigo: $uid_amigo");
            }            

            return $this->insert(
                "INSERT INTO amigos (uid, uid_amigo) VALUES " .
                " (:uid, :uid_amigo),(:uid_amigo,:uid)",
                ['uid'=>$uid, 'uid_amigo'=>$uid_amigo]
            );

        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    if (stripos($e->getMessage(),'PRIMARY')) {
                        throw new CLConstException('ERR_AMIGO_JA_EXISTENTE');
                    }
                    break;
            }             
            throw $e;
        }
    }

    public function removerAmigo($uid, $uid_amigo)
    {
        try {
            (new UsuarioModel())->validaUsuario($uid);
            (new UsuarioModel())->validaUsuario($uid_amigo);

            if ($uid == $uid_amigo) {
                throw new CLConstException('ERR_AMIGO_MESMO_USUARIO', "uid: $uid uid_amigo: $uid_amigo");
            }  
            
            return $this->query(
                "DELETE FROM amigos WHERE (uid=:uid AND uid_amigo=:uid_amigo) OR (uid=:uid_amigo AND uid=:uid_amigo)",
                ['uid'=>$uid, 'uid_amigo'=>$uid_amigo]
            );

        } catch (Exception $e) {       
            throw $e;
        }
    }    


}