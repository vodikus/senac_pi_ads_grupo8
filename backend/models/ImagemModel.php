<?php
require_once "includes/BaseModel.php";

class ImagemModel extends BaseModel {

    public function salvaFotoUsuario($uid, $files){
        $filename = "uid-" . $uid . "-avatar.jpg";
        $path = "../backend/imagens/usuarios/".$filename;
        move_uploaded_file($files['foto']['tmp_name'], $path);
        return $this->query("UPDATE usuarios SET avatar=:imagem WHERE uid=:uid", array_merge(['imagem' => $path, 'uid' => $uid]));
    }

    public function removeFotoUsuario($uid){
        return $this->query("UPDATE usuarios SET avatar='' WHERE uid=:uid", array_merge(['uid' => $uid]));
    }

}
