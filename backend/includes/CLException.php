<?php

class CLException extends Exception
{
    private $detalhe;

    public function __construct( $mensagem="", $codigo=0, $detalhe="") {
        $this->detalhe = $detalhe;
        parent::__construct(
            $mensagem,
            $codigo 
        );
    }

    public function getDetalhe() {
        return $this->detalhe;
    }


}