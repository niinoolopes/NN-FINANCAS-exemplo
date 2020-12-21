<?php

namespace App\Http\Controllers\Financa;

use App\Http\Controllers\Controller;
use App\Model\Financa\Tipo as TipoModal;

class Tipo extends Controller
{
  private $tipo;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');
    
    $this->tipo = new TipoModal;
  }
  
  public function get() {
    
    try{
      $STATUS = 'success';
      $result = $this->tipo->all();
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Tipo/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
}