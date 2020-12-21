<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\Ativo as AtivoModel;

class AtivoController extends Controller
{
  private $ativo;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
    
    $this->ativo = new AtivoModel;
  }

  public function get() {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      $STATUS = 'success';
      $result = $this->ativo->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Ativo/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $ativo = $this->ativo;
      $ativo->INAV_CODIGO    = $request->get('INAV_CODIGO');
      $ativo->INAV_DESCRICAO = $request->get('INAV_DESCRICAO');
      $ativo->INAV_CPNJ      = $request->get('INAV_CPNJ');
      $ativo->INAV_SITE      = $request->get('INAV_SITE');
      $ativo->INAV_LIQUIDEZ  = $request->get('INAV_LIQUIDEZ');
      $ativo->INAV_VENC      = $request->get('INAV_VENC');
      $ativo->INAV_STATUS    = $request->get('INAV_STATUS');
      $ativo->INAT_ID        = $request->get('INAT_ID');
      $ativo->USUA_ID        = $request->get('USUA_ID');
      $ativo->save();

      // --

      $STATUS = 'success';
      $result = $ativo;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Ativo/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
  
  public function update($INAV_ID, Request $request)
  {
    $ativo = $this->ativo->find($INAV_ID);

    if($ativo){

      try{
        // update
        $ativo->INAV_CODIGO    = $request->get('INAV_CODIGO');
        $ativo->INAV_DESCRICAO = $request->get('INAV_DESCRICAO');
        $ativo->INAV_LIQUIDEZ  = $request->get('INAV_LIQUIDEZ');
        $ativo->INAV_CPNJ      = $request->get('INAV_CPNJ');
        $ativo->INAV_SITE      = $request->get('INAV_SITE');
        $ativo->INAV_VENC      = $request->get('INAV_VENC');
        $ativo->INAV_STATUS    = $request->get('INAV_STATUS') == 1 ? 1 : 0 ;
        $ativo->INAT_ID        = $request->get('INAT_ID');
        $ativo->USUA_ID        = $request->get('USUA_ID');
        $ativo->save();
        // --
  
        $STATUS = 'success';
        $result = $ativo;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Ativo/update'
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
  
  public function delete($INAV_ID) {
    $ativo = $this->ativo->find($INAV_ID);

    if($ativo){
    
      try{
        
        $STATUS = 'success';
        $result = $ativo->delete();
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Ativo/delete'
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













  // ainda falta refatorar, quando criar as rotas PAGE apos usar apagar

  public function ativoCarteira($id) {

    try{
      return response()->json(['STATUS' => 'success','data' => $this->ativo->ativoCarteira($_GET, $id)]);
    
    }
    catch (\Exception $e){
      return response()->json(['STATUS' => 'error', 'msg' => 'Erro ao executar Investimento_Ativo_Model']);

    }
  }


  // -- RENDIMENTOS 

  public function rendimentoStore(Request $request) {
    // dd($request->all());
    
    try{
      $this->rendimento   = new InvestimentoAtivoRendimentoModel;

        $this->rendimento->INAR_TIPO   = $request->get('INAR_TIPO');
      $this->rendimento->INAR_VALOR  = $request->get('INAR_VALOR');
      $this->rendimento->INAR_DATA   = $request->get('INAR_DATA');
      $this->rendimento->INAR_STATUS = $request->get('INAR_STATUS');
      $this->rendimento->INAV_ID     = $request->get('INAV_ID');
      $this->rendimento->INCR_ID     = $request->get('INCR_ID');

      $this->rendimento->save();

      return response()->json(['STATUS'  => 'success','data' => $this->rendimento]);

    }
    catch (\Exception $e){
      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Model']);

    }
  }

  public function rendimentoUpdate($id, Request $request) {
    // dd($request->all());
    
    try{
      $this->rendimento   = new InvestimentoAtivoRendimentoModel;
      
      $rendimento = $this->rendimento->find($id);
      $rendimento->INAR_STATUS = $request->get('INAR_STATUS');
      $rendimento->INAR_TIPO   = $request->get('INAR_TIPO');
      $rendimento->INAR_DATA   = $request->get('INAR_DATA');
      $rendimento->INAV_ID     = $request->get('INAV_ID');
      $rendimento->INCR_ID     = $request->get('INCR_ID');

      $this->rendimento->save();

      return response()->json(['STATUS'  => 'success','data' => $this->rendimento]);

    }
    catch (\Exception $e){
      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Model']);

    }
  }
  
  public function rendimentoDelete($id) {
    $this->rendimento   = new InvestimentoAtivoRendimentoModel;

    try{
      return response()->json(['STATUS' => 'success','data' => $this->rendimento->find($id)->delete()]);
    
    }
    catch (\Exception $e){
      return response()->json(['STATUS' => 'error', 'msg' => 'Erro ao executar Investimento_Ativo_Model']);

    }
  }
}