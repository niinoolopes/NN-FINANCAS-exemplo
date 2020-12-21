<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\AtivoSplit as I_AtivoSplitModal;
use App\Rule\Investimento_CarteiraItem as I_CarteiraItem;

class AtivoSplitController extends Controller
{
  private $ativoSplit;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->ativoSplit = new I_AtivoSplitModal;
  }
  
  public function get() {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    
    try{
      $STATUS = 'success';
      $result = $this->ativoSplit->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/ativoSplit/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
  
  public function store(Request $request) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $ativoSplit = $this->ativoSplit;
      $ativoSplit->INAS_TIPO       = $request->get('INAS_TIPO');
      $ativoSplit->INAS_QUANTIDADE = $request->get('INAS_QUANTIDADE');
      $ativoSplit->INAS_DATA       = $request->get('INAS_DATA');
      $ativoSplit->INAS_STATUS     = $request->get('INAS_STATUS') ? 1 : 0;
      $ativoSplit->INAV_ID         = $request->get('INAV_ID');
      $ativoSplit->USUA_ID         = $_GET['usuario'];
      $ativoSplit->save();

      // --

      $STATUS = 'success';
      $result = $ativoSplit;

      $this->consolidaAtivo([
        'usuario' => $_GET['usuario'],
        'INCT_ID' => $_GET['INCT_ID'],
        'INAV_ID' => $request->get('INAV_ID'),
        'dataDe'  => date('Y-m-d', strtotime($request->get('INAS_DATA') . '-3 month')),
      ]);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/ativoSplit/store'
      ];
    }

    // --

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
  
  public function update($INAS_ID, Request $request) {
    $ativoSplit = $this->ativoSplit->find($INAS_ID);

    if($ativoSplit){
      try{
        // update
        $ativoSplit->INAS_TIPO       = $request->get('INAS_TIPO');
        $ativoSplit->INAS_QUANTIDADE = $request->get('INAS_QUANTIDADE');
        $ativoSplit->INAS_DATA       = $request->get('INAS_DATA');
        $ativoSplit->INAS_STATUS     = $request->get('INAS_STATUS') ? 1 : 0;
        $ativoSplit->INAV_ID         = $request->get('INAV_ID');
        $ativoSplit->USUA_ID         = $_GET['usuario'];
        $ativoSplit->save();
        
        // --
  
        $STATUS = 'success';
        $result = $ativoSplit;

        $this->consolidaAtivo([
          'usuario' => $_GET['usuario'],
          'INCT_ID' => $_GET['INCT_ID'],
          'INAV_ID' => $request->get('INAV_ID'),
          'dataDe'  => date('Y-m-d', strtotime($request->get('INAS_DATA') . '-3 month')),
        ]);
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/ativoSplit/update'
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
  
  public function delete($INAS_ID) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);
    if( !isset($_GET['INCT_ID'])) return response()->json(['STATUS' => 'error', 'msg' => 'o INCT_ID é obrigatório']);

    $ativoSplit = $this->ativoSplit->find($INAS_ID);

    if($ativoSplit){
      try{
        $INAS_DATA = $ativoSplit->INAS_DATA;
        $INAV_ID   = $ativoSplit->INAV_ID;
        
        $STATUS = 'success';
        $result = $ativoSplit->delete();

        $this->consolidaAtivo([
          'usuario' => $_GET['usuario'],
          'INCT_ID' => $_GET['INCT_ID'],
          'INAV_ID' => $INAV_ID,
          'dataDe'  => date('Y-m-d', strtotime($INAS_DATA . '-3 month')),
        ]);
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/ativoSplit/delete'
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