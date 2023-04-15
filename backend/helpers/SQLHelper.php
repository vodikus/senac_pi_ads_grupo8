<?php

class SQLHelper
{
    public static function montaCamposUpdate($campos, $entrada) {
        $saida = '';
        foreach( $entrada as $chave => $valor ) {
            if ($campos[$chave]['protected']!='always') {
                if ( array_key_exists('transform',$campos[$chave]) && $campos[$chave]['transform']=='sha256' ) {
                    $saida .= "$chave=SHA2(:$chave,256), ";
                } else {
                    $saida .= "$chave=:$chave, ";
                }
            } else {
                throw New Exception( "Campo $chave não pode ser alterado" , -1 );
            }
        }
        foreach ( $campos as $chave => $valor ) {
            if ( array_key_exists('update', $valor) && $valor['update'] == 'always' ) {
                if ( array_key_exists('transform',$valor) && $campos[$chave]['transform']=='current_timestamp' ) {
                    $saida .= "$chave=CURRENT_TIMESTAMP, ";
                }
            }
        }
        return substr($saida,0,-2);
    }
    public static function montaCamposSelect($campos, $prefixo = "a") {
        $saida = '';
        foreach ( $campos as $chave => $valor ) {
            if ( array_key_exists('visible', $valor) && $valor['visible']) {
                $saida .= "$prefixo.$chave, ";
            }
        }
        return substr($saida,0,-2);
    }
    public static function validaCampos($campos, $dados, $acao) {
        $retorno = array();
        foreach ( $dados as $chave => $valor ) {
            if ( array_key_exists($chave, $campos) ) {
                // se for all, não valida tipo e rejeita
                if ($campos[$chave]['protected']=='all') {
                    if ( $acao == 'DELETE' ) {
                        return true;
                    } else {
                        throw New Exception( "Campo $chave não pode ser alterado" , -1 );
                    }
                } else {
                    if ($campos[$chave]['protected']=='none' || $acao == 'INSERT' || ( $acao == 'UPDATE' && $campos[$chave]['protected']!='update') ) {
                        if (self::validaTipo( $campos[$chave]['type'], $valor )) {
                            $retorno[$chave] = $valor;
                        } else {
                            throw New Exception( "Campo $chave é inválido" , -1 );
                        }
                    } else {
                        throw New Exception( "Campo $chave não pode ser alterado" , -1 );
                    }
                }
            } else {
                throw New Exception( "Campo $chave não localizado para este modelo" , -1 );
            }
        }
        return $retorno;
    }
    
    private static function validaTipo($tipo, $valor) {
        switch ($tipo) {
            case 'int':
                $valido = is_int(intval($valor));
                break;
            case 'float':
                $valido = is_float(floatval($valor));
                break;
            case 'varchar':
                $valido = is_string($valor);
                break;
            case 'date':
                $valido = TimeDateHelper::validateDateTime($valor,"Y-m-d");
                break;
            case 'timestamp':
                $valido = TimeDateHelper::validateDateTime($valor);
                break;
            default:
                $valido = false;
                break;
        }
        return $valido;
    }
}
