<?php
namespace helpers;

class FileHelper

{

    public static function validaImagem($files)
    {

        if (!empty($files['foto'])) {

            $allowed_type = array('jpg', 'jpeg');
            $filename = basename($files['foto']['name']);
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);

            if (in_array($filetype, $allowed_type)) {
                return $files['foto'];
            }
            return null;
        }
        return null;
    }


}