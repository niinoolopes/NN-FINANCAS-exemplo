<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\AtivoTipo as I_AtivoTipoModal;

class AtivoTipoController extends Controller
{
  private $ativoTipo;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->ativoTipo = new I_AtivoTipoModal;
  }
  
  public function get() {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      $STATUS = 'success';
      $result = $this->ativoTipo->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/AtivoTipo/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
  
  public function store(Request $request) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $ativoTipo = $this->ativoTipo;
      $ativoTipo->INAT_DESCRICAO = $request->get('INAT_DESCRICAO');
      $ativoTipo->INAT_STATUS    = $request->get('INAT_STATUS');
      $ativoTipo->INTP_ID        = $request->get('INTP_ID');
      $ativoTipo->USUA_ID        = $request->get('USUA_ID');
      $ativoTipo->save();

      // --

      $STATUS = 'success';
      $result = $ativoTipo;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/AtivoTipo/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
  
  public function update($INAT_ID, Request $request) {
    $ativoTipo = $this->ativoTipo->find($INAT_ID);

    if($ativoTipo){

      try{
        // update
        $ativoTipo->INAT_DESCRICAO = $request->get('INAT_DESCRICAO');
        $ativoTipo->INAT_STATUS    = $request->get('INAT_STATUS');
        $ativoTipo->INTP_ID        = $request->get('INTP_ID');
        $ativoTipo->USUA_ID        = $request->get('USUA_ID');
        $ativoTipo->save();
        
        // --
  
        $STATUS = 'success';
        $result = $ativoTipo;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/AtivoTipo/update'
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
  
  public function delete($INAT_ID) {
    $ativoTipo = $this->ativoTipo->find($INAT_ID);

    if($ativoTipo){
    
      try{
        
        $STATUS = 'success';
        $result = $ativoTipo->delete();
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/AtivoTipo/delete'
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