<?php
require_once "includes/BaseModel.php";

class ImagemModel extends BaseModel {

    public function salvaFotoUsuario($uid, $files){
        $filename = "uid-" . $uid . "-avatar.jpg";
        $path = "../backend/imagens/usuarios/".$filename;
        move_uploaded_file($files['imagem']['tmp_name'], $path);
        return $this->query("UPDATE usuarios SET avatar=:imagem WHERE uid=:uid", array_merge(['imagem' => $path, 'uid' => $uid]));
    }

    public function removeFotoUsuario($uid){
        $filename = "uid-" . $uid . "-avatar.jpg";
        $path = "../backend/imagens/usuarios/".$filename;
        unlink($path);
        return $this->query("UPDATE usuarios SET avatar='' WHERE uid=:uid", array_merge(['uid' => $uid]));
    }

    public function salvaCapaLivro($lid, $files){
        $filename = "uid-" . $lid . "-capa.jpg";
        $path = "../backend/imagens/livros/".$filename;
        move_uploaded_file($files['imagem']['tmp_name'], $path);
        return $this->query("UPDATE livros SET capa=:imagem WHERE lid=:lid", array_merge(['imagem' => $path, 'lid' => $lid]));
    }

    public function removeCapaLivro($lid){
        $filename = "uid-" . $lid . "-capa.jpg";
        $path = "../backend/imagens/livros/".$filename;
        unlink($path);
        return $this->query("UPDATE livros SET capa='' WHERE lid=:lid", array_merge(['lid' => $lid]));
    }

}
