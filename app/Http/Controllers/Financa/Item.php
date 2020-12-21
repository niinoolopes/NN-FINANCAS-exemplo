<?php

namespace App\Http\Controllers\Financa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Financa\Item as FinancaItem;

class Item extends Controller
{
  private $item;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->item = new FinancaItem;
  }

  public function get()
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      
      $STATUS = 'success';
      $result = $this->item->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Financa/Item/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $item = $this->item;
      $item->FNIT_VALOR  = $request->FNIT_VALOR;
      $item->FNIT_DATA   = $request->FNIT_DATA;
      $item->FNIT_OBS    = $request->FNIT_OBS;
      $item->FNIT_STATUS = $request->FNIT_STATUS;
      $item->FNIS_ID     = $request->FNIS_ID;
      $item->FITP_ID     = $request->FITP_ID;
      $item->FIGP_ID     = $request->FIGP_ID;
      $item->FICT_ID     = $request->FICT_ID;
      $item->FINC_ID     = $request->FINC_ID;
      $item->USUA_ID     = $request->USUA_ID;
      $item->save();

      // --

      $STATUS = 'success';
      $result = $item;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Financa/Item/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($FNIT_ID, Request $request)
  {
    $item = $this->item->find($FNIT_ID);
    
    if($item){

      try{
       
        // update
        $item->FNIT_VALOR  = $request->FNIT_VALOR;
        $item->FNIT_DATA   = $request->FNIT_DATA;
        $item->FNIT_OBS    = $request->FNIT_OBS;
        $item->FNIT_STATUS = $request->FNIT_STATUS;
        $item->FNIS_ID     = $request->FNIS_ID;
        $item->FITP_ID     = $request->FITP_ID;
        $item->FIGP_ID     = $request->FIGP_ID;
        $item->FICT_ID     = $request->FICT_ID;
        $item->FINC_ID     = $request->FINC_ID;
        $item->USUA_ID     = $request->USUA_ID;
        $item->save();

        // --
  
        $STATUS = 'success';
        $result = $item;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Financa/item/update'
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
    
  public function delete($FNIT_ID)
  {
    $item = $this->item->find($FNIT_ID);

    if($item){
    
      try{
        
        $STATUS = 'success';
        $result = $item->delete();
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Financa/Item/delete'
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

