<?php

namespace App\Http\Controllers\Financa;

use App\Http\Controllers\Controller;
use App\Model\Financa\Situacao as SituacaoModal;

class Situacao extends Controller
{
  private $situacao;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');
    
    $this->situacao = new SituacaoModal;
  }
  
  public function get() {
    
    try{
      $STATUS = 'success';
      $result = $this->situacao->all();
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Situacao/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
}