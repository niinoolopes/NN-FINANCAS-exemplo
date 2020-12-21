<?php

namespace App\Http\Controllers\Cofre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Cofre\Item as CofreItem;

class Item extends Controller
{
  private $item;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->item = new CofreItem;
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
        'msg' => 'Erro ao executar Controller/Cofre/Item/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $item = $this->item;
      $item->COIT_VALOR     = $request->get('COIT_VALOR');
      $item->COIT_DATA      = $request->get('COIT_DATA');
      $item->COIT_OBS       = $request->get('COIT_OBS');
      $item->COIT_PROPOSITO = $request->get('COIT_PROPOSITO');
      $item->COIT_STATUS    = $request->get('COIT_STATUS');
      $item->COTP_ID        = $request->get('COTP_ID');
      $item->COCT_ID        = $request->get('COCT_ID');
      $item->USUA_ID        = $_GET['usuario'];
      $item->save();

      // --

      $STATUS = 'success';
      $result = $item;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Cofre/Item/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($COIT_ID, Request $request)
  {
    $item = $this->item->find($COIT_ID);
    
    if($item){

      try{
       
        // update
        $item->COIT_VALOR     = $request->get('COIT_VALOR');
        $item->COIT_DATA      = $request->get('COIT_DATA');
        $item->COIT_OBS       = $request->get('COIT_OBS');
        $item->COIT_PROPOSITO = $request->get('COIT_PROPOSITO');
        $item->COIT_STATUS    = $request->get('COIT_STATUS');
        $item->COTP_ID        = $request->get('COTP_ID');
        $item->COCT_ID        = $request->get('COCT_ID');
        $item->USUA_ID        = $request->get('USUA_ID');
        $item->save();

        // --
  
        $STATUS = 'success';
        $result = $item;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Cofre/item/update'
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
    
  public function delete($COIT_ID)
  {
    $item = $this->item->find($COIT_ID);

    if($item){
    
      try{
        
        $STATUS = 'success';
        $result = $item->delete();
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Cofre/Item/delete'
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

