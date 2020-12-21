<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\AtivoRendimento as I_AtivoRendimento;
use App\Rule\Investimento_CarteiraItem as I_CarteiraItem;

class AtivoRendimentoController extends Controller
{

  public function __construct()
  {

    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->rendimento = new I_AtivoRendimento;
  }

  public function get() {
    if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);

    $this->rendimento = new I_AtivoRendimento;

    try{
      
      $STATUS = 'success';
      $result = $this->rendimento->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Rendimento/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request) {
    if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);

    try{
      // insert
      $rendimento = $this->rendimento;
      $rendimento->INAR_TIPO   = $request->get('INAR_TIPO');
      $rendimento->INAR_VALOR  = $request->get('INAR_VALOR');
      $rendimento->INAR_DATA   = $request->get('INAR_DATA');
      $rendimento->INAR_STATUS = $request->get('INAR_STATUS');
      $rendimento->INAV_ID     = $request->get('INAV_ID');
      $rendimento->INCR_ID     = $request->get('INCR_ID');
      $rendimento->INCT_ID     = $request->get('INCT_ID');
      $rendimento->save();

      // --

      $STATUS = 'success';
      $result = $rendimento;
      
      $this->consolidaAtivo([
        'usuario' => $_GET['usuario'],
        'INCT_ID' => $_GET['INCT_ID'],
        'INAV_ID' => $request->get('INAV_ID'),
        'dataDe'  => date('Y-m-d', strtotime($request->get('INAR_DATA') . '-3 month')),
      ]);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Rendimento/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function update($INAR_ID, Request $request) {
    if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);

    $rendimento = $this->rendimento->find($INAR_ID);

    if($rendimento){
      try{
        // update
        $rendimento->INAR_TIPO   = $request->get('INAR_TIPO');
        $rendimento->INAR_VALOR  = $request->get('INAR_VALOR');
        $rendimento->INAR_DATA   = $request->get('INAR_DATA');
        $rendimento->INAR_STATUS = $request->get('INAR_STATUS') == 1 ? 1 : 0 ;
        $rendimento->INAV_ID     = $request->get('INAV_ID');
        $rendimento->INCR_ID     = $request->get('INCR_ID');
        $rendimento->INCT_ID     = $request->get('INCT_ID');
        $rendimento->save();

        // --
  
        $STATUS = 'success';
        $result = $rendimento;
              
        $this->consolidaAtivo([
          'usuario' => $_GET['usuario'],
          'INCT_ID' => $_GET['INCT_ID'],
          'INAV_ID' => $request->get('INAV_ID'),
          'dataDe'  => date('Y-m-d', strtotime($request->get('INAR_DATA') . '-3 month')),
        ]);
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Rendimento/update'
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
  
  public function delete($INAR_ID) {
    if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);

    $rendimento = $this->rendimento->find($INAR_ID);

    if($rendimento){
      try{
        $INAR_DATA = $rendimento->INAR_DATA;
        $INAV_ID   = $rendimento->INAV_ID;
        
        $STATUS = 'success';
        $result = $rendimento->delete();

        $this->consolidaAtivo([
          'usuario' => $_GET['usuario'],
          'INCT_ID' => $_GET['INCT_ID'],
          'INAV_ID' => $INAV_ID,
          'dataDe'  => date('Y-m-d', strtotime($INAR_DATA . '-3 month')),
        ]);
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Rendimento/delete'
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