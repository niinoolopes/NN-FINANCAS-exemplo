<?php

namespace App\Http\Middleware;

use Closure;

class NNToken
{
    public function handle($request, Closure $next)
    {
        return $next($request);
        
        $STATUS = 'erro';
        
        $h = apache_request_headers();

        if( isset($h['Authorization']) ) {

            $str = $h['Authorization'];
            $str = explode('.', $str);

            $erro = count($str) != 1;

            if( $erro ){
    
                $header  = $str[0];
                $payload = $str[1];
                $sign    = $str[2];
                
    
                $header  = base64_decode($header);
                $payload = base64_decode($payload);
                
                $header  = json_decode($header);
                $payload = json_decode($payload);
        
    
                $exp = $payload->exp;
                
                $time = strtotime(date('Y-m-d H:i:s'));
                $exp  = strtotime($exp);
    
    
                if( $time > $exp ){
                    $erro = false;
                }
            }

            if(!$erro){
                return response()->json([
                    'STATUS'  => 'token', 
                    'msg' => 'o Login expirou.'
                ]);
            }


            return $next($request);
        }
        
        return response()->json([
            'STATUS'  => $STATUS, 
            'msg' => 'o uso de Token é obrigatório'
        ]);
    }
}
