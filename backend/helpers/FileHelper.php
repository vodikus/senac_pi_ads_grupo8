<?php
namespace helpers;

class FileHelper
{

    public static function validaImagem($files)
    {

        if (!empty($files['imagem'])) {

            $allowed_type = array('jpg', 'jpeg', 'png');
            $filename = basename($files['imagem']['name']);
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);

            if (in_array($filetype, $allowed_type)) {
                return true;
            }
            return null;
        }
        return null;
    }


    public static function downloadImagem($url, $file)
    {
        error_log("URL: $url");
        error_log("Arquivo: $file");
        $fp = fopen($file, 'w+');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FILE, $fp);          
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 9000);      
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
        curl_exec($ch);
        curl_close($ch);                           
        fclose($fp);
    }

}