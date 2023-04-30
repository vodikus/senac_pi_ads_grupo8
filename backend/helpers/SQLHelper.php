<?php
// namespace Helpers;
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
        // Percorrer a lista de campos e verificar se os requeridos existem no array de dados
        foreach ( $campos as $chave => $valor ) {
            if ( self::requerido($valor) ) {
                if ( !array_key_exists($chave, $dados) ) {
                    throw New Exception( "Campo $chave é requerido" , 9994 );
                }
            }
        }

        $retorno = array();
        foreach ( $dados as $chave => $valor ) {
            if ( array_key_exists($chave, $campos) ) {
                // se for all, não valida tipo e rejeita
                if ($campos[$chave]['protected']=='all') {
                    if ( $acao == 'DELETE' ) {
                        return true;
                    } else {
                        throw New Exception( "Campo $chave não pode ser alterado" , 9990 );
                    }
                } else {
                    if ($campos[$chave]['protected']=='none' || $acao == 'INSERT' || ( $acao == 'UPDATE' && $campos[$chave]['protected']!='update') ) {
                        if ( self::validaTipo( $campos[$chave]['type'], $valor )) {
                            $retorno[$chave] = $valor;
                        } else {
                            throw New Exception( "Campo $chave é inválido" , 9991 );
                        }
                    } else {
                        throw New Exception( "Campo $chave não pode ser alterado" , 9992 );
                    }
                }
            } else {
                throw New Exception( "Campo $chave não localizado para este modelo" , 9993 );
            }
        }
        return $retorno;
    }

    public static function limpaDados($campos, $dados, $tipos = ['all']) {
        $retorno = array();
        
        error_log("ldp ".var_export($campos, true));
        foreach ( $dados as $chave => $valor ) {
            error_log("$chave:" . var_export($valor, true));
            foreach ( $tipos as $tipo ) {
                if ( array_key_exists($chave, $campos) && $campos[$chave]['protected'] != $tipo ) {
                    $retorno[$chave] = $valor;
                } 
            }
        }
        return $retorno;
    }
    private static function requerido($campo) {
        return ( array_key_exists('required', $campo) && $campo['required'] );
    }
    
    private static function validaTipo($chave, $valor) {
        switch ($chave) {
                            case 'int':
                $valido = is_null($valor) || is_int(intval($valor));
                                break;
                            case 'float':
                $valido = is_null($valor) || is_float(floatval($valor));
                                break;
                            case 'varchar':
                $valido = is_null($valor) || is_string($valor);
                                break;
                            case 'date':
                $valido = is_null($valor) || TimeDateHelper::validateDateTime($valor,"Y-m-d");
                                break;
                            case 'timestamp':
                $valido = is_null($valor) || TimeDateHelper::validateDateTime($valor);
                                break;
                            default:
                                $valido = false;
                                break;
                        }
        return $valido;
        }

    public static function sobrescrevePropriedades($campos, $entrada) {
        $saida = array_replace_recursive($campos,$entrada);
        return $saida;
    }

    public static function limpaCamposProtegidos($valor) {
        return !(array_key_exists('protected', $valor) && $valor['protected'] == 'all');
    }
}
