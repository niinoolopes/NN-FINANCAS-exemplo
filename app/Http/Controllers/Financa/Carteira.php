<?php

namespace App\Http\Controllers\Financa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Financa\Carteira as FinancaCarteira;

class Carteira extends Controller
{
  private $carteira;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->carteira   = new FinancaCarteira;
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
        'msg' => 'Erro ao executar Controller/Investimento/Carteira/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $carteira = $this->carteira;
      $carteira->FINC_DESCRICAO = $request->FINC_DESCRICAO;
      $carteira->FINC_PAINEL    = 0;
      $carteira->FINC_STATUS    = $request->FINC_STATUS;
      $carteira->save();

      $this->integrante->FINC_ID = $carteira->FINC_ID;
      $this->integrante->USUA_ID = $_GET['usuario'];
      $this->integrante->save();

      // --

      $STATUS = 'success';
      $result = $carteira;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Financa/Carteira/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($FINC_ID, Request $request)
  {
    $carteira = $this->carteira->find($FINC_ID);
    
    if($carteira){

      try{
        $dados = $request->all();

        // INATIVA AS CARTEIRAS QUANDO ENVIADO KEY 'PAINEL'
        if( key_exists('FINC_PAINEL', $dados) && $dados['FINC_PAINEL'] == 1){

          $arrCarteira = $this->carteira->get($_GET);

          foreach ($arrCarteira as $value) {
            $cart = $this->carteira->find($value->FINC_ID);
            $cart->FINC_PAINEL = 0;
            $cart->save();
          }
        }

        // update
        $carteira->FINC_DESCRICAO = $request->get('FINC_DESCRICAO');
        $carteira->FINC_PAINEL    = $request->get('FINC_PAINEL') == 1 ? 1 : 0 ;
        $carteira->FINC_STATUS    = $request->get('FINC_STATUS') == 1 ? 1 : 0 ;
        $carteira->save();

        // --
  
        $STATUS = 'success';
        $result = $carteira;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Financa/Carteira/update'
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
  
  public function delete($FINC_ID)
  {
    $carteira = $this->carteira->find($FINC_ID);

    if($carteira){
    
      try{
        
        $STATUS = 'success';
        $result = $carteira->delete();
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
        'msg' => 'o ID não existe.'
      ];
    }

    // --

    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }
}

