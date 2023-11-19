<?php
require_once "includes/BaseModel.php";

class ImagemModel extends BaseModel
{

    public function salvaFotoUsuario($uid, $files)
    {
        $filename = "uid-" . $uid . "-avatar.jpg";
        $path = dirname(__FILE__) . "/../imagens/usuarios/" . $filename;
        move_uploaded_file($files['imagem']['tmp_name'], $path);
        $caminho = "/imagens/usuarios/$filename";
        return $this->query("UPDATE usuarios SET avatar=:imagem WHERE uid=:uid", array_merge(['imagem' => $caminho, 'uid' => $uid]));
    }

    public function removeFotoUsuario($uid)
    {
        $filename = "uid-" . $uid . "-avatar.jpg";
        $path = dirname(__FILE__) . "/../imagens/usuarios/" . $filename;
        unlink($path);
        return $this->query("UPDATE usuarios SET avatar='' WHERE uid=:uid", array_merge(['uid' => $uid]));
    }

    public function salvaCapaLivro($lid, $files)
    {
        $filename = "uid-" . $lid . "-capa.jpg";
        $path = dirname(__FILE__) . "/../imagens/livros/" . $filename;
        error_log("De: " . $files['imagem']['tmp_name']);
        error_log("Para: $path");
        move_uploaded_file($files['imagem']['tmp_name'], $path);
        if (filesize($path) > 0) {
            $caminho = "/imagens/livros/$filename";
            return $this->query("UPDATE livros SET capa=:imagem WHERE lid=:lid", array_merge(['imagem' => $caminho, 'lid' => $lid]));
        }
        return false;
    }

    public function removeCapaLivro($lid)
    {
        $filename = "uid-" . $lid . "-capa.jpg";
        $path = dirname(__FILE__) . "/../imagens/livros/" . $filename;
        unlink($path);
        return $this->query("UPDATE livros SET capa='' WHERE lid=:lid", array_merge(['lid' => $lid]));
    }
}
