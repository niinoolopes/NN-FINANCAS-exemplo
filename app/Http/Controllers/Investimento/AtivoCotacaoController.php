<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\AtivoCotacao as I_AtivoCotacaoModal;
use App\Rule\Investimento_CarteiraItem as I_CarteiraItem;

class AtivoCotacaoController extends Controller
{
  private $ativoCotacao;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->ativoCotacao = new I_AtivoCotacaoModal;
  }
  
  public function get() {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      $STATUS = 'success';
      $result = $this->ativoCotacao->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/AtivoCotacao/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
  
  public function store(Request $request) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    if( !isset($_GET['INCT_ID'])) return response()->json(['STATUS' => 'error', 'msg' => 'o INCT_ID é obrigatório']);

    try{
      $ativoCotacao = $this->ativoCotacao;
      $ativoCotacao->INAC_VALOR   = $request->get('INAC_VALOR');
      $ativoCotacao->INAC_DATA    = $request->get('INAC_DATA');
      $ativoCotacao->INAC_STATUS  = $request->get('INAC_STATUS') ? 1 : 0;
      $ativoCotacao->INAV_ID      = $request->get('INAV_ID');
      $ativoCotacao->USUA_ID      = $_GET['usuario'];
      $ativoCotacao->save();

      // --

      $STATUS = 'success';
      $result = $ativoCotacao;

      $this->consolidaAtivo([
        'usuario' => $_GET['usuario'],
        'INCT_ID' => $_GET['INCT_ID'],
        'INAV_ID' => $request->get('INAV_ID'),
        'dataDe'  => date('Y-m-d', strtotime($request->get('INAC_DATA') . '-3 month')),
      ]);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/AtivoCotacao/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
  
  public function update($INAC_ID, Request $request) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    if( !isset($_GET['INCT_ID'])) return response()->json(['STATUS' => 'error', 'msg' => 'o INCT_ID é obrigatório']);

    $ativoCotacao = $this->ativoCotacao->find($INAC_ID);

    if($ativoCotacao){
      try{
        // update
        $ativoCotacao->INAC_VALOR   = $request->get('INAC_VALOR');
        $ativoCotacao->INAC_DATA    = $request->get('INAC_DATA');
        $ativoCotacao->INAC_STATUS  = $request->get('INAC_STATUS') ? 1 : 0;
        $ativoCotacao->INAV_ID      = $request->get('INAV_ID');
        $ativoCotacao->USUA_ID      = $_GET['usuario'];
        $ativoCotacao->save();
        
        // --
  
        $STATUS = 'success';
        $result = $ativoCotacao;
        
        $this->consolidaAtivo([
          'usuario' => $_GET['usuario'],
          'INCT_ID' => $_GET['INCT_ID'],
          'INAV_ID' => $request->get('INAV_ID'),
          'dataDe'  => date('Y-m-d', strtotime($request->get('INAC_DATA') . '-3 month')),
        ]);
      }
        catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/AtivoCotacao/update'
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
  
  public function delete($INAC_ID) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    if( !isset($_GET['INCT_ID'])) return response()->json(['STATUS' => 'error', 'msg' => 'o INCT_ID é obrigatório']);

    $ativoCotacao = $this->ativoCotacao->find($INAC_ID);

    if($ativoCotacao){
      try{
        $INAC_DATA = $ativoCotacao->INAC_DATA;
        $INAV_ID   = $ativoCotacao->INAV_ID;

        $STATUS = 'success';
        $result = $ativoCotacao->delete();

        $this->consolidaAtivo([
          'usuario' => $_GET['usuario'],
          'INCT_ID' => $_GET['INCT_ID'],
          'INAV_ID' => $INAV_ID,
          'dataDe'  => date('Y-m-d', strtotime($INAC_DATA . '-3 month')),
        ]);
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/AtivoCotacao/delete'
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

  public function consolidaAtivo($options) {
    if( !isset($options['usuario'])) die(json_encode(['STATUS' => 'error', 'msg' => 'o usuario é uma parametro obrigatório']));
    if( !isset($options['INCT_ID'])) die(json_encode(['STATUS' => 'error', 'msg' => 'o INCT_ID é uma parametro obrigatório']));


    $I_CarteiraItem = new I_CarteiraItem;
      
    $I_CarteiraItem->options = [
      'USUA_ID' => $options['usuario'],
      'INCT_ID' => $options['INCT_ID'],
      'dataAte' => date('Y-m-d'),
      'dataDe'  => isset($options['dataDe'])  ? $options['dataDe']  : false,
      'INAV_ID' => isset($options['INAV_ID']) ? $options['INAV_ID'] : false,
      'INTP_ID' => isset($options['INTP_ID']) ? $options['INTP_ID'] : false,
    ];

    return $I_CarteiraItem->exec();
  }
}