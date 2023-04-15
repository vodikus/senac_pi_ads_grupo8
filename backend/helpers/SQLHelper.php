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
    public static function validaCampos($campos, $dados, $acao) {
        // @TODO AJUSTAR LÓGICA PARA VALIDAR OBRIGATORIEDADE INSERT / UPDATE
        $retorno = array();
        foreach ( $dados as $chave => $valor ) {
            if ( array_key_exists($chave, $campos) ) {
                // se for all, não valida tipo e rejeita
                if ($campos[$chave]['protected']=='all') {
                    throw New Exception( "Campo $chave não pode ser alterado" , -1 );
                } else {
                    if ($campos[$chave]['protected']=='none' || $acao == 'INSERT' || ( $acao == 'UPDATE' && $campos[$chave]['protected']!='update') ) {
                        switch ($campos[$chave]['type']) {
                            case 'int':
                                $valido = is_int($valor);
                                break;
                            case 'float':
                                $valido = is_float($valor);
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
                        if ($valido) {
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

}
