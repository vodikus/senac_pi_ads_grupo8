<?php

use helpers\Constantes;

class CLException extends Exception
{
    public function __construct( $const = "ERR_NAODEFINIDO") {
        parent::__construct(
            Constantes::getMsg($const),
            Constantes::getCode($const) 
        );
    }
}