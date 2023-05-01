<?php

use helpers\Constantes;

class CLConstException extends CLException
{
   
    public function __construct( $const = "ERR_NAODEFINIDO", $detalhe = "") {
        parent::__construct(
            Constantes::getMsg($const),
            Constantes::getCode($const),
            $detalhe
        );
    }

}