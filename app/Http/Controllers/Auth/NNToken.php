<?php

namespace App\Http\Controllers\Auth;

class NNToken
{
    public function Make($usuario) {
        $usuario = (object)$usuario; 

        $key = '2020';

        //Header Token
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        //Payload - Content
        $payload = [
            'exp' => date('Y-m-d H:i:s', strtotime("+10 min")),
            'uid' => 1,
            "USUA_ID"    => $usuario->USUA_ID,
            "USUA_EMAIL" => $usuario->USUA_EMAIL,
        ];

        //JSON
        $header = json_encode($header);
        $payload = json_encode($payload);

        //Base 64
        $header = base64_encode($header);
        $payload = base64_encode($payload);

        //Sign
        $sign = hash_hmac('sha256', $header . "." . $payload, $key, true);
        $sign = base64_encode($sign);

        //Token
        $token = $header . '.' . $payload . '.' . $sign;

        return $token;
    }
}
