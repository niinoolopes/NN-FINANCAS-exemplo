<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\Carteira   as I_Carteira;
use App\Model\Investimento\Integrante as I_Integrante;
use App\Rule\Investimento_CarteiraItem as I_CarteiraItem;

class CarteiraController extends Controller
{
  private $carteira;

  public function __construct() {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->carteira   = new I_Carteira;
    $this->integrante = new I_Integrante;
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
      $carteira->INCT_DESCRICAO = $request->get('INCT_DESCRICAO');
      $carteira->INCT_PAINEL    = $request->get('INCT_PAINEL');
      $carteira->INCT_STATUS    = $request->get('INCT_STATUS');
      $carteira->save();

      $this->integrante->INCT_ID = $carteira->INCT_ID;
      $this->integrante->USUA_ID = $_GET['usuario'];
      $this->integrante->save();

      // --

      $STATUS = 'success';
      $result = $carteira;
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Carteira/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($INCT_ID, Request $request)
  {
    $carteira = $this->carteira->find($INCT_ID);

    if($carteira){

      try{
        $dados = $request->all();

        // INATIVA AS CARTEIRAS QUANDO ENVIADO KEY 'PAINEL'
        if( key_exists('INCT_PAINEL', $dados) && $dados['INCT_PAINEL'] == 1){

          $arrCarteira = $this->carteira->get($_GET);
          
          foreach ($arrCarteira as $value) {
            $cart = $this->carteira->find($value->INCT_ID);
            $cart->INCT_PAINEL = 0;
            $cart->save();
          }
        }

        // update
        $carteira->INCT_DESCRICAO = $request->get('INCT_DESCRICAO');
        $carteira->INCT_PAINEL    = $request->get('INCT_PAINEL') == 1 ? 1 : 0 ;
        $carteira->INCT_STATUS    = $request->get('INCT_STATUS') == 1 ? 1 : 0 ;
        $carteira->save();

        // --
  
        $STATUS = 'success';
        $result = $carteira;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Carteira/update'
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
  
  public function delete($INCT_ID)
  {
    $carteira = $this->carteira->find($INCT_ID);

    if($carteira){
    
      try{
        
        $STATUS = 'success';
        $result = $carteira->delete();
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Carteira/delete'
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

  public function consolida()
  {
    if( !isset($_GET['INCT_ID'])) die(json_encode(['STATUS' => 'error', 'msg' => 'o INCT_ID é uma parametro obrigatório']));


      $I_CarteiraItem = new I_CarteiraItem;
      
      $I_CarteiraItem->options = [
        'USUA_ID' => $_GET['usuario'],
        'INCT_ID' => $_GET['INCT_ID'],
        'dataAte' => $_GET['dataAte'],
        'dataDe'  => isset($_GET['dataDe'])  ? $_GET['dataDe'] : false,
        'INAV_ID' => isset($_GET['INAV_ID']) ? $_GET['INAV_ID'] : false,
        'INTP_ID' => isset($_GET['INTP_ID']) ? $_GET['INTP_ID'] : false,
      ];

      $STATUS = 'success';
      $result = $I_CarteiraItem->exec();
      
    try{

    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result = [
        'msg' => 'Erro ao executar Controller/Investimento/Carteira/delete'
      ];
    }


    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }
}