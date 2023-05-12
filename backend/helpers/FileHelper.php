<?php
namespace helpers;

class FileHelper

{

    public static function validaImagem($files)
    {

        if (!empty($files['imagem'])) {

            $allowed_type = array('jpg', 'jpeg');
            $filename = basename($files['imagem']['name']);
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);

            if (in_array($filetype, $allowed_type)) {
                return true;
            }
            return null;
        }
        return null;
    }


}