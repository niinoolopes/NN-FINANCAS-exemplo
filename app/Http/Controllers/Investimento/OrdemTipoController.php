<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use App\Model\Investimento\OrdemTipo as I_OrdemTipoModal;

class OrdemTipoController extends Controller
{
  private $ordemTipo;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->ordemTipo   = new I_OrdemTipoModal;
  }
  
  public function get() {
    
    try{
      $STATUS = 'success';
      $result = $this->ordemTipo->all();
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/OrdemTipo/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
}