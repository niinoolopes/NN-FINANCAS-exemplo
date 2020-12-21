<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Investimento\Item            as I_ItemModel;
use App\Model\Investimento\Ordem           as I_OrdemModel;
use App\Model\Investimento\OrdemTaxas      as I_OrdemTaxasModel;
use App\Model\Investimento\AtivoRendimento as I_RendimentoModel;

class OrdemController extends Controller
{
  private $ordem;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->result     = [];
    $this->item       = new I_ItemModel;
    $this->ordem      = new I_OrdemModel;
    $this->taxas      = new I_OrdemTaxasModel;
    $this->rendimento = new I_RendimentoModel;
  }
  
  public function get()
  {
    if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);
    
    try{
      $STATUS = 'success';
      $result = $this->ordem->get($_GET);
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Ordem/get'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  public function store(Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    try{
      $taxas = $this->taxas;
      $taxas["INTX_VALOR_LIQUIDO_OPERACOES"] = $request->get("INTX_VALOR_LIQUIDO_OPERACOES");
      $taxas["INTX_TAXA_LIQUIDACAO"]         = $request->get("INTX_TAXA_LIQUIDACAO");
      $taxas["INTX_TAXA_REGISTRO"]           = $request->get("INTX_TAXA_REGISTRO");
      $taxas["INTX_TAXA_TERMO_OPERACOES"]    = $request->get("INTX_TAXA_TERMO_OPERACOES");
      $taxas["INTX_TAXA_ANA"]                = $request->get("INTX_TAXA_ANA");
      $taxas["INTX_EMOLUMENTOS"]             = $request->get("INTX_EMOLUMENTOS");
      $taxas["INTX_TAXA_OPERACIONAL"]        = $request->get("INTX_TAXA_OPERACIONAL");
      $taxas["INTX_EXECUCAO"]                = $request->get("INTX_EXECUCAO");
      $taxas["INTX_TAXA_CUSTODIA"]           = $request->get("INTX_TAXA_CUSTODIA");
      $taxas["INTX_IMPOSTOS"]                = $request->get("INTX_IMPOSTOS");
      $taxas["INTX_IRRF_OPERACOES"]          = $request->get("INTX_IRRF_OPERACOES");
      $taxas["INTX_OUTRO"]                   = $request->get("INTX_OUTRO");
      $taxas["INTX_STATUS"]                  = $request->get("INTX_STATUS");
      $taxas->save();

      $ordem = $this->ordem;
      $ordem["INOD_DESCRICAO"] = $request->get("INOD_DESCRICAO");
      $ordem["INOD_DATA"]      = $request->get("INOD_DATA");
      $ordem["INOD_STATUS"]    = $request->get("INOD_STATUS");
      $ordem["INTX_ID"]        = $taxas->INTX_ID;
      $ordem["INCR_ID"]        = $request->get("INCR_ID");
      $ordem["INCT_ID"]        = $request->get("INCT_ID");
      $ordem->save();

      // --
      
      $STATUS = 'success';
      $result = [
        "INTX_VALOR_LIQUIDO_OPERACOES" => $taxas->INTX_VALOR_LIQUIDO_OPERACOES,
        "INTX_TAXA_LIQUIDACAO"         => $taxas->INTX_TAXA_LIQUIDACAO,
        "INTX_TAXA_REGISTRO"           => $taxas->INTX_TAXA_REGISTRO,
        "INTX_TAXA_TERMO_OPERACOES"    => $taxas->INTX_TAXA_TERMO_OPERACOES,
        "INTX_TAXA_ANA"                => $taxas->INTX_TAXA_ANA,
        "INTX_EMOLUMENTOS"             => $taxas->INTX_EMOLUMENTOS,
        "INTX_TAXA_OPERACIONAL"        => $taxas->INTX_TAXA_OPERACIONAL,
        "INTX_EXECUCAO"                => $taxas->INTX_EXECUCAO,
        "INTX_TAXA_CUSTODIA"           => $taxas->INTX_TAXA_CUSTODIA,
        "INTX_IMPOSTOS"                => $taxas->INTX_IMPOSTOS,
        "INTX_IRRF_OPERACOES"          => $taxas->INTX_IRRF_OPERACOES,
        "INTX_OUTRO"                   => $taxas->INTX_OUTRO,
        "INTX_STATUS"                  => $taxas->INTX_STATUS,
        "INTX_ID"                      => $ordem->INTX_ID,
        "INOD_DESCRICAO"               => $ordem->INOD_DESCRICAO,
        "INOD_DATA"                    => $ordem->INOD_DATA,
        "INOD_STATUS"                  => $ordem->INOD_STATUS,
        "INOD_ID"                      => $ordem->INOD_ID,
        "INCR_ID"                      => $ordem->INCR_ID,
        "INCT_ID"                      => $ordem->INCT_ID,
      ];
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'Erro ao executar Controller/Investimento/Ordem/store'
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }
  
  public function update($INOD_ID, Request $request)
  {
    $arrPost = $request->all();

    if( !isset($arrPost['INTX_ID'] )) return response()->json(['STATUS' => 'error', 'msg' => 'o INTX_ID é obrigatório']);
    if( !isset($arrPost['INOD_ID'] )) return response()->json(['STATUS' => 'error', 'msg' => 'o INOD_ID é obrigatório']);


    $ordem = $this->ordem->find($INOD_ID);
    $taxas = $this->taxas->find($arrPost['INTX_ID']);

    if($ordem && $taxas){

      try{
        // update
        $taxas->INTX_VALOR_LIQUIDO_OPERACOES = $request->get("INTX_VALOR_LIQUIDO_OPERACOES");
        $taxas->INTX_TAXA_LIQUIDACAO         = $request->get("INTX_TAXA_LIQUIDACAO");
        $taxas->INTX_TAXA_REGISTRO           = $request->get("INTX_TAXA_REGISTRO");
        $taxas->INTX_TAXA_TERMO_OPERACOES    = $request->get("INTX_TAXA_TERMO_OPERACOES");
        $taxas->INTX_TAXA_ANA                = $request->get("INTX_TAXA_ANA");
        $taxas->INTX_EMOLUMENTOS             = $request->get("INTX_EMOLUMENTOS");
        $taxas->INTX_TAXA_OPERACIONAL        = $request->get("INTX_TAXA_OPERACIONAL");
        $taxas->INTX_EXECUCAO                = $request->get("INTX_EXECUCAO");
        $taxas->INTX_TAXA_CUSTODIA           = $request->get("INTX_TAXA_CUSTODIA");
        $taxas->INTX_IMPOSTOS                = $request->get("INTX_IMPOSTOS");
        $taxas->INTX_IRRF_OPERACOES          = $request->get("INTX_IRRF_OPERACOES");
        $taxas->INTX_OUTRO                   = $request->get("INTX_OUTRO");
        $taxas->INTX_STATUS                  = $request->get("INTX_STATUS");
        $taxas->save();
        
        // update
        $ordem->INOD_DESCRICAO = $request->get("INOD_DESCRICAO");
        $ordem->INOD_DATA      = $request->get("INOD_DATA");
        $ordem->INOD_STATUS    = $request->get("INOD_STATUS");
        $ordem->INCR_ID        = $request->get("INCR_ID");
        $ordem->INCT_ID        = $request->get("INCT_ID");
        $ordem->save();
    
        $STATUS = 'success';
        $options['usuario'] = $_GET['usuario'];
        $options['INOD_ID'] = $ordem->INOD_ID;
        $result = $this->ordem->get($options)[0];
      }
      catch (\Exception $e){
  
        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Ordem/update'
        ];
      }

    } 

    if(!$ordem){
      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'o INOD_ID não existe.'
      ];
    }
    if(!$taxas){
      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'o INTX_ID não existe.'
      ];
    }

    // --

    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }
  
  public function delete($INOD_ID) {
    $ordem = $this->ordem->find($INOD_ID);
    // dd($ordem);

    if($ordem){
    
      try{
        $INCR_ID = $ordem->INCR_ID;
        $INCT_ID = $ordem->INCT_ID;

        // delete taxas
        $taxas = $this->taxas->find($ordem->INTX_ID);
        if($taxas) $taxas->delete();
        

        // delete rendimentos
        $items = $this->item->where('INOD_ID', $INOD_ID)->get()->toArray();
        foreach ($items as $item) {
          $this->rendimento
                ->where('INAV_ID', $item['INAV_ID'])
                ->where('INCR_ID', $INCR_ID)
                ->where('INCT_ID', $INCT_ID)
                ->delete();
        }
        
        // delete items 
        $this->item->where('INOD_ID', $INOD_ID)->delete(); 
        

        $STATUS = 'success';
        $result = $ordem->delete();
        $result = true;
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Investimento/Ordem/delete'
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