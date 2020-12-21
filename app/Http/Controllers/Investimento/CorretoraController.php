<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\Corretora as I_Corretora;

class CorretoraController extends Controller
{
  private $corretora;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");

    $this->corretora = new I_Corretora;
  }
  
  public function get() {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      $STATUS = 'success';
      $result = $this->corretora->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Corretora/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);





    if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);
    
    $this->result = $this->corretora->get($_GET, $id);
    
    try{
      return response()->json(['STATUS'  => 'success','data' => $this->result]);

    }
    catch (\Exception $e){
      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Model']);

    }
  }

  public function store(Request $request) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $corretora = $this->corretora;
      $corretora->INCR_DESCRICAO = $request->get('INCR_DESCRICAO');
      $corretora->INCR_CPNJ      = $request->get('INCR_CPNJ');
      $corretora->INCR_SITE      = $request->get('INCR_SITE');
      $corretora->INCR_STATUS    = $request->get('INCR_STATUS');
      $corretora->USUA_ID        = $request->get('USUA_ID');
      $corretora->save();

      // --

      $STATUS = 'success';
      $result = $corretora;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Corretora/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($INCR_ID, Request $request) {
    $corretora = $this->corretora->find($INCR_ID);

    if($corretora){

      try{
        // update
        $corretora->INCR_DESCRICAO = $request->get('INCR_DESCRICAO');
        $corretora->INCR_CPNJ      = $request->get('INCR_CPNJ');
        $corretora->INCR_SITE      = $request->get('INCR_SITE');
        $corretora->INCR_STATUS    = $request->get('INCR_STATUS');
        $corretora->USUA_ID        = $request->get('USUA_ID');
        $corretora->save();

        // --
  
        $STATUS = 'success';
        $result = $corretora;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Corretora/update'
        ];
      }

    } else {
      
      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'o ID não existe.'
      ];
    }

    // --

    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }
  
  public function delete($INCR_ID) {
    $corretora = $this->corretora->find($INCR_ID);

    if($corretora){
    
      try{
        
        $STATUS = 'success';
        $result = $corretora->delete();
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Corretora/delete'
        ];
      }

    } else {
          
      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'o ID não existe.'
      ];
    }

    // --

    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }
}