<?php

class TokenHelper
{
    private static $defaultHeader = [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ];

    public static function generateToken($userId, $role, $expSeconds = 60) {
        if ( !empty($userId) && !empty($role) ) {
            return self::generateJwt(
                self::$defaultHeader,
                [ 
                    'user_id' => $userId, 
                    'role' => $role,
                    'exp' => time() + $expSeconds
                ]
                );
        } else {
            throw New Exception( 'Campos user_id e role são requeridos para gerar o token.' );
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
}
