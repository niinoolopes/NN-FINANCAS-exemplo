<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\AtivoRendimento as I_AtivoRendimento;

class Rendimento extends Controller
{

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->rendimento = new I_AtivoRendimento;
  }

  
  public function get($id = false)
  {
    die('fazer');

    // if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);
    
    // try{
    //   return response()->json(['STATUS'  => 'success','data' => $this->rendimento->get($_GET, $id) ]);

    // }
    // catch (\Exception $e){
    //   return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Model']);

    // }
  }

  public function store(Request $request)
  {
    $this->rendimento->INAR_TIPO   = $request->INAR_TIPO;
    $this->rendimento->INAR_VALOR  = $request->INAR_VALOR;
    $this->rendimento->INAR_DATA   = $request->INAR_DATA;
    $this->rendimento->INAR_STATUS = $request->INAR_STATUS;
    $this->rendimento->INAV_ID     = $request->INAV_ID;
    $this->rendimento->save();
    try{

      return response()->json(['STATUS'  => 'success','data' => $this->rendimento]);

    }
    catch (\Exception $e){
      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Model']);

    }
  }

  public function update($id, Request $request)
  {
    try{
      $rendimento = $this->rendimento->find($id);
      
      $rendimento->INAR_VALOR  = $request->INAR_VALOR;
      $rendimento->INAR_STATUS = $request->INAR_STATUS;
      $rendimento->INAR_DATA   = $request->INAR_DATA;
      $rendimento->INAR_TIPO   = $request->INAR_TIPO;
      $rendimento->INAV_ID     = $request->INAV_ID;
      $rendimento->save();

      return response()->json(['STATUS'  => 'success','data' => $rendimento]);

    }
    catch (\Exception $e){

      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Investimento_Corretora_Model']);

    }
  }
  
  public function delete($id)
  {
    if($this->rendimento->where('INAR_ID',$id)->count() == 0)
      return response()->json(['STATUS' => 'error', 'msg' => "Não foi encontrado registor com ID {$id}"]);


    try{
      return response()->json(['STATUS' => 'success','data' => $this->rendimento->find($id)->delete()]);
    
    }
    catch (\Exception $e){
      return response()->json(['STATUS' => 'error', 'msg' => 'Erro ao executar Investimento_Corretora_Model']);

    }
  }
}