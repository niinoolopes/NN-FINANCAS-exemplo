<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use App\Model\Investimento\Busca           as I_BuscaModel;
use App\Model\Investimento\Item            as I_ItemModel;
// use App\Model\Investimento\Ordem           as I_OrdemModel;
use App\Model\Investimento\AtivoRendimento as I_AtivoRendimento;
use App\Model\Investimento\AtivoCotacao    as I_AtivoCotacao;
use App\Model\Investimento\AtivoSplit      as I_AtivoSplit;
use App\Model\Investimento\Ordem           as I_Ordem;

class BuscaController extends Controller
{

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->busca           = new I_BuscaModel;
    $this->item            = new I_ItemModel;
    $this->ativoRendimento = new I_AtivoRendimento;
    $this->ativoCotacao    = new I_AtivoCotacao;
    $this->ativoSplit      = new I_AtivoSplit;
    $this->ordem           = new I_Ordem;
  }

  public function get($tipo) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é uma parametro obrigatório']);


    // tipo original
    $tipoOriginal = $tipo;

    // tratamento para chamada do metodo
    $tipo   = str_replace('-', ' ',$tipo);
    $tipo   = ucwords($tipo);
    $tipo   = str_replace(' ', '',$tipo);
    $method = "get{$tipo}";


    if( !method_exists($this, $method) ){
      return response()->json(
        [
        'STATUS' => 'error', 
        'msg' => "a busca informada '{$tipoOriginal}' não existe."
        ]
      );
    }
    
    $result = $this->$method();
    try{
      $STATUS = 'success';
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => "Erro ao executar Controller/Investimento/Busca/{$tipoOriginal}"
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  // --

  // PAGES

  private function getComponente() {
    // $aaa['aa'] = 'aa';
    // $aaa['bb'] = 'bb';
    // $bbb['aa'] = 'AA';
    
    // $aaa = array_merge(
    //   $aaa,
    //   $bbb
    // );

    // dd($aaa);


    if(!isset($_GET['INCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => "o INCT_ID é uma parametro obrigatório"]) );
    if(!isset($_GET['retorno'])) die( json_encode(['STATUS' => 'error', 'msg' => "o 'retorno' é uma parametro obrigatório"]) );

    // BUSCA ITEMS
    $options = [];
    $options['usuario'] = $_GET['usuario'];
    $options['INCT_ID'] = $_GET['INCT_ID'];
    $options['dataAte'] = isset($_GET['dataAte']) ? $_GET['dataAte'] : date('Y-m-d');
    $options['INIT_STATUS'] = 1;
    
    if(isset($_GET['INTP_ID'])) $options['INTP_ID'] = $_GET['INTP_ID'];
    // if(isset($_GET['INCT_ID'])) $options['usuario'] = $_GET['INCT_ID'];
    // if(isset($_GET['INCT_ID'])) $options['usuario'] = $_GET['INCT_ID'];
    $items = $this->item->get($options);


    $items = $this->analiseItensBase($items, $_GET['dataAte']);


    // $items = array_values( array_filter( $items, function($val) { return $val->COTAS > 0; }) );
    // --
    
    $arrRetorno = [];

    if( !isset($_GET['retorno']) || (isset($_GET['retorno']) && $_GET['retorno'] == 'carteiraSaldo') )
      $arrRetorno['carteiraSaldo'] = $this->itemsCarteiraSaldo($items);


    if( !isset($_GET['retorno']) || (isset($_GET['retorno']) && $_GET['retorno'] == 'carteiraAnaliseTipo') )
      $arrRetorno['carteiraAnaliseTipo'] = $this->itemsCarteiraAnaliseTipo($items);
    

    if( !isset($_GET['retorno']) || (isset($_GET['retorno']) && $_GET['retorno'] == 'carteiraAnaliseTipoAtivo') )
      $arrRetorno['carteiraAnaliseTipoAtivo'] = $this->itemsCarteiraAnaliseTipoAtivo($items);


    if( !isset($_GET['retorno']) || (isset($_GET['retorno']) && $_GET['retorno'] == 'carteiraAnaliseAtivo') )
      $arrRetorno['carteiraAnaliseAtivo'] = $this->itemsCarteiraAnaliseAtivo($items);
      
    if( !isset($_GET['retorno']) || (isset($_GET['retorno']) && $_GET['retorno'] == 'carteiraComposicao') )
      $arrRetorno['carteiraComposicao'] = $this->itemsCarteiraComposicao($items);


    // dd($arrRetorno['carteiraComposicao']);

    return $arrRetorno;
  }
  private function itemsCarteiraSaldo($items) {
    $tmpRetorno = [
      'TOTAL'            => 0,
      'BRUTO'            => 0,
      'TOTAL_RENDIMENTO' => 0,
      'TOTAL_DIVIDENDO'  => 0,
      'TOTAL_JSCP'       => 0,
    ];

    if(count($items) == 0) return $tmpRetorno;
    
    // --

    foreach ($items as $item) {
      $tmpRetorno['TOTAL']            = number_format($tmpRetorno['TOTAL'] + $item->TOTAL, 2, '.', '');
      $tmpRetorno['BRUTO']            = number_format($tmpRetorno['BRUTO'] + $item->BRUTO, 2, '.', '');
      $tmpRetorno['TOTAL_RENDIMENTO'] = number_format($tmpRetorno['TOTAL_RENDIMENTO'] + $item->TOTAL_RENDIMENTO, 2, '.', '');
      $tmpRetorno['TOTAL_DIVIDENDO']  = number_format($tmpRetorno['TOTAL_DIVIDENDO'] + $item->TOTAL_DIVIDENDO, 2, '.', '');
      $tmpRetorno['TOTAL_JSCP']       = number_format($tmpRetorno['TOTAL_JSCP'] + $item->TOTAL_JSCP, 2, '.', '');
    }

    return $tmpRetorno;
  }
  private function itemsCarteiraAnaliseTipo($items) {
    $tmpRetorno = [
      'items'         => [],
      // 'items_GRAFICO' => [],
    ];

    if(count($items) == 0) return $tmpRetorno;
      
    // --

    $items = $this->apuraArrayItemsByKey($items, 'INTP');

    // // VALORES PARA GRAFICO
    // $itemsGrafico = [];
    // foreach ($items as $key => $item) {
    //   $itemsGrafico[$item->DESCRICAO]['label'][]   = $item->DESCRICAO;
    //   $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->BRUTO;
    //   $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->TOTAL;
    // }

    $tmpRetorno['items']         = array_values($items);
    // $tmpRetorno['items_GRAFICO'] = array_values($itemsGrafico);

    return $tmpRetorno;
  }
  private function itemsCarteiraAnaliseTipoAtivo($items) {
    $tmpRetorno = [
      'items'         => [],
      // 'items_GRAFICO' => [],
    ];

    if(count($items) == 0) return $tmpRetorno;
      
    // --

    $items = $this->apuraArrayItemsByKey($items, 'INAT');

    // // VALORES PARA GRAFICO
    // $itemsGrafico = [];
    // foreach ($items as $key => $item) {
    //   $itemsGrafico[$item->DESCRICAO]['label'][]   = $item->DESCRICAO;
    //   $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->BRUTO;
    //   $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->TOTAL;
    // }

    $tmpRetorno['items']         = array_values($items);
    // $tmpRetorno['items_GRAFICO'] = array_values($itemsGrafico);

    return $tmpRetorno;
  }
  private function itemsCarteiraAnaliseAtivo($items) {
    $tmpRetorno = [
      'items'         => [],
      // 'items_GRAFICO' => [],
    ];

    if(count($items) == 0) return $tmpRetorno;
      
    // --

    $items = $this->apuraArrayItemsByKey($items, 'INAV');

    // // VALORES PARA GRAFICO
    // $itemsGrafico = [];
    // foreach ($items as $key => $item) {
    //   $itemsGrafico[$item->DESCRICAO]['label'][]   = $item->DESCRICAO;
    //   $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->BRUTO;
    //   $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->TOTAL;
    // }

    $tmpRetorno['items']         = array_values($items);
    // $tmpRetorno['items_GRAFICO'] = array_values($itemsGrafico);

    return $tmpRetorno;
  }
  private function itemsCarteiraComposicao($items) {
    $tmpRetorno = [
      'items'       => [],
      'itemsFiltro' => []
    ];

    if(count($items) == 0) return $tmpRetorno;
      
    // --

    $items = $this->orderArrayFiltro([$items], 'INAV_CODIGO')[0];

    $items = array_merge(
      array_filter( $items, function($item) { return $item->COTAS > 0; }),
      array_filter( $items, function($item) { return $item->COTAS <= 0; })
    );
    
    $items = array_values($items);

    // --

    $tmpRetorno['items']      = $items;
    $tmpRetorno['itemsFiltro'] = $this->filtroByItems($items); // monta ItemFiltro

    return $tmpRetorno;
  }

  // -- listagens
  /*

  private function getCotacao() {
    if( !isset($_GET['INCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o INCT_ID é uma parametro obrigatório']) );
    
    $options['usuario'] = $_GET['usuario'];
    $options['INCT_ID'] = $_GET['INCT_ID'];
    if(isset($_GET['INOD_STATUS'])) $options['INOD_STATUS'] = $_GET['INOD_STATUS'];
    if(isset($_GET['INAV_ID']))     $options['INAV_ID']     = $_GET['INAV_ID'];
    if(isset($_GET['orderby']))     $options['orderby']     = $_GET['orderby'];
    if(isset($_GET['data']))        $options['data']        = $_GET['data'];
    if(isset($_GET['limit']))       $options['limit']       = (!empty($_GET['limit']))   ? $_GET['limit']   : 30;
    if(isset($_GET['dataAte']))     $options['dataAte']     = (!empty($_GET['dataAte'])) ? $_GET['dataAte'] : date('Y-m-d');
    if(isset($_GET['dataDe']))      $options['dataDe']      = (!empty($_GET['dataDe']))  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'].'-30 day'));

    $items = $this->ativoCotacao->get($options);

    $response['items']       = $items;
    // $response['itemsFiltro'] = $itemsFiltro;
    if(isset($_GET['limit']))   $response['limit']   = $options['limit'];
    if(isset($_GET['dataAte'])) $response['dataAte'] = $options['dataAte'];
    if(isset($_GET['dataDe']))  $response['dataDe']  = $options['dataDe'];

    return $response;
  }
  
  private function getAtivoSplit() {
    $options['usuario'] = $_GET['usuario'];
    // $options['INCT_ID'] = $_GET['INCT_ID'];
    if(isset($_GET['INAS_STATUS'])) $options['INAS_STATUS'] = $_GET['INAS_STATUS'];
    if(isset($_GET['INAS_TIPO']))   $options['INAS_TIPO']   = $_GET['INAS_TIPO'];
    if(isset($_GET['INAV_ID']))     $options['INAV_ID']     = $_GET['INAV_ID'];
    if(isset($_GET['orderby']))     $options['orderby']     = $_GET['orderby'];
    if(isset($_GET['data']))        $options['data']        = $_GET['data'];
    if(isset($_GET['limit']))       $options['limit']       = (!empty($_GET['limit']))   ? $_GET['limit']   : 30;
    if(isset($_GET['dataAte']))     $options['dataAte']     = (!empty($_GET['dataAte'])) ? $_GET['dataAte'] : date('Y-m-d');
    if(isset($_GET['dataDe']))      $options['dataDe']      = (!empty($_GET['dataDe']))  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'].'-30 day'));

    $items = $this->ativoSplit->get($options);
    $itemsFiltro = $this->filtroByAtivoSplit($items);

    $response['items']       = $items;
    $response['itemsFiltro'] = $itemsFiltro;
    if(isset($_GET['limit']))   $response['limit']   = $options['limit'];
    if(isset($_GET['dataAte'])) $response['dataAte'] = $options['dataAte'];
    if(isset($_GET['dataDe']))  $response['dataDe']  = $options['dataDe'];

    return $response;
  }

  private function getRendimento() {
    if( !isset($_GET['INCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o INCT_ID é uma parametro obrigatório']) );
    
    $options['usuario'] = $_GET['usuario'];
    $options['INCT_ID'] = $_GET['INCT_ID'];
    if(isset($_GET['INAR_STATUS'])) $options['INAR_STATUS'] = $_GET['INAR_STATUS'];
    if(isset($_GET['INAV_ID']))     $options['INAV_ID'] = $_GET['INAV_ID'];
    if(isset($_GET['orderby']))     $options['orderby'] = $_GET['orderby'];
    if(isset($_GET['data']))        $options['data']    = $_GET['data'];
    if(isset($_GET['limit']))       $options['limit']   = (!empty($_GET['limit']))   ? $_GET['limit']   : 30;
    if(isset($_GET['dataAte']))     $options['dataAte'] = (!empty($_GET['dataAte'])) ? $_GET['dataAte'] : date('Y-m-d');
    if(isset($_GET['dataDe']))      $options['dataDe']  = (!empty($_GET['dataDe']))  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'].'-30 day'));

    $items = $this->ativoRendimento->get( $options );

    $itemsFiltro = $this->filtroByRendimento($items);

    // --

    $response['items'] = $items;
    $response['itemsFiltro'] = $itemsFiltro;
    if(isset($_GET['limit']))   $response['limit']   = $options['limit'];
    if(isset($_GET['dataAte'])) $response['dataAte'] = $options['dataAte'];
    if(isset($_GET['dataDe']))  $response['dataDe']  = $options['dataDe'];

    return $response;
  }
  
  private function getOrdem() { // ok
    if( !isset($_GET['INCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o INCT_ID é uma parametro obrigatório']) );
    
    $options['usuario'] = $_GET['usuario'];
    $options['INCT_ID'] = $_GET['INCT_ID'];
    if(isset($_GET['INOD_STATUS'])) $options['INOD_STATUS'] = $_GET['INOD_STATUS'];
    if(isset($_GET['orderby']))     $options['orderby']     = $_GET['orderby'];
    if(isset($_GET['data']))        $options['data']        = $_GET['data'];
    if(isset($_GET['limit']))       $options['limit']       = (!empty($_GET['limit']))   ? $_GET['limit']   : 30;
    if(isset($_GET['dataAte']))     $options['dataAte']     = (!empty($_GET['dataAte'])) ? $_GET['dataAte'] : date('Y-m-d');
    if(isset($_GET['dataDe']))      $options['dataDe']      = (!empty($_GET['dataDe']))  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'].'-30 day'));

    $items = $this->ordem->get($options); 

    $itemsFiltro = $this->filtroByOrdem($items);

    $response['items']       = $items;
    $response['itemsFiltro'] = $itemsFiltro;
    if(isset($_GET['limit']))   $response['limit']   = $options['limit'];
    if(isset($_GET['dataAte'])) $response['dataAte'] = $options['dataAte'];
    if(isset($_GET['dataDe']))  $response['dataDe']  = $options['dataDe'];

    return $response;
  }

  private function getOrdemItems() { // ok
    $options['usuario'] = $_GET['usuario'];
    $options['INOD_ID'] = $_GET['INOD_ID'];
    $options['orderby'] = $_GET['orderby'];
    
    $items = $this->item->get($options);

    $itemsFiltro = $this->filtroByOperacoes($items); // monta ItemFiltro

    return [
      'items'       => $items,
      'itemsFiltro' => $itemsFiltro,
    ];
  }

  private function getItemExtrato() {
      if(isset($_GET['limit']))   $_GET['limit']   = (!empty($_GET['limit']))   ? $_GET['limit']   : 30;
      if(isset($_GET['dataAte'])) $_GET['dataAte'] = (!empty($_GET['dataAte'])) ? $_GET['dataAte'] : date('Y-m-d');
      if(isset($_GET['dataDe']))  $_GET['dataDe']  = (!empty($_GET['dataDe']))  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'].'-30 day'));

    $items = $this->item->get($_GET);
    
    $items = array_values($items);

    $itemsFiltro = $this->filtroByOperacoes($items); // monta ItemFiltro

    $response['items']       = $items;
    $response['itemsFiltro'] = $itemsFiltro;
    if(isset($_GET['limit']))   $response['limit']   = $_GET['limit'];
    if(isset($_GET['dataAte'])) $response['dataAte'] = $_GET['dataAte'];
    if(isset($_GET['dataDe']))  $response['dataDe']  = $_GET['dataDe'];

    return $response;
  }

  private function getAnaliseAtivo() {
    $_GET['dataAte'] = isset($_GET['dataAte']) ? $_GET['dataAte'] : date('Y-m-d');

    $items = $this->item->get($_GET);

    $items = $this->analiseItensBase($items, $_GET['dataAte']);

    $items = $this->apuraArrayItemsByKey($items, 'INAV');

    if(count($items) == 0)
    return [
      'items'         => [],
      'items_GRAFICO' => [],
    ];

    // --
  
    // VALORES PARA GRAFICO
    foreach ($items as $key => $item) {
      $itemsGrafico[$item->DESCRICAO]['label'][]   = $item->DESCRICAO;
      $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->BRUTO;
      $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->TOTAL;
    }

    return [
      'items'         => array_values($items), // usando array_values para retornar um array sem key
      'items_GRAFICO' => array_values($itemsGrafico),
    ];
  }
  */

  // --
  // AUX

  private function analiseItensBase($items, $dataAte){
    /*
      Método para agrupar items da tabela, 
      agrupamento por CARTEIRA/CORRETORA/ATIVO
      calculando valores de compra e venda, rendimentos, preco médio, quantidade de cotas, valor aplicado valor acumulado
    */
    $arrDadosCotacao = [];
    $arrDadosSplit   = [];


    // agrupa item por CARTEIRA e ATIVO
    foreach ($items as $key => $item) {

      $INCT = $item->INCT_ID;
      $INCR = $item->INCR_ID;
      $INAV = $item->INAV_ID;

      // REGRA RENDA VARIAVEL 
      if($item->INTP_ID == 2) {
        // BUSCA COTAÇÃO APENAS 1 VEZ PARA CADA ATIVO
        if(!isset($arrDadosCotacao[$item->INAV_ID])){
          $options = [];
          $options['usuario'] = $_GET['usuario'];
          $options['INAV_ID'] = $item->INAV_ID;
          $options['status']  = 1;
          $options['orderby'] = 'INAC_DATA:ASC';
          $cotacaoList = $this->ativoCotacao->get($options);
        }
        
        // BUSCA 'SPLIT' APENAS 1 VEZ PARA CADA ATIVO
        if(!isset($arrDadosSplit[$item->INAV_ID])){
          $options = [];  
          $options['usuario'] = $_GET['usuario'];
          $options['INAV_ID'] = $item->INAV_ID;
          $options['INAS_STATUS']  = 1;
          $options['orderby'] = 'INAS_DATA:ASC';
          $arrDadosSplit[$item->INAV_ID] = $this->ativoSplit->get($options);
        }
      }


      if(isset($items["{$INCT}_{$INCR}_{$INAV}"])){ // QUANDO ITEM JÁ EXISTIR NO ARRAY, APURA OS VALORES

        // VALIDA SE EXIST SPLIT PARA O ATIVO, CASO TENHA APLICA-SE
        if( isset($arrDadosSplit[$item->INAV_ID]) && count($arrDadosSplit[$item->INAV_ID]) > 0) {
          
          $aplicaSplitInplit = $this->aplicaSplitInplit($item, $items, $items["{$INCT}_{$INCR}_{$INAV}"], $arrDadosSplit);
          
          $arrDadosSplit                    = $aplicaSplitInplit['arrDadosSplit'];
          $items["{$INCT}_{$INCR}_{$INAV}"] = $aplicaSplitInplit['arrItem'];
        }

        $options['INCT_ID'] = $item->INCT_ID;
        $options['INCR_ID'] = $item->INCR_ID;
        $options['INAV_ID'] = $item->INAV_ID;
        $options['dataAte'] = $dataAte;
        $rendimentos = $this->ativoRendimento->rendimentoAtivoCorretora($options);

        $TOTAL_RENDIMENTO  = 0;
        $TOTAL_DIVIDENDO   = 0;
        $TOTAL_JSCP        = 0;
        $MES_RENDIMENTO    = 0;
        $MES_DIVIDENDO     = 0;
        $MES_JSCP          = 0;

        foreach ($rendimentos as $key_r => $rendimento) {
          $mesIgual =  $rendimento->INAR_DATA == date('Y-m');
  
          // RENDA FIXA
          if($item->INTP_ID == 1 ) { 
            $TOTAL_RENDIMENTO += $rendimento->INAR_VALOR;              // soma rendimentos totais acumulados
            if($mesIgual) $MES_RENDIMENTO += $rendimento->INAR_VALOR;  // soma rendimentos do mes atual
          } 
  
          // RENDA VARIAVEL
          else if ($item->INTP_ID == 2){       
  
            // DIVIDENDOS
            if($rendimento->INAR_TIPO === 'D'){
              $TOTAL_DIVIDENDO += $rendimento->INAR_VALOR;              // soma DIVIDENDOS totais acumulados
              if($mesIgual) $MES_DIVIDENDO += $rendimento->INAR_VALOR;  // soma DIVIDENDOS do mes atual
            }
  
            // JSCP
            if($rendimento->INAR_TIPO === 'J'){
              $TOTAL_JSCP += $rendimento->INAR_VALOR;              // soma JSCP totais acumulados
              if($mesIgual) $MES_JSCP += $rendimento->INAR_VALOR;  // soma JSCP do mes atual
            }
          }
  
          unset($rendimentos[$key_r]);
        }

        // ---- ITEMS
        if($item->INIT_CV == 'C'){
          $items["{$INCT}_{$INCR}_{$INAV}"]->H_TOTAL_COMPRA = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->H_TOTAL_COMPRA + $item->INIT_PRECO_TOTAL , 2, '.', '');
          $items["{$INCT}_{$INCR}_{$INAV}"]->H_COTAS_COMPRA = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->H_COTAS_COMPRA + $item->INIT_COTAS , 2, '.', '');
          $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_COMPRA   = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_COMPRA   + $item->INIT_PRECO_TOTAL , 2, '.', '');
          $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS_COMPRA   = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS_COMPRA   + $item->INIT_COTAS , 2, '.', '');
  
        }else if($item->INIT_CV == 'V'){
          $items["{$INCT}_{$INCR}_{$INAV}"]->H_TOTAL_VENDA = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->H_TOTAL_VENDA + $item->INIT_PRECO_TOTAL , 2, '.', '');
          $items["{$INCT}_{$INCR}_{$INAV}"]->H_COTAS_VENDA = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->H_COTAS_VENDA + $item->INIT_COTAS , 2, '.', '');
          $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_VENDA   = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_VENDA   + $item->INIT_PRECO_TOTAL , 2, '.', '');
          $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS_VENDA   = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS_VENDA   + $item->INIT_COTAS , 2, '.', '');
        }

        // APURA COTAS
        $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS_COMPRA - $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS_VENDA , 2, '.', '');

        // APURA TOTAL
        $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_COMPRA - $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_VENDA , 2, '.', '');

        // APURA PRECO_MEDIO
        if($items["{$INCT}_{$INCR}_{$INAV}"]->COTAS > 0) {
          $items["{$INCT}_{$INCR}_{$INAV}"]->PRECO_MEDIO = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL / $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS , 2, '.', '');
        }

        // REGRA RENDA VARIAVEL 
        if($item->INTP_ID == 2) {

          //  QUANDO EXISTIR 'COTACAO LIST' aplica regra com valores de cotação, ou utiliza o PREÇO MÉDIO já apurado
          $cotacaoMes = 'init';
          if(count($cotacaoList) > 0) {

            if(strlen($dataAte) == 7)  $dataAte = "{$dataAte}-01";
            if(strlen($dataAte) == 10) $dataAte = "{$dataAte}";

            //BUSCA COTAÇÃO PARA O MES EM BUSCA
            for ($i = count($cotacaoList) - 1 ; $i >= 0; $i--) { 
              $d_mes = date('Y-m', strtotime($dataAte));
              $c_mes = date('Y-m', strtotime($cotacaoList[$i]->INAC_DATA));
  
              if($d_mes == $c_mes) {
                $cotacaoMes = $cotacaoList[$i];
                $cotacaoList = [];
                break;
              }
            }
          }

          // QUANDO EXISTIR COTAÇÃO "MES == dataAte"
          $PRECO_COTACAO = ($cotacaoMes != 'init') ? $cotacaoMes->INAC_VALOR : $items["{$INCT}_{$INCR}_{$INAV}"]->PRECO_MEDIO;
          
          $items["{$INCT}_{$INCR}_{$INAV}"]->PRECO_COTACAO = $PRECO_COTACAO;

          // PREÇO BRUTO, 'PREÇO DA COTA X QUANTIDADE'  
          $items["{$INCT}_{$INCR}_{$INAV}"]->BRUTO = number_format( $PRECO_COTACAO * $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS , 2, '.', '');
          
          // VALORIZAÇAO POR COTA, 'PREÇO COTA - PREÇO MEDIO'
          $items["{$INCT}_{$INCR}_{$INAV}"]->VALORIZACAO_UNIDADE = number_format( $PRECO_COTACAO - $items["{$INCT}_{$INCR}_{$INAV}"]->PRECO_MEDIO , 2, '.', '');
          
          // VALORIZACAO_TOTAL TOTAL, '(PREÇO COTA - PREÇO MEDIO) X QUANTIDADE'
          $items["{$INCT}_{$INCR}_{$INAV}"]->VALORIZACAO_TOTAL = number_format( ($PRECO_COTACAO - $items["{$INCT}_{$INCR}_{$INAV}"]->PRECO_MEDIO) * $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS , 2, '.', '');


          // QUANTO NÃO HOUVER COTAS, ZERA ALGUMAS VALORES
          if($items["{$INCT}_{$INCR}_{$INAV}"]->COTAS == 0 ) {
            
            $LUCRO_VENDA = number_format( $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_VENDA - $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_COMPRA , 2, '.', '');

            $items["{$INCT}_{$INCR}_{$INAV}"]->LUCRO_VENDA += $LUCRO_VENDA;

            $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_COMPRA      = 0;
            $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL_VENDA       = 0;
            $items["{$INCT}_{$INCR}_{$INAV}"]->TOTAL             = 0;
      
            $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS_COMPRA      = 0;
            $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS_VENDA       = 0;
            $items["{$INCT}_{$INCR}_{$INAV}"]->COTAS             = 0;
      
            $items["{$INCT}_{$INCR}_{$INAV}"]->PRECO_MEDIO       = 0;
      
            $items["{$INCT}_{$INCR}_{$INAV}"]->VALORIZACAO_UNIDADE   = 0;
            $items["{$INCT}_{$INCR}_{$INAV}"]->VALORIZACAO_TOTAL = 0;

            
            $H_LUCRO_VENDA['INOD_DATA']   = $item->INOD_DATA;
            $H_LUCRO_VENDA['LUCRO_VENDA'] = $LUCRO_VENDA;
            $H_LUCRO_VENDA['IR']          = 0;

            if( ($item->INAT_ID == 1 && $LUCRO_VENDA >= 20000) || $item->INAT_ID == 6) {
              $H_LUCRO_VENDA['IR'] = number_format( (($LUCRO_VENDA * 0.15) ), 2, '.', '');
            }
            if($item->INAT_ID == 4) {
              $H_LUCRO_VENDA['IR'] = number_format( (($LUCRO_VENDA * 0.20) ), 2, '.', '');
            }

            $items["{$INCT}_{$INCR}_{$INAV}"]->H_LUCRO_VENDA[] = $H_LUCRO_VENDA;
          }
        }
      }else{
        $items["{$INCT}_{$INCR}_{$INAV}"] = $this->renderArrayAtivo($item);

        // QUANDO HOUVER APENAS UM ITEM DO ATIVO E EXISTIR UM SPLIT É REALIZADO A APLICAÇÃO DO SPLIT
        $filterItemsAtivoUnico = count(array_filter( $items, function($val) use($item) { return $val->INAV_ID == $item->INAV_ID; }));

        if($filterItemsAtivoUnico == 1) {
          $aplicaSplitInplit = $this->aplicaSplitInplit($item, $items, $items["{$INCT}_{$INCR}_{$INAV}"], $arrDadosSplit);

          $arrDadosSplit                    = $aplicaSplitInplit['aplicaSplitInplit'];
          $items["{$INCT}_{$INCR}_{$INAV}"] = $aplicaSplitInplit['arrItem'];
        };

        // --
      }
      
      unset($items[$key]);
    }

    // APURA BRUTO POR TIPO 'RENDA FIXA/RENDA VARIAVEL'
    foreach ($items as $item_key => $item) {

      // APURA VALOR RENDA FIXA
      if($items[$item_key]->INTP_ID == 1){
        $items[$item_key]->BRUTO = $item->TOTAL + $item->TOTAL_RENDIMENTO;
      }
      
      $items[$item_key]->COTAS_COMPRA      = number_format($item->COTAS_COMPRA, 2, '.', '');
      $items[$item_key]->TOTAL_COMPRA      = number_format($item->TOTAL_COMPRA, 2, '.', '');
      $items[$item_key]->COTAS_VENDA       = number_format($item->COTAS_VENDA, 2, '.', '');
      $items[$item_key]->TOTAL_VENDA       = number_format($item->TOTAL_VENDA, 2, '.', '');
      $items[$item_key]->COTAS             = number_format($item->COTAS, 2, '.', '');
      $items[$item_key]->TOTAL    = number_format($item->TOTAL, 2, '.', '');
      $items[$item_key]->BRUTO       = number_format($item->BRUTO, 2, '.', '');
      $items[$item_key]->VALORIZACAO_UNIDADE   = number_format($item->VALORIZACAO_UNIDADE, 2, '.', '');
      $items[$item_key]->VALORIZACAO_TOTAL = number_format($item->VALORIZACAO_TOTAL, 2, '.', '');
      $items[$item_key]->LUCRO_VENDA       = number_format($item->LUCRO_VENDA, 2, '.', '');
      $items[$item_key]->PRECO_MEDIO       = number_format($item->PRECO_MEDIO, 2, '.', '');
      $items[$item_key]->PRECO_COTACAO     = number_format($item->PRECO_COTACAO, 2, '.', '');
      $items[$item_key]->TOTAL_RENDIMENTO  = number_format($item->TOTAL_RENDIMENTO, 2, '.', '');
      $items[$item_key]->TOTAL_DIVIDENDO   = number_format($item->TOTAL_DIVIDENDO, 2, '.', '');
      $items[$item_key]->TOTAL_JSCP        = number_format($item->TOTAL_JSCP, 2, '.', '');
      $items[$item_key]->MES_RENDIMENTO    = number_format($item->MES_RENDIMENTO, 2, '.', '');
      $items[$item_key]->MES_DIVIDENDO     = number_format($item->MES_DIVIDENDO, 2, '.', '');
      $items[$item_key]->MES_JSCP          = number_format($item->MES_JSCP, 2, '.', '');
    }
    
    return array_values($items);
  }

  private function apuraArrayItemsByKey($items, $alias) {
    
    foreach ($items as $item_key => $item) {
      $DESCRICAO = "{$alias}_DESCRICAO";
      $ID        = "{$alias}_ID";
      $tmp_key   = $item->$ID;
      
      if(!isset($items["aux_{$tmp_key}"])){
        $items["aux_{$tmp_key}"] = (object)[
          "USUA_ID"          => $item->USUA_ID,
          "USUA_NOME"        => $item->USUA_NOME,

          "INCT_ID"          => $item->INCT_ID,
          "INCT_DESCRICAO"   => $item->INCT_DESCRICAO,
          "INCT_STATUS"      => $item->INCT_STATUS,
          
          "INCR_ID"          => $item->INCR_ID,
          "INCR_DESCRICAO"   => $item->INCR_DESCRICAO,
          "INCR_STATUS"      => $item->INCR_STATUS,
          "CORRETORA"        => $item->INCR_DESCRICAO,
          
          "$ID"              => $item->$ID,
          "$DESCRICAO"       => $item->$DESCRICAO,
          "DESCRICAO"        => $item->$DESCRICAO,
          "INAV_LIQUIDEZ"    => $item->INAV_LIQUIDEZ,
          "INAV_VENC"        => $item->INAV_VENC,
          "INAV_STATUS"      => $item->INAV_STATUS,

          "INTP_ID"          => $item->INTP_ID,
          "INTP_DESCRICAO"   => $item->INTP_DESCRICAO,
          "INTP_STATUS"      => $item->INTP_STATUS,
          
          "INAT_ID"          => $item->INAT_ID,
          "INAT_DESCRICAO"   => $item->INAT_DESCRICAO,
          "INAT_STATUS"      => $item->INAT_STATUS,

          "COTAS_COMPRA"     => 0,
          "TOTAL_COMPRA"     => 0,
          "COTAS_VENDA"      => 0,
          "TOTAL_VENDA"      => 0,
          
          "COTAS"            => 0,
          "TOTAL"            => 0,
          "BRUTO"            => 0,

          
          "VALORIZACAO_UNIDADE"  => $item->VALORIZACAO_UNIDADE,   // uso em RENDA VARIAVEL
          "VALORIZACAO_TOTAL"=> $item->VALORIZACAO_TOTAL, // uso em RENDA VARIAVEL
          "LUCRO_VENDA"      => $item->LUCRO_VENDA,       // uso em RENDA VARIAVEL
          "PRECO_MEDIO"      => $item->PRECO_MEDIO,       // uso em RENDA VARIAVEL
          "PRECO_COTACAO"    => $item->PRECO_COTACAO,     // uso em RENDA VARIAVEL
          
          "TOTAL_RENDIMENTO" => 0, // uso em RENDA FIXA
          "TOTAL_DIVIDENDO"  => 0, // uso em RENDA VARIAVEL
          "TOTAL_JSCP"       => 0, // uso em RENDA VARIAVEL
          "MES_RENDIMENTO"   => 0, // uso em RENDA FIXA
          "MES_DIVIDENDO"    => 0, // uso em RENDA VARIAVEL
          "MES_JSCP"         => 0, // uso em RENDA VARIAVEL
        ];
      }

      $items["aux_{$tmp_key}"]->COTAS_COMPRA     += number_format($item->COTAS_COMPRA, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_COMPRA     += number_format($item->TOTAL_COMPRA, 2, '.', '');
      $items["aux_{$tmp_key}"]->COTAS_VENDA      += number_format($item->COTAS_VENDA, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_VENDA      += number_format($item->TOTAL_VENDA, 2, '.', '');
      $items["aux_{$tmp_key}"]->COTAS            += number_format($item->COTAS, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL            += number_format($item->TOTAL, 2, '.', '');
      $items["aux_{$tmp_key}"]->BRUTO            += number_format($item->BRUTO, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_RENDIMENTO += number_format($item->TOTAL_RENDIMENTO, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_DIVIDENDO  += number_format($item->TOTAL_DIVIDENDO, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_JSCP       += number_format($item->TOTAL_JSCP, 2, '.', '');
      $items["aux_{$tmp_key}"]->MES_RENDIMENTO   += number_format($item->MES_RENDIMENTO, 2, '.', '');
      $items["aux_{$tmp_key}"]->MES_DIVIDENDO    += number_format($item->MES_DIVIDENDO, 2, '.', '');
      $items["aux_{$tmp_key}"]->MES_JSCP         += number_format($item->MES_JSCP, 2, '.', '');
      
      unset($items[$item_key]);
    }

    foreach ($items as $item_key => $item) {
      // APURA PREÇO MÉDIO
      if( $items[$item_key]->COTAS > 0){
        $items[$item_key]->PRECO_MEDIO = $items[$item_key]->TOTAL / $items[$item_key]->COTAS;
      } else {
        $items[$item_key]->PRECO_MEDIO = 0;
      }
    }

    return $items;
  }
  
  private function renderArrayAtivo($item) {

    $tmp = (object)[
      "USUA_ID"          => $item->USUA_ID,
      "USUA_NOME"        => $item->USUA_NOME,

      "INCT_ID"          => $item->INCT_ID,
      "INCT_DESCRICAO"   => $item->INCT_DESCRICAO,
      "INCT_STATUS"      => $item->INCT_STATUS,

      "INCR_ID"          => $item->INCR_ID,
      "INCR_DESCRICAO"   => $item->INCR_DESCRICAO,
      "INCR_STATUS"      => $item->INCR_STATUS,

      "INAV_ID"          => $item->INAV_ID,
      "INAV_CODIGO"      => $item->INAV_CODIGO,
      "INAV_DESCRICAO"   => $item->INAV_DESCRICAO,
      "INAV_CPNJ"        => $item->INAV_CPNJ,
      "INAV_SITE"        => $item->INAV_SITE,
      "INAV_LIQUIDEZ"    => $item->INAV_LIQUIDEZ,
      "INAV_VENC"        => $item->INAV_VENC,
      "INAV_STATUS"      => $item->INAV_STATUS,

      "INTP_ID"          => $item->INTP_ID,
      "INTP_DESCRICAO"   => $item->INTP_DESCRICAO,
      "INTP_STATUS"      => $item->INTP_STATUS,

      "INAT_ID"          => $item->INAT_ID,
      "INAT_DESCRICAO"   => $item->INAT_DESCRICAO,
      "INAT_STATUS"      => $item->INAT_STATUS,
      
      "H_COTAS_COMPRA"    => 0,
      "H_COTAS_VENDA"     => 0,
      "COTAS_COMPRA"      => 0,
      "COTAS_VENDA"       => 0,
      "COTAS"             => 0,

      "H_TOTAL_COMPRA"    => 0,
      "H_TOTAL_VENDA"     => 0,
      "TOTAL_COMPRA"      => 0,
      "TOTAL_VENDA"       => 0,
      "TOTAL"             => 0,

      "BRUTO"             => 0,
      
      "VALORIZACAO_UNIDADE"  => 0, // uso em RENDA VARIAVEL
      "VALORIZACAO_TOTAL"=> 0, // uso em RENDA VARIAVEL

      "PRECO_MEDIO"      => 0, // uso em RENDA VARIAVEL
      "PRECO_COTACAO"    => 0, // uso em RENDA VARIAVEL
      "LUCRO_VENDA"      => 0, // uso em RENDA VARIAVEL
      "H_LUCRO_VENDA"    => [], // uso em RENDA VARIAVEL
      
      "TOTAL_RENDIMENTO" => 0, // uso em RENDA FIXA
      "TOTAL_DIVIDENDO"  => 0, // uso em RENDA VARIAVEL
      "TOTAL_JSCP"       => 0, // uso em RENDA VARIAVEL

      "MES_RENDIMENTO"   => 0, // uso em RENDA FIXA
      "MES_DIVIDENDO"    => 0, // uso em RENDA VARIAVEL
      "MES_JSCP"         => 0, // uso em RENDA VARIAVEL
    ];

    // ---- ITEMS
    if($item->INIT_CV == 'C'){
      $tmp->H_TOTAL_COMPRA = number_format( $tmp->H_TOTAL_COMPRA + $item->INIT_PRECO_TOTAL , 2, '.', '');
      $tmp->H_COTAS_COMPRA = number_format( $tmp->H_COTAS_COMPRA + $item->INIT_COTAS , 2, '.', '');
      $tmp->TOTAL_COMPRA   = number_format( $tmp->TOTAL_COMPRA   + $item->INIT_PRECO_TOTAL , 2, '.', '');
      $tmp->COTAS_COMPRA   = number_format( $tmp->COTAS_COMPRA   + $item->INIT_COTAS , 2, '.', '');

    }else if($item->INIT_CV == 'V'){
      $tmp->H_TOTAL_VENDA = number_format( $tmp->H_TOTAL_VENDA + $item->INIT_PRECO_TOTAL , 2, '.', '');
      $tmp->H_COTAS_VENDA = number_format( $tmp->H_COTAS_VENDA + $item->INIT_COTAS , 2, '.', '');
      $tmp->TOTAL_VENDA   = number_format( $tmp->TOTAL_VENDA   + $item->INIT_PRECO_TOTAL , 2, '.', '');
      $tmp->COTAS_VENDA   = number_format( $tmp->COTAS_VENDA   + $item->INIT_COTAS , 2, '.', '');
    }

    // APURA COTAS
    $tmp->COTAS = number_format( $tmp->COTAS_COMPRA - $tmp->COTAS_VENDA , 2, '.', '');

    // APURA TOTAL
    $tmp->TOTAL = number_format( $tmp->TOTAL_COMPRA - $tmp->TOTAL_VENDA , 2, '.', '');

    // APURA PRECO_MEDIO
    if( $tmp->COTAS > 0) {
      $tmp->PRECO_MEDIO = number_format( $tmp->TOTAL / $tmp->COTAS , 2, '.', '');
    }

    // REGRA RENDA VARIAVEL 
    if($item->INTP_ID == 2) {

      $PRECO_COTACAO = $tmp->PRECO_MEDIO;
      
      $tmp->PRECO_COTACAO = $PRECO_COTACAO;

      // PREÇO BRUTO, 'PREÇO DA COTA X QUANTIDADE'  
      $tmp->BRUTO = number_format( $PRECO_COTACAO * $tmp->COTAS , 2, '.', '');
      
      // VALORIZAÇAO POR COTA, 'PREÇO COTA - PREÇO MEDIO'
      $tmp->VALORIZACAO_UNIDADE = number_format( $PRECO_COTACAO - $tmp->PRECO_MEDIO , 2, '.', '');
      
      // VALORIZACAO_TOTAL TOTAL, '(PREÇO COTA - PREÇO MEDIO) X QUANTIDADE'
      $tmp->VALORIZACAO_TOTAL = number_format( ($PRECO_COTACAO - $tmp->PRECO_MEDIO) * $tmp->COTAS , 2, '.', '');
    }

    return $tmp;
  }

  private function aplicaSplitInplit($item, $items, $arrItem, $arrDadosSplit, $useIF = false) {
    $INAS = $arrDadosSplit[$item->INAV_ID][0];

    $dataOrdem = date_create($item->INOD_DATA);
    $dataSplit = date_create(date('Y-m-d', strtotime($INAS->INAS_DATA . '-1 day'))); // remove 1 dia para dar igual quando for dias iguais, pois no diff dias iguais da false
    $diff      = date_diff($dataOrdem, $dataSplit);

    // QUNADO DATA ORDEM FOR IGUAL OU SUPERIOR A DATA SPLIT, APLICA REGRA
    if( $useIF || $diff->invert == 1 ? true : false) {
      /* REGRA DE USO
       * INAS_TIPO == 'S' MULTIPLICA
       * INAS_TIPO == 'I' DIVIDE
      */
      if($INAS->INAS_TIPO == 'S') $arrItem->COTAS_COMPRA *= ($INAS->INAS_QUANTIDADE + 1);
      if($INAS->INAS_TIPO == 'I') $arrItem->COTAS_COMPRA /= ($INAS->INAS_QUANTIDADE + 1);
      
      if($INAS->INAS_TIPO == 'S') $arrItem->COTAS_VENDA *= ($INAS->INAS_QUANTIDADE + 1);
      if($INAS->INAS_TIPO == 'I') $arrItem->COTAS_VENDA /= ($INAS->INAS_QUANTIDADE + 1);

      unset($arrDadosSplit[$item->INAV_ID][0]);
    }

    return [
      'arrItem' => $arrItem,
      'arrDadosSplit' => $arrDadosSplit
    ];
  }


  // --

  // FILTRO 
  private function filtroByItems($items) {
    $arrFiltro = [
      'INCR'     => [],
      'INTP'     => [],
      'INAT'     => [],
      'INAV'     => [],
      'LIQUIDEZ' => [],
      'DATA'     => [],
      'COTAS'    => [],
    ];

    if( count($items) == 0 ) return $arrFiltro;

    foreach($items as $item){
      $cotaVal  = $item->COTAS == 0 ? 'sem-cotas' : 'com-cotas';
      $cotaDesc = $item->COTAS == 0 ? 'Sem cotas' : 'Com cotas';
      $dataVal  = date('Y-m-d',strtotime($item->INAV_VENC));
      $dataDesc = date('d/m/Y',strtotime($item->INAV_VENC));
      
      if( !key_exists($item->INCR_ID, $arrFiltro['INCR']) ){
        $arrFiltro['INCR'][$item->INCR_ID] = (object)[
          'VALUE'     => $item->INCR_ID,
          'DESCRICAO' => $item->INCR_DESCRICAO ];
      }
      if( !key_exists($item->INTP_ID, $arrFiltro['INTP']) ){
        $arrFiltro['INTP'][$item->INTP_ID] = (object)[
          'VALUE'     => $item->INTP_ID,
          'DESCRICAO' => $item->INTP_DESCRICAO ];
      }
      if( !key_exists($item->INAT_ID, $arrFiltro['INAT']) ){
        $arrFiltro['INAT'][$item->INAT_ID] = (object)[
          'VALUE'     => $item->INAT_ID,
          'DESCRICAO' => $item->INAT_DESCRICAO ];
      }
      if( !key_exists($item->INAV_ID, $arrFiltro['INAV']) ){
        $arrFiltro['INAV'][$item->INAV_ID] = (object)[
          'VALUE'     => $item->INAV_ID,
          'DESCRICAO' => $item->INAV_CODIGO ];
      }
      if( !key_exists($item->INAV_LIQUIDEZ, $arrFiltro['LIQUIDEZ']) ){
        $arrFiltro['LIQUIDEZ'][$item->INAV_LIQUIDEZ] = (object)[
          'VALUE'     => $item->INAV_LIQUIDEZ,
          'DESCRICAO' => $item->INAV_LIQUIDEZ == 'sim' ? 'Sim' : 'Não' ];
      }
      if( !key_exists($dataVal, $arrFiltro['DATA']) ){
        $arrFiltro['DATA'][$dataVal] = (object)[
          'VALUE'     => $dataVal,
          'DESCRICAO' => $dataDesc];
      }
      if( !key_exists($cotaVal, $arrFiltro['COTAS']) ){
        $arrFiltro['COTAS'][$cotaVal] = (object)[
          'VALUE'     => $cotaVal,
          'DESCRICAO' => $cotaDesc ];
      }
    }

    return $this->orderArrayFiltro($arrFiltro, 'DESCRICAO');
  }
  
  private function filtroByOperacoes($items) {
    $arrFiltro = [
      'INCR'      => [],
      'INAT'      => [],
      'INAV'      => [],
      'CV'        => [],
      'INOD_DATA' => [],
    ];

    if( count($items) == 0 ) return $arrFiltro;

    foreach($items as $item){
      $dataVal  = date('Y-m-d',strtotime($item->INOD_DATA));
      $dataDesc = date('d/m/Y',strtotime($item->INOD_DATA));

      if( !key_exists($item->INCR_ID, $arrFiltro['INCR']) ){
        $arrFiltro['INCR'][$item->INCR_ID] = (object)[
          'VALUE'     => $item->INCR_ID,
          'DESCRICAO' => $item->INCR_DESCRICAO ];
      }
      if( !key_exists($item->INAT_ID, $arrFiltro['INAT']) ){
        $arrFiltro['INAT'][$item->INAT_ID] = (object)[
          'VALUE'     => $item->INAT_ID,
          'DESCRICAO' => $item->INAT_DESCRICAO ];
      }
      if( !key_exists($item->INAV_ID, $arrFiltro['INAV']) ){
        $arrFiltro['INAV'][$item->INAV_ID] = (object)[
          'VALUE'     => $item->INAV_ID,
          'DESCRICAO' => $item->INAV_CODIGO ];
      }
      if( !key_exists($item->INIT_CV, $arrFiltro['CV']) ){
        $arrFiltro['CV'][$item->INIT_CV] = (object)[
          'VALUE'     => $item->INIT_CV,
          'DESCRICAO' => $item->INIT_CV == 'C' ? 'Compra' : 'Venda' ];
      }
      if( !key_exists($dataVal, $arrFiltro['INOD_DATA']) ){
        $arrFiltro['INOD_DATA'][$dataVal] = (object)[
          'VALUE'     => $dataVal,
          'DESCRICAO' => $dataDesc];
      }
    }

    return $this->orderArrayFiltro($arrFiltro, 'DESCRICAO');
  }

  private function filtroByOrdem($items) {

    $arrFiltro = [
      'INCR' => [],
      'OBS' => [],
      'INOD_DATA' => [],
    ];
    
    if( count($items) == 0 ) return $arrFiltro;

    foreach($items as $item){
      $data = date('d/m/Y',strtotime($item->INOD_DATA));

      if(!key_exists($item->INCR_ID, $arrFiltro['INCR']) ){
        $arrFiltro['INCR'][$item->INCR_ID] = (object)[
          'VALUE'     => $item->INCR_ID,
          'DESCRICAO' => $item->INCR_DESCRICAO,
        ];
      }
      if(!key_exists($item->INOD_ID, $arrFiltro['OBS']) ){
        $arrFiltro['OBS'][$item->INOD_ID] = (object)[
          'VALUE'     => $item->INOD_ID,
          'DESCRICAO' => $item->INOD_DESCRICAO,
        ];
      }
      if(!key_exists($data, $arrFiltro['INOD_DATA']) ){
        $arrFiltro['INOD_DATA'][$data] = (object)[
          'VALUE'     => $item->INOD_DATA,
          'DESCRICAO' => $data,
        ];
      }
    }
    
    return $this->orderArrayFiltro($arrFiltro, 'DESCRICAO');
  }

  private function filtroByAtivoSplit($items) {
    
    $arrFiltro = [
      'INAT'            => [],
      'INAV'            => [],
      'INAS_TIPO'       => [],
      'INAS_QUANTIDADE' => [],
      'INAS_DATA'       => []
    ];
    
    if( count($items) == 0 ) return $arrFiltro;

    foreach($items as $item){
      $dataVal  = date('Y-m-d',strtotime($item->INAS_DATA));
      $dataDesc = date('d/m/Y',strtotime($item->INAS_DATA));

      if(!key_exists($item->INAT_ID, $arrFiltro['INAT']) ){
        $arrFiltro['INAT'][$item->INAT_ID] = (object)[
          'VALUE'     => $item->INAT_ID,
          'DESCRICAO' => $item->INAT_DESCRICAO ];
      }
      if(!key_exists($item->INAV_ID, $arrFiltro['INAV']) ){
        $arrFiltro['INAV'][$item->INAV_ID] = (object)[
          'VALUE'     => $item->INAV_ID,
          'DESCRICAO' => $item->INAV_CODIGO ];
      }
      if(!key_exists($item->INAS_TIPO, $arrFiltro['INAS_TIPO']) ){
        $arrFiltro['INAS_TIPO'][$item->INAS_TIPO] = (object)[
          'VALUE'     => $item->INAS_TIPO,
          'DESCRICAO' => $item->INAS_TIPO == 'S' ? 'Split' : 'Inplit' ];
      }
      if(!key_exists($item->INAS_QUANTIDADE, $arrFiltro['INAS_QUANTIDADE']) ){
        $arrFiltro['INAS_QUANTIDADE'][$item->INAS_QUANTIDADE] = (object)[
          'VALUE'     => $item->INAS_QUANTIDADE,
          'DESCRICAO' => $item->INAS_QUANTIDADE];
      }
      if(!key_exists($dataVal, $arrFiltro['INAS_DATA']) ){
        $arrFiltro['INAS_DATA'][$dataVal] = (object)[
          'VALUE'     => $dataVal,
          'DESCRICAO' => $dataDesc];
      }
    }

    return $this->orderArrayFiltro($arrFiltro, 'DESCRICAO');
  }

  private function filtroByRendimento($items) {
    
    $arrFiltro = [
      'INCR' => [],
      'INTP' => [],
      'INAT' => [],
      'INAV' => [],
      'INAR_TIPO' => [],
      'INAR_VALOR' => [],
      'INAR_DATA' => []
    ];
    
    if( count($items) == 0 ) return $arrFiltro;

    foreach($items as $item){
      $dataVal  = date('Y-m-d',strtotime($item->INAR_DATA));
      $dataDesc = date('d/m/Y',strtotime($item->INAR_DATA));

      if(!key_exists($item->INCR_ID, $arrFiltro['INCR']) ){ // -- INCR
        $arrFiltro['INCR'][$item->INCR_ID] = (object)[
          'VALUE'     => $item->INCR_ID,
          'DESCRICAO' => $item->INCR_DESCRICAO ];
      }
      if(!key_exists($item->INTP_ID, $arrFiltro['INTP']) ){ // -- INTP
        $arrFiltro['INTP'][$item->INTP_ID] = (object)[
          'VALUE'     => $item->INTP_ID,
          'DESCRICAO' => $item->INTP_DESCRICAO ];
      }
      if(!key_exists($item->INAT_ID, $arrFiltro['INAT']) ){ // -- INAT
        $arrFiltro['INAT'][$item->INAT_ID] = (object)[
          'VALUE'     => $item->INAT_ID,
          'DESCRICAO' => $item->INAT_DESCRICAO ];
      }
      if(!key_exists($item->INAV_ID, $arrFiltro['INAV']) ){ // -- INAV
        $arrFiltro['INAV'][$item->INAV_ID] = (object)[
          'VALUE'     => $item->INAV_ID,
          'DESCRICAO' => $item->INAV_CODIGO ];
      }
      if(!key_exists($item->INAR_TIPO, $arrFiltro['INAR_TIPO']) ){ // -- INCR
        if($item->INAR_TIPO == 'R') $desc = 'Rendimento';
        if($item->INAR_TIPO == 'D') $desc = 'Dividendo';
        if($item->INAR_TIPO == 'J') $desc = 'Juros sobre capital próprio';

        $arrFiltro['INAR_TIPO'][$item->INAR_TIPO] = (object)[
          'VALUE'     => $desc,
          'DESCRICAO' => $desc ];
      }
      if(!key_exists($item->INAR_VALOR, $arrFiltro['INAR_VALOR']) ){
        $arrFiltro['INAR_VALOR'][$item->INAR_VALOR] = (object)[
          'VALUE'     => $item->INAR_VALOR,
          'DESCRICAO' => $item->INAR_VALOR];
      }
      if(!key_exists($dataVal, $arrFiltro['INAR_DATA']) ){
        $arrFiltro['INAR_DATA'][$dataVal] = (object)[
          'VALUE'     => $dataVal,
          'DESCRICAO' => $dataDesc];
      }
    }

    return $this->orderArrayFiltro($arrFiltro, 'DESCRICAO');
  }

  private function orderArrayFiltro($arrFiltro, $alias) {

    foreach($arrFiltro as $key => $value){

      $column = array_map(function($val) use($alias) { 
        return $val->$alias; 
        }, array_values($arrFiltro[$key])
      );

      array_multisort($column, $arrFiltro[$key], SORT_ASC);

      $arrFiltro[$key] = array_values($arrFiltro[$key]);
    }

    return $arrFiltro;
  }
}