<?php

namespace App\Http\Controllers\Financa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Financa\Categoria as FinancaCategoria;
// use Illuminate\Support\Str;

class Categoria extends Controller
{
  private $categoria ;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->categoria = new FinancaCategoria;
  }

  public function get()
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      $STATUS = 'success';
      $result   = $this->categoria->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Financa/Categoria/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      
      $this->categoria->FICT_DESCRICAO = $request->FICT_DESCRICAO;
      $this->categoria->FICT_STATUS    = $request->FICT_STATUS;
      // $this->categoria->FICT_SLUG      = Str::slug($request->FICT_DESCRICAO);
      $this->categoria->FICT_OBS       = $request->FICT_OBS;
      $this->categoria->FICT_ADD_COFRE = $request->FICT_ADD_COFRE;
      $this->categoria->FIGP_ID        = $request->FIGP_ID;
      $this->categoria->save();

      // --

      $STATUS = 'success';
      $result = $this->categoria;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Financa/Categoria/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($FICT_ID, Request $request)
  {
    $categoria = $this->categoria->find($FICT_ID);
    
    if($categoria){

      try{
        $categoria->FICT_DESCRICAO = $request->FICT_DESCRICAO;
        $categoria->FICT_STATUS    = $request->FICT_STATUS;
        // $categoria->FICT_SLUG      = Str::slug($request->FICT_DESCRICAO);
        $categoria->FICT_OBS       = $request->FICT_OBS;
        $categoria->FICT_ADD_COFRE = $request->FICT_ADD_COFRE;
        $categoria->FIGP_ID        = $request->FIGP_ID;
        $categoria->save();

        // --
  
        $STATUS = 'success';
        $result = $categoria;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Financa/Categoria/update'
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
    
  public function delete($FICT_ID)
  {
    $categoria = $this->categoria->find($FICT_ID);
    
    if($categoria){

      try{
        
        $STATUS = 'success';
        $result = $categoria->delete();

      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Financa/Carteira/delete'
        ];
      }

    } else {
          
      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'o FINC_ID não existe.'
      ];
    }

    // --

    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }
}

