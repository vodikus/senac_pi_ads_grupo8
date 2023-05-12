<?php
require_once "includes/BaseModel.php";

class ImagemModel extends BaseModel {

    public function salvaFotoUsuario($uid, $files){
        $imagem_conteudo = $this->pegaConteudoImagem($files);
        return $this->query("UPDATE usuarios SET avatar=:imagem WHERE uid=:uid", array_merge(['imagem' => $imagem_conteudo, 'uid' => $uid]));
    }

    private function pegaConteudoImagem($files) {
        $imagem = $files['foto']['tmp_name'];
        return addslashes(file_get_contents($imagem));
    }

}
