<?php
namespace Helpers;
class TokenHelper
{
    private static $defaultHeader = [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ];

    public static function generateToken($email, $roles, $userId=0, $expSeconds = 60) {
        if ( !empty($email) && !empty($roles) ) {
            return self::generateJwt(
                self::$defaultHeader,
                [ 
                    'uid' => $userId, 
                    'email' => $email, 
                    'roles' => $roles,
                    'exp' => time() + $expSeconds
                ]
                );
        } else {
            throw New Exception( 'Campos email e roles são requeridos para gerar o token.' );
        }
    }

    private static function generateJwt($header, $payload, $secret = '53nh4D0L1Vr0') {
        $encHeader = self::base64Url_encode(json_encode($header));
        $encPayload = self::base64Url_encode(json_encode($payload));
        $signature = self::generateSignature($encHeader . "." . $encPayload, $secret);
        $encSignature = self::base64Url_encode($signature);
        return "$encHeader.$encPayload.$encSignature";
    }

    private static function base64Url_encode($str) {
        return rtrim(str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($str)));
    }

    public static function validateToken($token, $secret = '53nh4D0L1Vr0') {
        $arrToken = explode('.', $token);
        if ( count($arrToken) == 3 ) {
            $header = base64_decode($arrToken[0]);
            $payload = base64_decode($arrToken[1]);
            $signTkn = base64_decode($arrToken[2]);
    
            $exp = json_decode($payload)->exp;
            // echo "Exp: $exp\n";
            $expired = ($exp - time()) < 0;
    
            $signChk = self::generateSignature(
                self::base64Url_encode($header) 
                . "." . 
                self::base64Url_encode($payload),
                $secret
            );
    
            $signValid = ($signTkn === $signChk);
            return ( $expired || !$signValid ) ? false : true;
        } else {
            return false;
        }
    }
    public static function decodeToken($token) {
        $arrToken = explode('.', $token);
        if ( count($arrToken) == 3 ) {
            $header = json_decode(base64_decode($arrToken[0]));
            $payload = json_decode(base64_decode($arrToken[1]));
            $signTkn = base64_decode($arrToken[2]);
    
            return ['header' => $header, 'payload' => $payload, 'signature' => $signTkn];
        } else {
            throw New Exception( 'Token inválido' );
        }
    }

    private static function generateSignature($str, $secret) {
        return self::base64Url_encode(
            hash_hmac(
                'sha256', 
                $str, 
                $secret, 
                true
            )
        );
    }

    public static function extractToken($str) {
        preg_match("/Bearer\s(?'token'\S+)/", $str, $token);                        
        if ( is_array($token) &&  array_key_exists('token', $token) ) {
            return $token['token'];
        }
        return false;
    }

    public static function extractTokenField($token, $field) {
        if ( !empty($token) ) {
            $arrToken = self::decodeToken($token);
            if (array_key_exists('payload', $arrToken) && array_key_exists($field, $arrToken['payload'])) {
                return $arrToken['payload']->$field;
            } else {
                throw New Exception( "Campo $field não existe neste token" );
            }
        } else {
            throw New Exception( 'Token requerido' );
        }
        
    }


}
