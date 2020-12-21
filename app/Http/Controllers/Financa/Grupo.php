<?php

namespace App\Http\Controllers\Financa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Financa\Grupo as FinancaGrupo;

class Grupo extends Controller
{
  private $grupo;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');
    
    $this->grupo = new FinancaGrupo;
  }

  public function get()
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $STATUS = 'success';
      $result   = $this->grupo->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Financa/Grupo/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $grupo = $this->grupo;
      $grupo->FIGP_DESCRICAO = $request->FIGP_DESCRICAO;
      $grupo->FIGP_STATUS    = $request->FIGP_STATUS;
      // $grupo->FIGP_SLUG      = Str::slug($request->FIGP_DESCRICAO);
      $grupo->FITP_ID        = $request->FITP_ID;
      $grupo->FINC_ID        = $request->FINC_ID;
      $grupo->save();

    // --

      $STATUS = 'success';
      $result = $grupo;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Financa/Grupo/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($FIGP_ID, Request $request)
  {
    $grupo = $this->grupo->find($FIGP_ID);

    if($grupo){
      
      try{
        // update
        $grupo->FIGP_DESCRICAO = $request->get('FIGP_DESCRICAO');
        $grupo->FIGP_STATUS    = $request->get('FIGP_STATUS');
        // $grupo->FIGP_SLUG      = Str::slug($request->get('FIGP_DESCRICAO'));
        $grupo->FITP_ID        = $request->get('FITP_ID');
        $grupo->FINC_ID        = $request->get('FINC_ID');
        $grupo->save();
    
        // --
        
        $STATUS = 'success';
        $result = $grupo;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Financa/Grupo/update'
        ];
      }

    } else {
        
      $STATUS = 'erro';
      $result = (object) [
        'msg' => 'o ID não existe.'
      ];
    }

    // --

    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }
  
  public function delete($FIGP_ID)
  {
    $grupo = $this->grupo->find($FIGP_ID);

    if($grupo){
      
      try{
        
        $STATUS = 'success';
        $result = $grupo->delete();
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Financa/Grupo/delete'
        ];
      }

    } else {
          
      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'o ID não existe.'
      ];
    }

    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }
}

