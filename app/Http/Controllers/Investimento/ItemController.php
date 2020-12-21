<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\Item  as I_ItemModel;
use App\Model\Investimento\Ativo as I_AtivoModel;

class ItemController extends Controller
{

  private $result;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->item   = new I_ItemModel;
    $this->ativo  = new I_AtivoModel;
  }

  
  public function get($id = false)
  {
    if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);
    
    $this->result = $this->item->get($_GET, $id);
    
    try{
      return response()->json(['STATUS'  => 'success','data' => $this->result]);

    }
    catch (\Exception $e){
      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Model']);

    }
  }

  public function store(Request $request)
  {
    // "INIT_ID" => "novo"
    // "INTP_ID" => "1"
    // "INAT_ID" => "1"


    // "INIT_NEGOCIACAO" => ""
    // "INIT_CV" => "C"
    // "INIT_MERCADO" => "FRACIONADO"
    // "INIT_PRECO_UNICO" => "1255.82"
    // "INIT_PRECO_TOTAL" => "1255.82"
    // "INIT_DC" => ""
    // "INIT_STATUS" => "1"
    // "INOD_ID" => "1"
    // "INAV_ID" => "7"

    // "INIT_COTAS" => "1"
    // "usuario" => "1"

    try{
      $this->item->INIT_NEGOCIACAO  = $request->get('INIT_NEGOCIACAO');
      $this->item->INIT_CV          = $request->get('INIT_CV');
      $this->item->INIT_MERCADO     = $request->get('INIT_MERCADO');
      $this->item->INIT_COTAS       = $request->get('INIT_COTAS');
      $this->item->INIT_PRECO_UNICO = $request->get('INIT_PRECO_UNICO');
      $this->item->INIT_PRECO_TOTAL = $request->get('INIT_PRECO_TOTAL');
      $this->item->INIT_DC          = $request->get('INIT_DC');
      $this->item->INIT_STATUS      = $request->get('INIT_STATUS');
      $this->item->INOD_ID          = $request->get('INOD_ID');
      $this->item->INAV_ID          = $request->get('INAV_ID');
      $this->item->save();

      // $this->apuraAtivo($this->item->INAV_ID);

      return response()->json(['STATUS'  => 'success','data' => $this->item]);

    }
    catch (\Exception $e){
      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Model']);

    }
  }
  
  public function update($id, Request $request)
  {
    $item = $this->item->find($id);

    if($item){
      
      try{
        $item->INIT_NEGOCIACAO  = $request->get('INIT_NEGOCIACAO');
        $item->INIT_CV          = $request->get('INIT_CV');
        $item->INIT_MERCADO     = $request->get('INIT_MERCADO');
        $item->INIT_COTAS       = $request->get('INIT_COTAS');
        $item->INIT_PRECO_UNICO = $request->get('INIT_PRECO_UNICO');
        $item->INIT_PRECO_TOTAL = $request->get('INIT_PRECO_TOTAL');
        $item->INIT_DC          = $request->get('INIT_DC');
        $item->INIT_STATUS      = $request->get('INIT_STATUS');
        $item->INOD_ID          = $request->get('INOD_ID');
        $item->INAV_ID          = $request->get('INAV_ID');
  
        $item->save();

        // --
        // $this->apuraAtivo($item->INAV_ID);
  
        $STATUS = 'success';
        $result = $item;

      }
      catch (\Exception $e){
  
        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/item/update'
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
  
  public function delete($id)
  {
    $item = $this->item->find($id);
    
    if($item){

      try{
        
        $STATUS = 'success';
        $result = $item->delete();

      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Item/delete'
        ];
      }

    } else {
          
      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'o INIT_ID não existe.'
      ];
    }

    // --

    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }



  // ---


  private function apuraAtivo($INAV_ID = false)
  {

    $get['INAV_ID'] = $INAV_ID;
    $get['order'] = 'asc';
    $arrResult = $this->item->get($get);

    // agrupo registros por ID
    foreach ($arrResult as $key => $value) {
      $key_ativo = "ID_{$value['INAV_ID']}";
      $arrResult[$key_ativo][] = $value;
      unset($arrResult[$key]);
    } 

    foreach ($arrResult as $key => $arrID) {
      $INAV_ID = reset($arrID)['INAV_ID'];

      $INOD_DATAs = array_filter($arrID, function($val){ return $val['INIT_CV'] == 'C' || $val['INIT_CV'] == 'V'; });
      $INOD_DATAs = array_map(function($val){ return $val['INOD_DATA']; }, $INOD_DATAs);
      $INOD_DATAs = array_unique($INOD_DATAs);
      sort($INOD_DATAs);
      
      // RENDA FIXA
      if(reset($arrID)['INTP_ID'] == 1){
        $arrDetalhes = [
          'VALOR_APLICADO'       => 0,
          'VALOR_TOTAL'          => 0,
          'QUANTIDADE'           => 0,
          'RENDIMENTO'           => 0,
          'RENDIMENTO_PERCENTUAL' => 0,
          'LIQUIDEZ'             => reset($arrID)['INAV_LIQUIDEZ'],
          'DATA_PRIMEIRA_COMPRA' => reset($INOD_DATAs),
          'DATA_ULTIMA_COMPRA'   => end($INOD_DATAs),
          
          'VALOR_IR'             => 0,
          'VALOR_LIQUIDO'        => 0,
          'RENTABILIDADE'        => 0,
        ];
      } 
      // RENDA VARIAVEL
      else {
        $arrDetalhes = [
          'VALOR_APLICADO'        => 0,
          'VALOR_TOTAL'           => 0,
          'QUANTIDADE'            => 0,
          'RENDIMENTO'            => 0,
          'RENDIMENTO_PERCENTUAL' => 0,
          'LIQUIDEZ'             => reset($arrID)['INAV_LIQUIDEZ'],
          'DATA_PRIMEIRA_COMPRA' => reset($INOD_DATAs),
          'DATA_ULTIMA_COMPRA'   => end($INOD_DATAs),

          'VALOR_ATUAL'          => 0,
          'DIVIDENDO'            => 0,
          'JSCP'                 => 0,
          'PM'                   => 0,
          'PRECO_ATUAL'          => 90,
        ];
      }


      foreach ($arrID as $key => $item) {

        // RENDA FIXA
        if($item['INTP_ID'] == 1){
          // QUANDO FOR Compra
          if($item['INIT_CV'] == 'C') {
            $num_1 = $arrDetalhes['VALOR_APLICADO'] + $item['INIT_PRECO_TOTAL'];
            $num_2 = $arrDetalhes['QUANTIDADE']     + $item['INIT_COTAS'];
            $num_3 = $arrDetalhes['VALOR_TOTAL']    + $item['INIT_PRECO_TOTAL'];

            $arrDetalhes['VALOR_APLICADO'] = number_format($num_1, 2, '.', '');
            $arrDetalhes['QUANTIDADE']     = number_format($num_2, 2, '.', '');
            $arrDetalhes['VALOR_TOTAL']    = number_format($num_3, 2, '.', '');
            
          // QUANDO FOR Venda
          }else  if($item['INIT_CV'] == 'V') {
            $num_1 = $arrDetalhes['VALOR_APLICADO'] - $item['INIT_PRECO_TOTAL'];
            $num_2 = $arrDetalhes['QUANTIDADE']     - $item['INIT_COTAS'];
            $num_3 = $arrDetalhes['VALOR_TOTAL']    - $item['INIT_PRECO_TOTAL'];

            $arrDetalhes['VALOR_APLICADO'] = number_format($num_1, 2, '.', '');
            $arrDetalhes['QUANTIDADE']     = number_format($num_2, 2, '.', '');
            $arrDetalhes['VALOR_TOTAL']    = number_format($num_3, 2, '.', '');

          // QUANDO FOR Rendimento
          }else  if($item['INIT_CV'] == 'R') {
            $num_1 = $arrDetalhes['VALOR_TOTAL'] + $item['INIT_PRECO_TOTAL'];
            $num_2 = $arrDetalhes['RENDIMENTO']  + $item['INIT_PRECO_TOTAL'];
            $num_3 = $num_2 / $item['VALOR_APLICADO'];

            $arrDetalhes['VALOR_TOTAL']            = number_format($num_1, 2, '.', '');
            $arrDetalhes['RENDIMENTO']             = number_format($num_2, 2, '.', '');
            $arrDetalhes['RENDIMENTO_PERCENTUAL']  = number_format($num_3, 2, '.', '');
          };
        } 

        // RENDA VARIAVEL
        else {
          // QUANDO FOR Compra
          if($item['INIT_CV'] == 'C') {
            $num_1 = $arrDetalhes['VALOR_APLICADO'] + $item['INIT_PRECO_TOTAL'];
            $num_2 = $arrDetalhes['QUANTIDADE']     + $item['INIT_COTAS'];
            $num_3 = $arrDetalhes['VALOR_TOTAL']    + $item['INIT_PRECO_TOTAL'];

            $arrDetalhes['VALOR_APLICADO'] = number_format($num_1, 2, '.', '');
            $arrDetalhes['QUANTIDADE']     = number_format($num_2, 2, '.', '');
            $arrDetalhes['VALOR_TOTAL']    = number_format($num_3, 2, '.', '');

          // QUANDO FOR Venda
          }else  if($item['INIT_CV'] == 'V') {
            $num_1 = $arrDetalhes['VALOR_APLICADO'] - $item['INIT_PRECO_TOTAL'];
            $num_2 = $arrDetalhes['QUANTIDADE']     - $item['INIT_COTAS'];
            $num_3 = $arrDetalhes['VALOR_TOTAL']    - $item['INIT_PRECO_TOTAL'];

            $arrDetalhes['VALOR_APLICADO'] = number_format($num_1, 2, '.', '');
            $arrDetalhes['QUANTIDADE']     = number_format($num_2, 2, '.', '');
            $arrDetalhes['VALOR_TOTAL']    = number_format($num_3, 2, '.', '');

          // QUANDO FOR Dividendo
          }else  if($item['INIT_CV'] == 'D') {
            $num_1 = $arrDetalhes['DIVIDENDO']   + $item['INIT_PRECO_TOTAL'];
            $num_2 = $arrDetalhes['VALOR_TOTAL'] + $item['INIT_PRECO_TOTAL'];

            $arrDetalhes['DIVIDENDO']   = number_format($num_1, 2, '.', '');
            $arrDetalhes['VALOR_TOTAL'] = number_format($num_2, 2, '.', '');

          // QUANDO FOR juros de capital
          }else  if($item['INIT_CV'] == 'J') {
            $num_1 = $arrDetalhes['JSCP']        + $item['INIT_PRECO_TOTAL'];
            $num_2 = $arrDetalhes['VALOR_TOTAL'] + $item['INIT_PRECO_TOTAL'];

            $arrDetalhes['JSCP']        = number_format($num_1, 2, '.', '');
            $arrDetalhes['VALOR_TOTAL'] = number_format($num_2, 2, '.', '');
          }

          $num = $arrDetalhes['VALOR_APLICADO'] / $arrDetalhes['QUANTIDADE'];
          $arrDetalhes['PM'] = number_format($num, 2 ,'.', '');

          $num = 0;
          if($arrDetalhes['PRECO_ATUAL']){
            $ganho_por_cota = $arrDetalhes['PRECO_ATUAL'] - $arrDetalhes['PM'];
            $num = $ganho_por_cota * $arrDetalhes['QUANTIDADE']; }

          $num += $arrDetalhes['DIVIDENDO'] + $arrDetalhes['JSCP'];
          $arrDetalhes['RENDIMENTO'] = number_format($num, 2 ,'.', '');

          $num = $num / $arrDetalhes['VALOR_APLICADO'];
          $num = $num * 100;
          $arrDetalhes['RENDIMENTO_PERCENTUAL'] = number_format($num, 2 ,'.', '');
        }
      }

      $INAV = $this->ativo->find($INAV_ID);
      $INAV->INAV_DETALHE = json_encode($arrDetalhes);
      $INAV->save();
    }
  }
}