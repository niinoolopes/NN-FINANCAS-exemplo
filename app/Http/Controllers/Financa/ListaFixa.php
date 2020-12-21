<?php

namespace App\Http\Controllers\Financa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Financa\ListaFixa as F_financaListaFixa;

class ListaFixa extends Controller
{
  private $listaFixa ;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');
    
    $this->listaFixa = new F_financaListaFixa;
  }

  public function get()
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      $STATUS = 'success';
      $result = $this->listaFixa->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Financa/ListaFixa/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $listaFixa = $this->listaFixa;
      $listaFixa->FNIT_VALOR  = $request->FNIT_VALOR;
      $listaFixa->FNIT_DATA   = $request->FNIT_DATA;
      $listaFixa->FNIT_OBS    = $request->FNIT_OBS;
      $listaFixa->FNIT_STATUS = $request->FNIT_STATUS;
      $listaFixa->FNIS_ID     = $request->FNIS_ID;
      $listaFixa->FITP_ID     = $request->FITP_ID;
      $listaFixa->FIGP_ID     = $request->FIGP_ID;
      $listaFixa->FICT_ID     = $request->FICT_ID;
      $listaFixa->FINC_ID     = $request->FINC_ID;
      $listaFixa->USUA_ID     = $request->USUA_ID;
      $listaFixa->save();

      // --

      $STATUS = 'success';
      $result = $listaFixa;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Financa/ListaFixa/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($FNLF_ID, Request $request)
  {
    $listaFixa = $this->listaFixa->find($FNLF_ID);
    
    if($listaFixa){

      try{

        // update
        $listaFixa->FNIT_VALOR  = $request->FNIT_VALOR;
        $listaFixa->FNIT_DATA   = $request->FNIT_DATA;
        $listaFixa->FNIT_OBS    = $request->FNIT_OBS;
        $listaFixa->FNIT_STATUS = $request->FNIT_STATUS;
        $listaFixa->FNIS_ID     = $request->FNIS_ID;
        $listaFixa->FITP_ID     = $request->FITP_ID;
        $listaFixa->FIGP_ID     = $request->FIGP_ID;
        $listaFixa->FICT_ID     = $request->FICT_ID;
        $listaFixa->FINC_ID     = $request->FINC_ID;
        $listaFixa->USUA_ID     = $request->USUA_ID;
        $listaFixa->save();

        // --
  
        $STATUS = 'success';
        $result = $listaFixa;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Financa/listaFixa/update'
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
    
  public function delete($FNLF_ID)
  {
    $listaFixa = $this->listaFixa->find($FNLF_ID);

    if($listaFixa){
    
      try{
        
        $STATUS = 'success';
        $result = $listaFixa->delete();
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Financa/ListaFixa/delete'
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

