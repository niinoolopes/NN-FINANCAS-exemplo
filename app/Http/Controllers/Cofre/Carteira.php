<?php

namespace App\Http\Controllers\Cofre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Cofre\Carteira as CofreCarteira;
use App\Model\Cofre\Integrante as CofreIntegrante;

class Carteira extends Controller
{
  private $carteira;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->carteira   = new CofreCarteira;
  }

  public function get() {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      $STATUS = 'success';
      $result = $this->carteira->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Cofre/Carteira/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      $carteira = new CofreCarteira;
      $carteira->COCT_DESCRICAO = $request->COCT_DESCRICAO;
      $carteira->COCT_STATUS    = $request->COCT_STATUS;
      $carteira->COCT_PAINEL    = 0;
      $carteira->save();
  
      $integrante = new CofreIntegrante;
      $integrante->COCT_ID = $carteira->COCT_ID;
      $integrante->USUA_ID = $_GET['usuario'];
      $integrante->save();

      // --

      $STATUS = 'success';
      $result = $carteira;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Cofre/Carteira/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($COCT_ID, Request $request)
  {
    $carteira = $this->carteira->find($COCT_ID);
    
    if($carteira){

      try{
        $dados = $request->all();

        // INATIVA AS CARTEIRAS QUANDO ENVIADO KEY 'PAINEL'
        if( key_exists('COCT_PAINEL', $dados) && $dados['COCT_PAINEL'] == 1){

          $arrCarteira = $this->carteira->get($_GET);

          foreach ($arrCarteira as $value) {
            $cart = $this->carteira->find($value->COCT_ID);
            $cart->COCT_PAINEL = 0;
            $cart->save();
          }
        }

        // update
        $carteira->COCT_DESCRICAO = $request->get('COCT_DESCRICAO');
        $carteira->COCT_PAINEL    = $request->get('COCT_PAINEL') == 1 ? 1 : 0 ;
        $carteira->COCT_STATUS    = $request->get('COCT_STATUS') == 1 ? 1 : 0 ;
        $carteira->save();

        // --
  
        $STATUS = 'success';
        $result = $carteira;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Cofre/Carteira/update'
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
  
  public function delete($COCT_ID)
  {
    $carteira = $this->carteira->find($COCT_ID);

    if($carteira){
    
      try{
        
        $STATUS = 'success';
        $result = $carteira->delete();
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Cofre/Carteira/delete'
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

