<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Model\Cofre\Busca    as C_BuscaModel;
use App\Model\Cofre\Item     as C_ItemModel;
use App\Model\Cofre\Tipo     as C_TipoModel;

use App\Model\Financa\Busca     as F_BuscaModel;
use App\Model\Financa\Item      as F_ItemModel;
use App\Model\Financa\Tipo      as F_TipoModel;
use App\Model\Financa\Situacao  as F_SituacaoModel;
use App\Model\Financa\ListaFixa as F_financaListaFixa;

use App\Model\Investimento\CarteiraItem    as I_CarteiraItem;
use App\Model\Investimento\Busca           as I_BuscaModel;
use App\Model\Investimento\Item            as I_ItemModel;
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
  }

  public function get($tipo) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é uma parametro obrigatório']);
    if(!isset($_GET['retorno'])) die( json_encode(['STATUS' => 'error', 'msg' => "o 'retorno' é uma parametro obrigatório"]) );


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
  private function getComponente() {

    // BUSCA ITEMS
    $options = [];
    $options['usuario'] = $_GET['usuario'];
    // ID
    if(isset($_GET['COCT_ID']))     $options['COCT_ID']     = $_GET['COCT_ID'];
    if(isset($_GET['INCT_ID']))     $options['INCT_ID']     = $_GET['INCT_ID'];
    if(isset($_GET['FINC_ID']))     $options['FINC_ID']     = $_GET['FINC_ID'];
    
    if(isset($_GET['INCR_ID']))     $options['INCR_ID']     = $_GET['INCR_ID'];
    if(isset($_GET['INTP_ID']))     $options['INTP_ID']     = $_GET['INTP_ID'];
    if(isset($_GET['INAV_ID']))     $options['INAV_ID']     = $_GET['INAV_ID'];
    if(isset($_GET['FIGP_ID']))     $options['FIGP_ID']     = $_GET['FIGP_ID'];
    if(isset($_GET['FICT_ID']))     $options['FICT_ID']     = $_GET['FICT_ID'];
    // STATUS
    if(isset($_GET['INIT_STATUS'])) $options['INIT_STATUS'] = $_GET['INIT_STATUS'];
    // DATE
    if(isset($_GET['data']))        $options['data']        = (!empty($_GET['data']))    ? $_GET['data']    : date('Y-m-d');
    if(isset($_GET['dataAte']))     $options['dataAte']     = (!empty($_GET['dataAte'])) ? $_GET['dataAte'] : date('Y-m-d');
    if(isset($_GET['dataAno']))     $options['dataAno']     = (!empty($_GET['dataAno'])) ? $_GET['dataAno'] : date('Y-m');

    // --

    $modulos = $this->validaModules($_GET['retorno']);
    $arrRetorno = [];

    if($modulos['I_']){
      if(!isset($options['INCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => "o INCT_ID é uma parametro obrigatório"]) );
      if(!isset($options['dataAte'])) die( json_encode(['STATUS' => 'error', 'msg' => "o dataAte é uma parametro obrigatório"]) );

      if(
        in_array('I_carteiraSaldo', $modulos['retorno']) ||
        in_array('I_carteiraComposicao', $modulos['retorno']) ||
        in_array('I_carteiraAnaliseTipo', $modulos['retorno'])
      ) {
        $itemsMes = $this->I_analiseItensBase(
          $this->I_carteiraItem->get([
            'usuario' => $options['usuario'],
            'INCT_ID' => $options['INCT_ID'],
            'data'    => $options['dataAte'],
          ])
        );
      }
      
      if(
        in_array('I_carteiraAno', $modulos['retorno'])
      ) {
        $itemsAno = $this->I_analiseItensBase(
          $this->I_carteiraItem->get([
            'usuario' => $options['usuario'],
            'INCT_ID' => $options['INCT_ID'],
            'dataAno' => $options['dataAte'],
          ])
        );
      }

      if(in_array('I_carteiraSaldo', $modulos['retorno'])){
        $arrRetorno['I_carteiraSaldo'] = $this->I_carteiraSaldo($itemsMes);
      }
      if(in_array('I_carteiraComposicao', $modulos['retorno'])){
        $arrRetorno['I_carteiraComposicao'] = $this->I_carteiraComposicao($itemsMes);
      }
      if(in_array('I_carteiraAnaliseTipo', $modulos['retorno'])){
        $arrRetorno['I_carteiraAnaliseTipo']['INTP'] = $this->I_carteiraAnaliseTipo($itemsMes, 'INTP', true);
        $arrRetorno['I_carteiraAnaliseTipo']['INAT'] = $this->I_carteiraAnaliseTipo($itemsMes, 'INAT', true);
        $arrRetorno['I_carteiraAnaliseTipo']['INAV'] = $this->I_carteiraAnaliseTipo($itemsMes, 'INAV', true);
      }
      if(in_array('I_carteiraAno', $modulos['retorno'])){
        $items = $this->I_item->get([
          'usuario' => $options['usuario'],
          'dataAno' => $options['dataAte'],
        ]);

        $arrRetorno['I_carteiraAno'] = $this->I_carteiraAno($itemsAno, $items, $options['dataAte']);
      }
    }
    if($modulos['C_']){
      if(!isset($_GET['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => "o COCT_ID é uma parametro obrigatório"]) );


      $itemsData = $this->C_item->get([
        'usuario'     => $options['usuario'],
        'COCT_ID'     => $options['COCT_ID'],
        'dataAte'     => $options['dataAte'],
        'COIT_STATUS' => 1,
      ]);

      $itemsDataAno = $this->C_item->get([
        'usuario'     => $options['usuario'],
        'COCT_ID'     => $options['COCT_ID'],
        'dataAno'     => $options['dataAno'],
        'COIT_STATUS' => 1,
      ]);
        

      if(in_array('C_carteiraSaldo', $modulos['retorno'])){
        $arrRetorno['C_carteiraSaldo'] = $this->C_carteiraSaldo($itemsData);
      }
      if(in_array('C_carteiraGraficoAno', $modulos['retorno'])){
        $arrRetorno['C_carteiraGraficoAno'] = $this->C_carteiraGraficoAno($itemsDataAno);
      }
      if(in_array('C_carteiraComposicao', $modulos['retorno'])){
        $arrRetorno['C_carteiraComposicao'] = $this->C_carteiraComposicao($itemsData);
      }

    }
    if($modulos['F_']){
      if(!isset($options['FINC_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o FINC_ID é uma parametro obrigatório']) );

      if(
        in_array('F_carteiraSaldo', $modulos['retorno']) ||
        in_array('F_mesGeral', $modulos['retorno'])
      ) {
        if(!isset($options['data'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o data é uma parametro obrigatório']) );

        $itemsData = $this->F_item->get([
          'usuario'     => $options['usuario'],
          'FINC_ID'     => $options['FINC_ID'],
          'data'        => $options['data'],
          'FNIT_STATUS' => 1,
        ]);
        $itemsDataReceita = array_filter($itemsData, function($item) { return $item->FITP_ID == 1; });
        $itemsDataDespesa = array_filter($itemsData, function($item) { return $item->FITP_ID == 2; });
      }

      if(
        in_array('F_anoConsolidado', $modulos['retorno']) ||
        in_array('F_analiseGrupo', $modulos['retorno']) ||
        in_array('F_analiseAno', $modulos['retorno'])
      ) {
        if(!isset($options['data'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o data é uma parametro obrigatório']) );

        $itemsAno = $this->F_item->get([
          'usuario'     => $options['usuario'],
          'FINC_ID'     => $options['FINC_ID'],
          'FIGP_ID'     => isset($options['FIGP_ID']) ? $options['FIGP_ID'] : false,
          'FICT_ID'     => isset($options['FICT_ID']) ? $options['FICT_ID'] : false,
          'dataAno'     => $options['data'],
          'FNIT_STATUS' => 1,
        ]);
      }

      
      if(in_array('F_carteiraSaldo', $modulos['retorno'])) {
        $arrRetorno['F_carteiraSaldo'] = $this->F_carteiraSaldo($itemsData);
      }
      if(in_array('F_mesGeral', $modulos['retorno'])) {
        $arrRetorno['F_mesGeral']['RECEITA'] = $this->F_mesGeral($itemsDataReceita);
        $arrRetorno['F_mesGeral']['DESPESA'] = $this->F_mesGeral($itemsDataDespesa);
      }
      if(in_array('F_anoConsolidado', $modulos['retorno'])) {
        $arrRetorno['F_anoConsolidado'] = $this->F_anoConsolidado($itemsAno);
      }
      if(in_array('F_analiseGrupo', $modulos['retorno'])) {
      
        $anoAte  = intval(substr($options['data'], 0 , 4));
        $anoDe   = intval($anoAte - 4);

        $items5Anos = $this->F_item->get([
          'usuario'     => $options['usuario'],
          'FINC_ID'     => $options['FINC_ID'],
          'FICT_ID'     => isset($options['FICT_ID']) ? $options['FICT_ID'] : false,
          'FNIT_STATUS' => 1,
          'dataDe'      => $anoDe,
          'dataAte'     => $anoAte,
        ]);

        $arrRetorno['F_analiseGrupo'] = $this->F_analiseGrupo($itemsAno, $items5Anos, $anoDe, $anoAte);
      }
      if(in_array('F_analiseMes', $modulos['retorno'])) {
        $options['dataDe'] = date('Y-m', strtotime('-11 month', strtotime($options['data'])) );

        $itemsUltimosMeses = $this->F_item->get([
          'usuario'     => $options['usuario'],
          'FINC_ID'     => $options['FINC_ID'],
          'dataDe'      => $options['dataDe'],
          'dataAte'     => $options['data'],
          'FNIT_STATUS' => 1,
        ]);
        
        $arrRetorno['F_analiseMes'] = $this->F_analiseMes($options, $itemsUltimosMeses);
      }
      if(in_array('F_analiseAno', $modulos['retorno'])) {
        $arrRetorno['F_analiseAno'] = $this->F_analiseAno($options, $itemsAno);
      }

    }

    return $arrRetorno;
  }
  private function I_carteiraSaldo($itemsMes) {
    $tmpRetorno = [
      'TOTAL'            => 0,
      'BRUTO'            => 0,
      'MES_RENDIMENTO'   => 0,
      'MES_DIVIDENDO'    => 0,
      'MES_JSCP'         => 0,
      'TOTAL_RENDIMENTO' => 0,
      'TOTAL_DIVIDENDO'  => 0,
      'TOTAL_JSCP'       => 0,
    ];

    if(count($itemsMes) == 0) return $tmpRetorno;
    
    // --

    foreach ($itemsMes as $item) {
      $tmpRetorno['TOTAL']            = number_format($tmpRetorno['TOTAL']            + $item->TOTAL, 2, '.', '');
      $tmpRetorno['BRUTO']            = number_format($tmpRetorno['BRUTO']            + $item->BRUTO, 2, '.', '');
      $tmpRetorno['MES_RENDIMENTO']   = number_format($tmpRetorno['MES_RENDIMENTO']   + $item->MES_RENDIMENTO, 2, '.', '');
      $tmpRetorno['MES_DIVIDENDO']    = number_format($tmpRetorno['MES_DIVIDENDO']    + $item->MES_DIVIDENDO, 2, '.', '');
      $tmpRetorno['MES_JSCP']         = number_format($tmpRetorno['MES_JSCP']         + $item->MES_JSCP, 2, '.', '');
      $tmpRetorno['TOTAL_RENDIMENTO'] = number_format($tmpRetorno['TOTAL_RENDIMENTO'] + $item->TOTAL_RENDIMENTO, 2, '.', '');
      $tmpRetorno['TOTAL_DIVIDENDO']  = number_format($tmpRetorno['TOTAL_DIVIDENDO']  + $item->TOTAL_DIVIDENDO, 2, '.', '');
      $tmpRetorno['TOTAL_JSCP']       = number_format($tmpRetorno['TOTAL_JSCP']       + $item->TOTAL_JSCP, 2, '.', '');
    }

    unset($itemsMes);

    return $tmpRetorno;
  }
  private function I_carteiraComposicao($itemsMes) {
    $tmpRetorno = [
      'items'       => [],
      'itemsFiltro' => []
    ];

    if(count($itemsMes) == 0) return $tmpRetorno;
      
    // --

    $itemsMes = $this->orderArrayFiltro([$itemsMes], 'INAV_CODIGO')[0];

    $itemsMes = array_merge(
      array_filter( $itemsMes, function($item) { return $item->COTAS > 0; }),
      array_filter( $itemsMes, function($item) { return $item->COTAS <= 0; })
    );
    
    $itemsMes = array_values($itemsMes);

    // --

    $tmpRetorno['items']      = $itemsMes;
    $tmpRetorno['itemsFiltro'] = $this->I_filtroByItems($itemsMes); // monta ItemFiltro

    return $tmpRetorno;
  }
  private function I_carteiraAnaliseTipo($items, $tipo, $comCotas = false) {
    $tmpRetorno = [
      'items'         => [],
      'items_GRAFICO' => [
        'labels' => [],
        'valores' => [],
      ],
    ];

    if(count($items) == 0) return $tmpRetorno;
      
    // --

    $items = $this->I_apuraArrayItemsByKey($items, $tipo);
    
    if($comCotas)
      $items = array_filter($items, function($item) {return $item->COTAS > 0; });

    foreach ($items as $key => $item) {
      $tmpRetorno['items_GRAFICO']['labels'][]  = $item->DESCRICAO;
      $tmpRetorno['items_GRAFICO']['valores'][] = $item->BRUTO;
    }

    $tmpRetorno['items'] = array_values($items);

    return $tmpRetorno;
  }
  private function I_carteiraAno($itemsAno, $itemsOrdem, $dataAte) {
    
    $arrRetorno = [];
    $strAno = substr($dataAte, 0 , 4);

    for ($i=1; $i <= 12; $i++) {
        $strMes     = (strlen($i) == 1) ? "0{$i}" : $i;
        $strDia     = cal_days_in_month(CAL_GREGORIAN, $strMes , $strAno);
        
        // -- APORTES
          $dataSearch = "{$strAno}-{$strMes}-$strDia";

          $itemsFiltro = array_filter($itemsOrdem, function($item) use($dataSearch) {
            $mesItem   = date('Y-m', strtotime($item->INOD_DATA));
            $mesSearch = date('Y-m', strtotime($dataSearch));
            return $mesItem == $mesSearch;
          });
          
          $arrRetorno['aporte'][$strMes] = 0;
          $arrRetorno['aporteCompra'][$strMes] = 0;
          $arrRetorno['aporteVenda'][$strMes] = 0;

          foreach ($itemsFiltro as $item) {
            if($item->INIT_CV == 'C'){
              $arrRetorno['aporteCompra'][$strMes] = number_format($arrRetorno['aporteCompra'][$strMes] + $item->INIT_PRECO_TOTAL, 2, '.', '');
              $n = $arrRetorno['aporte'][$strMes] + $item->INIT_PRECO_TOTAL;
            }
            if($item->INIT_CV == 'V'){
              $arrRetorno['aporteVenda'][$strMes] = number_format($arrRetorno['aporteVenda'][$strMes] + $item->INIT_PRECO_TOTAL, 2, '.', '');
              $n = $arrRetorno['aporte'][$strMes] - $item->INIT_PRECO_TOTAL;
            }
            $arrRetorno['aporte'][$strMes] = number_format($n, 2, '.', '');
          }
        // -- APORTES - FIM


        // -- CARTEIRA/RENDIMENTO
          $dataSearch = "{$strAno}-{$strMes}-01";
          
          $itemsFiltro = array_filter($itemsAno, function($item) use($dataSearch) {
            $mesItem   = date('Y-m', strtotime($item->INCTC_DATA));
            $mesSearch = date('Y-m', strtotime($dataSearch));
            return $mesItem == $mesSearch;
          });
          
        $arrRetorno['carteira'][$strMes] = 0;
        $arrRetorno['rendimentoMes'][$strMes] = 0;
        $arrRetorno['rendimentoAno'][$strMes] = 0;
    
        foreach ($itemsFiltro as $item) {
          $arrRetorno['carteira'][$strMes]      = number_format($arrRetorno['carteira'][$strMes]      + $item->BRUTO, 2, '.', '');
          $arrRetorno['rendimentoMes'][$strMes] = number_format($arrRetorno['rendimentoMes'][$strMes] + $item->MES_RENDIMENTO   + $item->MES_DIVIDENDO + $item->MES_JSCP, 2, '.', '');
          $arrRetorno['rendimentoAno'][$strMes] = number_format($arrRetorno['rendimentoAno'][$strMes] + $item->TOTAL_RENDIMENTO + $item->TOTAL_DIVIDENDO + $item->TOTAL_JSCP, 2, '.', '');
        }
    
    }

    $arrRetorno['carteira']      = array_values($arrRetorno['carteira']);
    $arrRetorno['aporte']        = array_values($arrRetorno['aporte']);
    $arrRetorno['aporteCompra']  = array_values($arrRetorno['aporteCompra']);
    $arrRetorno['aporteVenda']   = array_values($arrRetorno['aporteVenda']);
    $arrRetorno['rendimentoMes'] = array_values($arrRetorno['rendimentoMes']);
    $arrRetorno['rendimentoAno'] = array_values($arrRetorno['rendimentoAno']);

    return $arrRetorno;
  }
  private function C_carteiraSaldo($items) {
    $arrRetorno = [
      'ENTRADA'  => 0,
      'RETIRADA' => 0,
      'SALDO'    => 0,
    ];

    if(count($items) == 0) return $arrRetorno;

    //-- 

    foreach ($items as $item) {
      if($item->COTP_ID == 1) $arrRetorno['ENTRADA']  += $item->COIT_VALOR;
      if($item->COTP_ID == 2) $arrRetorno['RETIRADA'] += $item->COIT_VALOR;

      $arrRetorno['ENTRADA']  = number_format( $arrRetorno['ENTRADA'], 2, '.', '');
      $arrRetorno['RETIRADA'] = number_format( $arrRetorno['RETIRADA'], 2, '.', '');
      $arrRetorno['SALDO']    = number_format( $arrRetorno['ENTRADA'] - $arrRetorno['RETIRADA'] , 2, '.', '');

      unset($item);
    }

    return $arrRetorno;
  }
  private function C_carteiraGraficoAno($items) {
    $arrRetorno = [
      'ENTRADA'  => 0,
      'RETIRADA' => 0,
      'SALDO'    => 0,
    ];

    if(count($items) == 0) return $arrRetorno;


    // monta Array retornos 'SALDO', 'ENTRADA' e 'Retirada'
    $keys = [];
    foreach (['Saldo', 'Entrada', 'Retirada'] as $key => $text) {
      for ($i=1; $i <= 12; $i++) { 
        $keys[$text][$i] = number_format(0, 2, '.', '');
      }
    }
    
    // apura os valores para cada KEY de acordo com o TIPO
    foreach ($items as $key => $item) {
      $mes = date('n', strtotime($item->COIT_DATA));

      $soma = $keys[$item->COTP_DESCRICAO][$mes] + $item->COIT_VALOR;
      $keys[$item->COTP_DESCRICAO][$mes] = number_format($soma, 2, '.', '');
      
      unset($items[$key]);
    }


    // apura o Saldo de cada Mês
    for ($i=1; $i <= 12; $i++) { 

      $Saldo    = $keys['Saldo'][$i];
      $Entrada  = $keys['Entrada'][$i];
      $Retirada = $keys['Retirada'][$i];
      
      // resgata saldo do mes anterior para o mes atual do loop
      if( $i > 1 ) { 
        $meAnterior = $i - 1;
        $Saldo += $keys['Saldo'][$meAnterior];
      } 

      $Saldo += $Entrada;
      $Saldo -= $Retirada;
      $keys['Saldo'][$i] = number_format($Saldo, 2, '.', '');
    }

    $keys['Retirada'] = array_map(function($val){ return $val != 0 ? "-{$val}" : "0";}, $keys['Retirada']);

    return [
      'SALDO'    => array_values($keys['Saldo']),
      'ENTRADA'  => array_values($keys['Entrada']),
      'RETIRADA' => array_values($keys['Retirada']),
    ];
  }
  private function C_carteiraComposicao($items) {

    $arrRetorno = [
      'items' => [],
      'consolidadoGrafico' => [
        'labels'     => [],
        'valores'    => [],
        'percentual' => []
      ]
    ];

    if(count($items) == 0) return $arrRetorno;


    // MONTA ARRAY DE DADOS
    foreach($items as $key => $item) { 
      $strKey = "{$item->COIT_PROPOSITO}-{$item->COTP_ID}";

      if(!isset($items[$strKey])){
        $items[$strKey] = (object)[
          "COIT_PROPOSITO"  => $item->COIT_PROPOSITO,
          "COIT_SOMA"       => 0,
          "COIT_PERCENTUAL" => 0,
          "COTP_ID"         => $item->COTP_ID,
        ];
      };
      
      if($item->COTP_ID == 1) $items[$strKey]->COIT_SOMA += number_format($item->COIT_VALOR, 3, '.', '' ); 
      if($item->COTP_ID == 2) $items[$strKey]->COIT_SOMA += number_format($item->COIT_VALOR, 3, '.', '' ); 
      unset($items[$key]);
    }

    
    // APURA TOTAL DA CARTEIRA PARA CALCULAR PERCENTUAL POR PROPOSITO
    $TOTAL         = 0;
    $totalEntrada  = 0;
    $totalRetirada = 0;

    foreach($items as $item) { 
      if($item->COTP_ID == 1) $totalEntrada  += number_format($item->COIT_SOMA, 3, '.', '' ); 
      if($item->COTP_ID == 2) $totalRetirada += number_format($item->COIT_SOMA, 3, '.', '' ); 
      $TOTAL = number_format($totalEntrada - $totalRetirada, 3, '.', '' );
    }
    unset($totalEntrada);
    unset($totalRetirada);


    foreach ($items as $key => $item) {
      if( !key_exists($item->COIT_PROPOSITO, $arrRetorno['items']) ) {
        $arrRetorno['items'][$item->COIT_PROPOSITO] = (object)[
          'COIT_PROPOSITO'  => $item->COIT_PROPOSITO,
          'COIT_ENTRADA'    => 0,
          'COIT_RETIRADA'   => 0,
          'COIT_SALDO'      => 0,
          'COIT_PERCENTUAL' => 0,
        ];
      }

      // SOMA 'ENTRADA' e 'RETIRADA'
      if($item->COTP_ID == 1) {
        $n = $arrRetorno['items'][$item->COIT_PROPOSITO]->COIT_ENTRADA;
        $arrRetorno['items'][$item->COIT_PROPOSITO]->COIT_ENTRADA  = number_format(($n + $item->COIT_SOMA), 2, '.', '');
      }
      if($item->COTP_ID == 2) {
        $n = $arrRetorno['items'][$item->COIT_PROPOSITO]->COIT_ENTRADA;
        $arrRetorno['items'][$item->COIT_PROPOSITO]->COIT_RETIRADA  = number_format(($n + $item->COIT_SOMA), 2, '.', '');
      }
      
      
      // CALCULA 'SALDO'
      $COIT_ENTRADA  = $arrRetorno['items'][$item->COIT_PROPOSITO]->COIT_ENTRADA;
      $COIT_RETIRADA = $arrRetorno['items'][$item->COIT_PROPOSITO]->COIT_RETIRADA;


      $COIT_SALDO    = number_format(($COIT_ENTRADA - $COIT_RETIRADA), 2, '.', '');
      $arrRetorno['items'][$item->COIT_PROPOSITO]->COIT_SALDO = $COIT_SALDO;

      // CALCULA 'PERCENTUAL'
      $COIT_PERCENTUAL = ( $COIT_SALDO > 0 && $TOTAL > 0) ? $COIT_SALDO / $TOTAL : 0;
      $COIT_PERCENTUAL = number_format($COIT_PERCENTUAL, 2, '.', '' );
      $arrRetorno['items'][$item->COIT_PROPOSITO]->COIT_PERCENTUAL = "{$COIT_PERCENTUAL}%";

      // REMOVE PROPOSITO QUANDO TIVER VALOR IGUAL A 'ZERADO'
      if($COIT_SALDO == 0) unset($arrRetorno['items'][$item->COIT_PROPOSITO]);
    }

    
    // ATRIBUI VALORES AO ARRAY GRAFICO
    foreach($arrRetorno['items'] as $item) {

      $arrRetorno['consolidadoGrafico']['labels'][]     = $item->COIT_PROPOSITO;
      $arrRetorno['consolidadoGrafico']['valores'][]    = $item->COIT_SALDO;
      $arrRetorno['consolidadoGrafico']['percentual'][] = $item->COIT_PERCENTUAL;
    }

    return [
      'items' => array_values($arrRetorno['items']),
      'consolidadoGrafico' =>  $arrRetorno['consolidadoGrafico'],
    ];
  }
  private function F_carteiraSaldo($items) {

    $arrRetorno = [
      'RECEITA'  => 0,
      'DESPESA'  => 0,
      'SOBRA'    => 0,
      'ESTIMADO' => 0
    ];
    $itemsPagos = [
      'RECEITA'  => 0,
      'DESPESA'  => 0,
    ];

    if(count($items) == 0) return $arrRetorno;

    // --

    foreach ($items as $item) {
      // soma valores PAGOS
      if( $item->FNIS_ID == 1 ) {
        if($item->FITP_ID == 1) $itemsPagos['RECEITA'] = number_format($itemsPagos['RECEITA'] + $item->FNIT_VALOR, 2, '.', '');
        if($item->FITP_ID == 2) $itemsPagos['DESPESA'] = number_format($itemsPagos['DESPESA'] + $item->FNIT_VALOR, 2, '.', '');
      }
      // soma todos os valores, consolidar estimado
      if($item->FITP_ID == 1) $arrRetorno['RECEITA'] = number_format($arrRetorno['RECEITA'] + $item->FNIT_VALOR, 2, '.', '');
      if($item->FITP_ID == 2) $arrRetorno['DESPESA'] = number_format($arrRetorno['DESPESA'] + $item->FNIT_VALOR, 2, '.', '');
    }
    
    return [
      'RECEITA'  => number_format($itemsPagos['RECEITA'], 2, '.', ''),
      'DESPESA'  => number_format($itemsPagos['DESPESA'], 2, '.', ''),
      'SOBRA'    => number_format($itemsPagos['RECEITA'] - $itemsPagos['DESPESA'], 2, '.', ''),
      'ESTIMADO' => number_format($arrRetorno['RECEITA'] - $arrRetorno['DESPESA'], 2, '.', ''),
    ];
  }
  private function F_mesGeral($items) {

    $resultado = [ 
      'FIGP' => [], 
      'FICT' => [], 
      'FIGP_GRAFICO' => [ 'labels' => [], 'valores' => [] ], 
      'FICT_GRAFICO' => [ 'labels' => [], 'valores' => [] ] 
    ];
    
    if( count($items) == 0) return $resultado;

    // --

    $Total= 0;
    foreach($items as $item) {
      $item = (array)$item;
      
      $FNIT_VALOR     = $item['FNIT_VALOR'];
      // $FITP_DESCRICAO = strtoupper($item['FITP_DESCRICAO']); // DESCRIÇÃO DO TIPO EM MAIUSCULO
      $FIGP_ID        = $item['FIGP_ID'];
      $FIGP_DESCRICAO = $item['FIGP_DESCRICAO'];
      $FICT_ID        = $item['FICT_ID'];
      $FICT_DESCRICAO = $item['FICT_DESCRICAO'];

      // ADICIONA ARRAY DE GRUPO/CATEGORIA
      $resultado = $this->F_renderItem('FIGP', $FIGP_ID, $FIGP_DESCRICAO, $resultado);
      $resultado = $this->F_renderItem('FICT', $FICT_ID, $FICT_DESCRICAO, $resultado);

      // SOMA VALORES TOTAIS 
      $Total = number_format($Total + $FNIT_VALOR, 2,'.', '');
      
      $resultado = $this->F_somaValores('FIGP', $item, $resultado, $Total); 
      $resultado = $this->F_somaValores('FICT', $item, $resultado, $Total); 
    }


    // CONVERT 'Object of Objects' TO 'ARRAY of Objects'
    foreach ($resultado as $key_tipo => $items) {
        if(strlen($key_tipo) == 4){
          $arr = array_values($items);

          // ORDENAÇÃO POR DESCRICAO
          $columns = array_map(function($val) { return $val->DESCRICAO; }, $arr);
          array_multisort($columns, $arr);

          // --

          $resultado[$key_tipo] = $arr;

          $labels   = array_map(function($val){ return $val->DESCRICAO; }, $arr);
          $valores = array_map(function($val){ return $val->PAGO;      }, $arr);
  
          // ADICIONA labels E VALORES
          $resultado["{$key_tipo}_GRAFICO"] = [ 
            'labels'  => array_values($labels), 
            'valores' => array_values($valores)
          ];
        }
    }

    return $resultado;
  }
  private function F_anoConsolidado($items){

    if(count($items) == 0) 
      return [
        'RECEITA'  => 0,
        'DESPESA'  => 0,
        'SOBRA'    => 0,
        'ESTIMADO' => 0,
      ];

    // --

    $meses          = [];
    for ($i = 1; $i <= 12; $i ++) { 
      $key = strlen($i) == 1 ? "0{$i}" : "{$i}";
      $meses[$key] = number_format(0 , 2, '.', '');
    }
    $itemsPagoGrafico = [
      'RECEITA'  => $meses,
      'DESPESA'  => $meses,
      'SOBRA'    => $meses,
      'ESTIMADO' => $meses,
    ];
    $itemsEstimadoGrafico = [
      'RECEITA'  => $meses,
      'DESPESA'  => $meses,
      'SOBRA'    => $meses,
      'ESTIMADO' => $meses,
    ];
    unset($meses);


    for ($i=1; $i<=12; $i++) { 
      $alias = "Mes_" . $i;

      $itemsPago[$alias] = (object)[
        'RECEITA'  => 0,
        'DESPESA'  => 0,
        'SOBRA'    => 0,
        'ESTIMADO' => 0,
      ];
      $itemsEstimado[$alias]      = (object)[
        'RECEITA'  => 0,
        'DESPESA'  => 0,
        'SOBRA'    => 0,
        'ESTIMADO' => 0,
      ];
    }

    // --

    foreach ($items as $item) {
      $Mes_num = "Mes_" . date('n', strtotime($item->FNIT_DATA));
      $num_mes = date('m', strtotime($item->FNIT_DATA));

      // soma valores PAGOS, consolidar sobra
      if( $item->FNIS_ID == 1 ){
        if($item->FITP_ID == 1){
          $itemsPago[$Mes_num]->RECEITA          = number_format($itemsPago[$Mes_num]->RECEITA          + $item->FNIT_VALOR, 2, '.', '');
          $itemsPagoGrafico['RECEITA'][$num_mes] = number_format($itemsPagoGrafico['RECEITA'][$num_mes] + $item->FNIT_VALOR, 2, '.','');
        }
        if($item->FITP_ID == 2){
          $itemsPago[$Mes_num]->DESPESA          = number_format($itemsPago[$Mes_num]->DESPESA          + $item->FNIT_VALOR, 2, '.', '');
          $itemsPagoGrafico['DESPESA'][$num_mes] = number_format($itemsPagoGrafico['DESPESA'][$num_mes] + $item->FNIT_VALOR, 2, '.','');
        }

        $itemsPago[$Mes_num]->SOBRA          = number_format($itemsPago[$Mes_num]->RECEITA - $itemsPago[$Mes_num]->DESPESA , 2, '.', '');
        $itemsPagoGrafico['SOBRA'][$num_mes] = number_format($itemsPagoGrafico['RECEITA'][$num_mes] - $itemsPagoGrafico['DESPESA'][$num_mes], 2, '.','');
      }

      // soma todos os valores, consolidar estimado
      if($item->FITP_ID == 1){
        $itemsEstimado[$Mes_num]->RECEITA  = number_format($itemsEstimado[$Mes_num]->RECEITA + $item->FNIT_VALOR, 2, '.', '');
        $itemsEstimadoGrafico['RECEITA'][$num_mes] = number_format($itemsEstimadoGrafico['RECEITA'][$num_mes] + $item->FNIT_VALOR, 2, '.','');
      }
      if($item->FITP_ID == 2){
        $itemsEstimado[$Mes_num]->DESPESA  = number_format($itemsEstimado[$Mes_num]->DESPESA + $item->FNIT_VALOR, 2, '.', '');
        $itemsEstimadoGrafico['DESPESA'][$num_mes] = number_format($itemsEstimadoGrafico['DESPESA'][$num_mes] + $item->FNIT_VALOR, 2, '.','');
      }

      $itemsPago[$Mes_num]->ESTIMADO          = number_format($itemsEstimado[$Mes_num]->RECEITA - $itemsEstimado[$alias]->DESPESA , 2, '.', '');
      $itemsPagoGrafico['ESTIMADO'][$num_mes] = number_format($itemsEstimadoGrafico['RECEITA'][$num_mes] - $itemsEstimadoGrafico['DESPESA'][$num_mes], 2, '.','');
    }

    unset($items);
    unset($itemsEstimado);

    $items[] = array_merge( ['RECEITA'],  array_values(array_map(function($mes){ return number_format($mes->RECEITA , 2, '.', ''); }, $itemsPago)));
    $items[] = array_merge( ['DESPESA'],  array_values(array_map(function($mes){ return number_format($mes->DESPESA , 2, '.', ''); }, $itemsPago)));
    $items[] = array_merge( ['SOBRA'],    array_values(array_map(function($mes){ return number_format($mes->SOBRA   , 2, '.', ''); }, $itemsPago)));
    $items[] = array_merge( ['ESTIMADO'], array_values(array_map(function($mes){ return number_format($mes->ESTIMADO, 2, '.', ''); }, $itemsPago)));
    
    $itemsGrafico['RECEITA']  = array_values($itemsPagoGrafico['RECEITA']);
    $itemsGrafico['DESPESA']  = array_values($itemsPagoGrafico['DESPESA']);
    $itemsGrafico['SOBRA']    = array_values($itemsPagoGrafico['SOBRA']);
    $itemsGrafico['ESTIMADO'] = array_values($itemsPagoGrafico['ESTIMADO']);
    
    unset($itemsPago);
    unset($itemsPagoGrafico);

    return [
      'items' => $items,
      'itemsGrafico' => $itemsGrafico,
    ];
  }
  private function F_analiseGrupo($itemsAno, $items5Anos, $anoDe, $anoAte){
    
    $arrRetorno = [
      'itemsLista' => [],
      'analiseAno' => [],
      'analiseAnoGrafico' => [
        'label'   => [],
        'valores' => [],
      ],
      'analise5Anos' => [],
    ];

    // --

    for ($i=1; $i <= 12 ; $i++) { 
      $data   = "01-$i-2020";
      $mes    = ucfirst( strftime('%B', strtotime($data)) );
      $mesNum = date( 'm', strtotime($data));

      $meses[$mesNum] = 0;

      $arrRetorno['itemsLista'][$mesNum] = [
        'MES'   => utf8_encode($mes),
        'LISTA' => [],
      ];
      $arrRetorno['analiseAno'][$mesNum] = [ 
        'MES' => utf8_encode($mes), 
        'VALOR' => 0 
      ];

      $arrRetorno['analiseAnoGrafico']['label'][] = utf8_encode($mes);
      $arrRetorno['analiseAnoGrafico']['valores'][] = 0;
    }

    // ordena meses
    ksort($meses);

    for ( $ano = $anoAte ; $ano >= $anoDe ; $ano-- ) {
      $arrRetorno['analise5Anos'][$ano] = [
          'ANO'   => $ano,
          'TOTAL' => 0,
          'MESES' => $meses
      ];
    }

    // loop itemsAno
    foreach ($itemsAno as $item) {
      $item     = (object)$item; 
      $mesNum   = date( 'm', strtotime($item->FNIT_DATA));
      $anoNum   = date( 'Y', strtotime($item->FNIT_DATA));
      $keyArray = intval($mesNum) - 1;
      
      // ADICIONA ITEM A LISTA
      $arrRetorno['itemsLista'][$mesNum]['LISTA'][] = $item;
      
      // ADICIONA VALOR AO MES
      $arrRetorno['analiseAno'][$mesNum]['VALOR'] = number_format($arrRetorno['analiseAno'][$mesNum]['VALOR'] + $item->FNIT_VALOR, 2, '.', '');
      
      // ADICIONA SOMA AO MES
      $arrRetorno['analiseAnoGrafico']['valores'][$keyArray] = $arrRetorno['analiseAno'][$mesNum]['VALOR'];
    }

    
    // loop items5Anos
    foreach ($items5Anos as $item) {
      $item     = (object)$item; 
      $mesNum   = date( 'm', strtotime($item->FNIT_DATA));
      $anoNum   = date( 'Y', strtotime($item->FNIT_DATA));

      // SOMA VALOR TOTAL
      $arrRetorno['analise5Anos'][$anoNum]['TOTAL'] = number_format($arrRetorno['analise5Anos'][$anoNum]['TOTAL'] + $item->FNIT_VALOR, 2, '.', '');

      // SOMA VALOR DO MES
      $arrRetorno['analise5Anos'][$anoNum]['MESES'][$mesNum] = number_format($arrRetorno['analise5Anos'][$anoNum]['MESES'][$mesNum] + $item->FNIT_VALOR, 2, '.', '');
    }

    // --
    
    $arrRetorno['itemsLista']   = array_values($arrRetorno['itemsLista']);
    $arrRetorno['analiseAno']   = array_values($arrRetorno['analiseAno']);
    $arrRetorno['analise5Anos'] = array_values($arrRetorno['analise5Anos']);
    $arrRetorno['analise5Anos'] = array_map(function($value){ return array_merge( [$value['ANO']], [$value['TOTAL']], array_values($value['MESES']) ); },$arrRetorno['analise5Anos']);

    return $arrRetorno;
  }
  private function F_analiseMes($options, $itemsUltimosMeses){
    $resultado = [
      'RECEITA' => [ 
        'FIGP' => [], 
        'FICT' => [],
        'FIGP_GRAFICO' => ['labels' => [], 'valores' => [] ], 
        'FICT_GRAFICO' => ['labels' => [], 'valores' => [] ],
      ],
      'DESPESA' => [ 
        'FIGP' => [], 
        'FICT' => [],
        'FIGP_GRAFICO' => ['labels' => [], 'valores' => [] ], 
        'FICT_GRAFICO' => ['labels' => [], 'valores' => [] ],
      ],
      'th' => []
    ];

    if(count($itemsUltimosMeses) == 0) return $resultado;

    // --

    $meses    = [];
    $dateInit = date('d-m-Y', strtotime($options['data'] . '-01'));
    for ($i = 0; $i < 12; $i++) { 
      // atualiza para -1 mes apos a primeira interação
      if($i > 0) $dateInit = date("d-m-Y", strtotime($dateInit. "-1 month"));

      // adicione em um array os meses para usar na tabela
      $th['th'][] = date("m-Y", strtotime($dateInit));

      // cria array retorno
      $key_mes = date('m_Y', strtotime($dateInit));
      $meses[$key_mes] = 0;
    }

    return array_merge(
      $this->F_analise($itemsUltimosMeses, $meses),
      $th
    );
  }
  private function F_analiseAno($options, $itemsAno){
    $resultado = [
      'RECEITA' => [
        'FIGP' => [], 
        'FICT' => [], 
        'FIGP_GRAFICO' => ['labels' => [], 'valores' => [] ], 
        'FICT_GRAFICO' => ['labels' => [], 'valores' => [] ],
      ],
      'DESPESA' => [ 
        'FIGP' => [], 
        'FICT' => [], 
        'FIGP_GRAFICO' => ['labels' => [], 'valores' => [] ], 
        'FICT_GRAFICO' => ['labels' => [], 'valores' => [] ],
      ],
    ];
    
    if(count($itemsAno) == 0) return $resultado;

    // --

    $meses = [];
    $ano = substr($options['data'], 0, 4);
    $dateInit = "{$ano}-01-01";
    for ($i = 0; $i < 12; $i++) { 
      // atualiza para +1 mes apos a primeira interação
      if($i > 0) $dateInit = date("d-m-Y", strtotime($dateInit. "+1 month"));

      // cria array retorno
      $key_mes = date('m_Y', strtotime($dateInit));
      $meses[$key_mes] = 0;
    }

    return $this->F_analise($itemsAno, $meses);
  }

  // aux
  private function F_renderItem($TIPO, $ID, $DESCRICAO, $resultado) {
    if(!isset($resultado[$TIPO][$ID]) )
      $resultado[$TIPO][$ID] = (object)[
        'DESCRICAO'  => $DESCRICAO,
        'TOTAL'      => 0,
        'PERCENTUAL' => 0,
        'PAGO'       => 0,
        'PENDENTE'   => 0,
        'TALVEZ'     => 0,
      ];
      
    return $resultado;
  }
  private function F_somaValores($tipo, $item, $resultado, $arrTotal) {
    $key_ID         = $item["{$tipo}_ID"];
    // $key_DESCRICAO  = $item["{$tipo}_DESCRICAO"];
    $FNIT_VALOR     = $item["FNIT_VALOR"];
    $FNIS_ID        = $item["FNIS_ID"];
    // $FITP_DESCRICAO = strtoupper($item["FITP_DESCRICAO"]); // DESCRIÇÃO DO TIPO EM MAIUSCULO

    // ADICIONA VALORES
    if( $FNIS_ID == 1) $resultado[$tipo][$key_ID]->PAGO     += number_format($FNIT_VALOR, 2,'.', '');
    if( $FNIS_ID == 2) $resultado[$tipo][$key_ID]->PENDENTE += number_format($FNIT_VALOR, 2,'.', '');
    if( $FNIS_ID == 3) $resultado[$tipo][$key_ID]->TALVEZ   += number_format($FNIT_VALOR, 2,'.', '');

    // IDENTIFICA VALORES
    $FICT_PAGO     = $resultado[$tipo][$key_ID]->PAGO;
    $FICT_PENDENTE = $resultado[$tipo][$key_ID]->PENDENTE;
    $FICT_TALVEZ   = $resultado[$tipo][$key_ID]->TALVEZ;

    // TOTAL
    $soma = ($FICT_PAGO + $FICT_PENDENTE + $FICT_TALVEZ);
    $resultado[$tipo][$key_ID]->TOTAL = number_format($soma, 2, '.', '');

    // PERCENTUAL
    $FICT_TOTAL      = $resultado[$tipo][$key_ID]->TOTAL;
    $FICT_PERCENTUAL = ( $FICT_TOTAL / $arrTotal) * 100;
    $FICT_PERCENTUAL = number_format( $FICT_PERCENTUAL , 2 , '.', '');

    $resultado[$tipo][$key_ID]->PERCENTUAL = "{$FICT_PERCENTUAL}%";

    return $resultado;
  }
  private function F_analise($items, $meses) {
    $arrRetorno =[];

    foreach ($items as $key => $item) {
      $item = (object)$item;
      
      $FITP_DESCRICAO = strtoupper($item->FITP_DESCRICAO);
      $key_mes = date('m_Y', strtotime($item->FNIT_DATA));

      foreach (['FIGP','FICT'] as $alias) {
        $ALIAS      = $alias;
        $ALIAS_DESC = "{$alias}_DESCRICAO";
        
        if( !isset($arrRetorno[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC]) )
          $arrRetorno[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC] = $meses;

          // SOMA
          $soma = $arrRetorno[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC][$key_mes] + $item->FNIT_VALOR;
          $arrRetorno[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC][$key_mes] = number_format($soma, 2, '.', '');
      }
    }
    unset($items);

    // ORDENA POR NOME
    foreach ($arrRetorno as $key_TIPO => $TIPO) {
      foreach ($TIPO as $key_DESC => $items) {
        if(strlen($key_DESC) == 4){
          $column = array_keys($arrRetorno[$key_TIPO][$key_DESC]);
          array_multisort($column, $arrRetorno[$key_TIPO][$key_DESC], SORT_ASC);
        }
      }
    }

    // FORMATA RETORNO
    foreach ($arrRetorno as $key_TIPO => $TIPO) {

      foreach ($TIPO as $key_DESC => $items) {
        if( strlen($key_DESC) == 4) {

          foreach ($items as $key_ITEM => $item) {
            // valores por dos meses
            $valores = array_values($item);

            // concatena NOME, TOTAL, VALORES
            $tmp = array_merge(
              [
                $key_ITEM,
                number_format(array_sum($valores), 2, '.', '')
              ],
              $valores
            );

            unset($arrRetorno[$key_TIPO][$key_DESC][$key_ITEM]);
            $arrRetorno[$key_TIPO][$key_DESC][] = $tmp;
          }

          // remove KEY do array
          $tmp = array_values($arrRetorno[$key_TIPO][$key_DESC]);
          $arrRetorno[$key_TIPO][$key_DESC] = $tmp;

          // alimenta key grafico com valores formatado
          foreach ($arrRetorno[$key_TIPO][$key_DESC] as $value) {
            $arrRetorno[$key_TIPO]["{$key_DESC}_GRAFICO"]['labels'][]  = $value[0];
            $arrRetorno[$key_TIPO]["{$key_DESC}_GRAFICO"]['valores'][] = $value[1];
          }
        }
      }
    }
    
    $arrRetorno['th'] = $meses;
    return $arrRetorno;
  }


  // --


  private function getLista() {

    $options['usuario'] = $_GET['usuario'];

    // carteira
    if(isset($_GET['FINC_ID']))     $options['FINC_ID']     = $_GET['FINC_ID'];
    if(isset($_GET['COCT_ID']))     $options['COCT_ID']     = $_GET['COCT_ID'];
    if(isset($_GET['INCT_ID']))     $options['INCT_ID']     = $_GET['INCT_ID'];
    // ID
    if(isset($_GET['INAV_ID']))     $options['INAV_ID']     = $_GET['INAV_ID'];
    if(isset($_GET['INOD_ID']))     $options['INOD_ID']     = $_GET['INOD_ID'];
    if(isset($_GET['FNIT_ID']))     $options['FNIT_ID']     = $_GET['FNIT_ID'];
    if(isset($_GET['FNLF_ID']))     $options['FNLF_ID']     = $_GET['FNLF_ID'];
    if(isset($_GET['INTP_ID']))     $options['INTP_ID']     = $_GET['INTP_ID'];
    // STATUS
    if(isset($_GET['INOD_STATUS'])) $options['INOD_STATUS'] = $_GET['INOD_STATUS'];
    if(isset($_GET['INAR_STATUS'])) $options['INAR_STATUS'] = $_GET['INAR_STATUS'];
    if(isset($_GET['INAS_STATUS'])) $options['INAS_STATUS'] = $_GET['INAS_STATUS'];
    if(isset($_GET['FNIT_STATUS'])) $options['FNIT_STATUS'] = $_GET['FNIT_STATUS'];
    // datas
    if(isset($_GET['data']))        $options['data']        = $_GET['data'];
    if(isset($_GET['dataAte']))     $options['dataAte']     = (!empty($_GET['dataAte'])) ? $_GET['dataAte'] : date('Y-m-d');
    if(isset($_GET['dataDe']))      $options['dataDe']      = (!empty($_GET['dataDe']))  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'].'-60 day'));
    // PARANS
    if(isset($_GET['INAS_TIPO']))   $options['INAS_TIPO']   = $_GET['INAS_TIPO'];
    if(isset($_GET['orderby']))     $options['orderby']     = $_GET['orderby'];
    if(isset($_GET['limit']))       $options['limit']       = (!empty($_GET['limit']))   ? $_GET['limit']   : 100;


    $modulos = $this->validaModules($_GET['retorno']);
    $arrRetorno = [];

    if($modulos['I_']){
      if(!isset($options['INCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o INCT_ID é uma parametro obrigatório']) );


      if(in_array('I_cotacao', $modulos['retorno'])) {
        $arrRetorno['I_cotacao'] = $this->I_cotacao($options);

        if(isset($options['limit']))   $arrRetorno['I_cotacao']['limit']   = $options['limit'];
        if(isset($options['dataAte'])) $arrRetorno['I_cotacao']['dataAte'] = $options['dataAte'];
        if(isset($options['dataDe']))  $arrRetorno['I_cotacao']['dataDe']  = $options['dataDe'];
      }
      if(in_array('I_cotacaoSugestao', $modulos['retorno'])) {
        if(!isset($options['dataAte'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o dataAte é uma parametro obrigatório']) );
        
        $arrRetorno['I_cotacaoSugestao'] = $this->I_cotacaoSugestao($options);
      }
      if(in_array('I_ativoSplit', $modulos['retorno'])) {
        $arrRetorno['I_ativoSplit'] = $this->I_ativoSplit($options);

        if(isset($options['limit']))   $arrRetorno['I_ativoSplit']['limit']   = $options['limit'];
        if(isset($options['dataAte'])) $arrRetorno['I_ativoSplit']['dataAte'] = $options['dataAte'];
        if(isset($options['dataDe']))  $arrRetorno['I_ativoSplit']['dataDe']  = $options['dataDe'];
      }
      if(in_array('I_rendimento', $modulos['retorno'])) {
        $arrRetorno['I_rendimento'] = $this->I_rendimento([
          'usuario'     => $options['usuario'],
          'INCT_ID'     => $options['INCT_ID'],
          'INAR_STATUS' => isset($options['INAR_STATUS']) ? $options['INAR_STATUS'] : false,
          'data'        => isset($options['data'])        ? $options['data']        : false,
          'dataDe'      => isset($options['dataDe'])      ? $options['dataDe']      : false,
          'dataAte'     => isset($options['dataAte'])     ? $options['dataAte']     : false,
          'orderby'     => $options['orderby'],
        ]);

        if(isset($options['limit']))   $arrRetorno['I_rendimento']['limit']   = $options['limit'];
        if(isset($options['dataAte'])) $arrRetorno['I_rendimento']['dataAte'] = $options['dataAte'];
        if(isset($options['dataDe']))  $arrRetorno['I_rendimento']['dataDe']  = $options['dataDe'];
      }
      if(in_array('I_rendimentoSugestao', $modulos['retorno'])) {
        if(!isset($options['dataAte'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o dataAte é uma parametro obrigatório']) );
        
        $arrRetorno['I_rendimentoSugestao'] = $this->I_rendimentoSugestao($options);
      }
      if(in_array('I_ordem', $modulos['retorno'])) {
        $arrRetorno['I_ordem'] = $this->I_ordem($options);

        if(isset($options['limit']))   $arrRetorno['I_ordem']['limit']   = $options['limit'];
        if(isset($options['dataAte'])) $arrRetorno['I_ordem']['dataAte'] = $options['dataAte'];
        if(isset($options['dataDe']))  $arrRetorno['I_ordem']['dataDe']  = $options['dataDe'];
      }
      if(in_array('I_itemsOrdem', $modulos['retorno'])) {
        $arrRetorno['I_itemsOrdem'] = $this->I_itemsOrdem($options);

        if(isset($options['limit']))   $arrRetorno['I_itemsOrdem']['limit']   = $options['limit'];
        if(isset($options['dataAte'])) $arrRetorno['I_itemsOrdem']['dataAte'] = $options['dataAte'];
        if(isset($options['dataDe']))  $arrRetorno['I_itemsOrdem']['dataDe']  = $options['dataDe'];
      }
      if(in_array('I_itemsOperacoes', $modulos['retorno'])) {
        $arrRetorno['I_itemsOperacoes'] = $this->I_itemsOperacoes($options);

        if(isset($options['limit']))   $arrRetorno['I_itemsOperacoes']['limit']   = $options['limit'];
        if(isset($options['dataAte'])) $arrRetorno['I_itemsOperacoes']['dataAte'] = $options['dataAte'];
        if(isset($options['dataDe']))  $arrRetorno['I_itemsOperacoes']['dataDe']  = $options['dataDe'];
      }
      if(in_array('F_sugestao', $modulos['retorno'])) {
        $arrRetorno['F_sugestao'] = $this->F_sugestao($options);
      }
    }
    if($modulos['C_']){
      if(!isset($options['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o COCT_ID é uma parametro obrigatório']) );

      if(in_array('C_itemsExtrato', $modulos['retorno'])) {
        $arrRetorno['C_itemsExtrato'] = $this->C_itemsExtrato($options);

        if(isset($options['limit']))   $arrRetorno['C_itemsExtrato']['limit']   = $options['limit'];
        if(isset($options['dataAte'])) $arrRetorno['C_itemsExtrato']['dataAte'] = $options['dataAte'];
        if(isset($options['dataDe']))  $arrRetorno['C_itemsExtrato']['dataDe']  = $options['dataDe'];
      }
    }
    if($modulos['F_']){
      if(!isset($options['FINC_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o FINC_ID é uma parametro obrigatório']) );

      if(in_array('F_itemsExtrato', $modulos['retorno'])) {
        $arrRetorno['F_itemsExtrato'] = $this->F_itemsExtrato($options);

        if(isset($options['limit']))   $arrRetorno['F_itemsExtrato']['limit']   = $options['limit'];
        if(isset($options['dataAte'])) $arrRetorno['F_itemsExtrato']['dataAte'] = $options['dataAte'];
        if(isset($options['dataDe']))  $arrRetorno['F_itemsExtrato']['dataDe']  = $options['dataDe'];
      }
      
      if(in_array('F_listaFixa', $modulos['retorno'])) {
        $arrRetorno['F_listaFixa'] = $this->F_listaFixa($options);

        if(isset($options['limit']))   $arrRetorno['F_listaFixa']['limit']   = $options['limit'];
        if(isset($options['dataAte'])) $arrRetorno['F_listaFixa']['dataAte'] = $options['dataAte'];
        if(isset($options['dataDe']))  $arrRetorno['F_listaFixa']['dataDe']  = $options['dataDe'];
      }
      
      if(in_array('F_sugestao', $modulos['retorno'])) {
        $arrRetorno['F_sugestao'] = $this->F_sugestao($options);
      }
      
      if(in_array('F_item', $modulos['retorno'])) {
        $arrRetorno['F_item'] = $this->F_item($options);
      }
    }

    return $arrRetorno;
  }
  private function F_itemsExtrato($options) {
    $items = $this->F_item->get($options);
    
    $arrRetorno['items'] = $items;
    $arrRetorno['itemsFiltro'] = $this->F_filtroByItems($items);
    return $arrRetorno;
  }
  private function F_listaFixa($options) {
    $items = $this->F_listaFixa->get($options);
    
    $arrRetorno['items'] = $items;
    $arrRetorno['itemsFiltro'] = $this->F_filtroByItems($items);
    return $arrRetorno;
  }
  private function F_sugestao($options){
    $items = $this->F_busca->sugestao($options);

    foreach ($items as $key => $value) {
      $value = (object)$value;

      $dia = date('d', strtotime($value->FNIT_DATA));
      $mes = date('m');
      $ano = date('Y', strtotime($value->FNIT_DATA));

      $items[$key] = (object)[
          "FNIT_ID"        => $value->FNIT_ID,
          "FNIT_VALOR"     => $value->FNIT_VALOR,
          "FNIT_DATA"      => "{$ano}-{$mes}-{$dia}",
          "FNIT_OBS"       => $value->FNIT_OBS,
          "FNIT_STATUS"    => $value->FNIT_STATUS,
          "FNIS_ID"        => $value->FNIS_ID,
          "FITP_ID"        => $value->FITP_ID,
          "FIGP_ID"        => $value->FIGP_ID,
          "FICT_ID"        => $value->FICT_ID,
          "FINC_ID"        => $value->FINC_ID,
          "USUA_ID"        => $value->USUA_ID,
          "FNIS_DESCRICAO" => $value->FNIS_DESCRICAO,
          "FITP_DESCRICAO" => $value->FITP_DESCRICAO,
          "FIGP_DESCRICAO" => $value->FIGP_DESCRICAO,
          "FICT_DESCRICAO" => $value->FICT_DESCRICAO,
          "FINC_DESCRICAO" => $value->FINC_DESCRICAO,
      ];
    }

    $itemsFiltro = $this->F_filtroByItems($items);

    $arrRetorno['items']       = $items;
    $arrRetorno['itemsFiltro'] = $itemsFiltro;
    return $arrRetorno;
  }
  private function F_item($options){
    if(!isset($options['FNIT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o FNIT_ID é uma parametro obrigatório']) );

    return $this->F_item->get($options);
  }
  private function I_cotacao($options) { // ok
    $items = $this->I_ativoCotacao->get($options);

    $arrRetorno['items'] = $items;
    return $arrRetorno;
  }
  private function I_cotacaoSugestao($options){
    $items = $this->I_item->get([
      'usuario' => $options['usuario'],
      'INCT_ID' => $options['INCT_ID'],
      'dataAte' => $options['dataAte'],
    ]);
    
    $INAV_IDs = array_unique( array_map(function($item){ return $item->INAV_ID; }, $items) );
    $INCT_IDs = array_unique( array_map(function($item){ return $item->INCT_ID; }, $items) );
    
    $arrCotacao    = $this->I_analiseCotacao($options, $INAV_IDs);
    $arrSplit      = $this->I_analiseSplitInplit($options, $INAV_IDs);
    $arrRendimento = $this->I_analiseRendimento($options, $INAV_IDs, $INCT_IDs);
    $itemsBase     = $this->I_analiseItensBase($items, $arrCotacao, $arrSplit, $arrRendimento, $options['dataAte']);

    $itemsBase     = array_filter($itemsBase, function($item){ return $item->COTAS > 0; });

    foreach($itemsBase as $key => $item){
      $itemsBase[$key] = (object)[
        'INCT_ID'           => $item->INCT_ID,           // "INCT_ID": 1
        'INCT_DESCRICAO'    => $item->INCT_DESCRICAO,    // "INCT_DESCRICAO": "Carteira Teste 1"
        
        'INCR_ID'           => $item->INCR_ID,           // "INCR_ID": 1
        'INCR_DESCRICAO'    => $item->INCR_DESCRICAO,    // "INCR_DESCRICAO": "RICO"

        'INTP_ID'           => $item->INTP_ID,           // "INTP_ID": 2
        'INTP_DESCRICAO'    => $item->INTP_DESCRICAO,    // "INTP_DESCRICAO": "Renda Variavel"
        
        'INAT_ID'           => $item->INAT_ID,           // "INAT_ID": 5
        'INAT_DESCRICAO'    => $item->INAT_DESCRICAO,    // "INAT_DESCRICAO": "Ações"
        
        'INAV_ID'           => $item->INAV_ID,           // "INAV_ID": 27
        'INAV_CODIGO'       => $item->INAV_CODIGO,       // "INAV_CODIGO": "BBAS3"
        'INAV_DESCRICAO'    => $item->INAV_DESCRICAO,    // "INAV_DESCRICAO": "BRASIL ONNM"

        'INAC_VALOR'        => $item->PRECO_COTACAO,     // "PRECO_COTACAO": "35.90"
        'INAC_STATUS'       => 1,
        'INAC_DATA'         => date('Y-m-d')
      ];
    }
    $itemsBase = array_values($itemsBase);


    return $itemsBase;
  }
  private function I_ativoSplit($options) { // ok
    $items = $this->I_ativoSplit->get($options);
    $itemsFiltro = $this->I_filtroByAtivoSplit($items);

    $arrRetorno['items']       = $items;
    $arrRetorno['itemsFiltro'] = $itemsFiltro;
    return $arrRetorno;
  }
  private function I_rendimento($options) { // ok
    $items = $this->I_ativoRendimento->get($options);
    $itemsFiltro = $this->I_filtroByRendimento($items);

    $response['items']       = $items;
    $response['itemsFiltro'] = $itemsFiltro;
    return $response;
  }
  private function I_rendimentoSugestao($options){
    $items = $this->I_item->get([
      'usuario' => $options['usuario'],
      'dataAte' => $options['dataAte'],
      'INCT_ID' => $options['INCT_ID'],
      'INTP_ID' => $options['INTP_ID'],
    ]);
      
    $itemsBase = $this->I_analiseItensBase($items, [], [], [], $options['dataAte']);

    $itemsComCotas = array_filter($itemsBase, function($value) { return $value->COTAS > 0; });

    $items = $this->I_ativoRendimento->get([
      'usuario'     => $options['usuario'],
      'INCT_ID'     => $options['INCT_ID'],
      'INTP_ID'     => $options['INTP_ID'],
      'data'        => date('Y-m', strtotime("{$options['dataAte']}-01" .'-1 month' )),
      'orderby'     => isset($options['orderby']) ? $options['orderby'] : false,
      'INAV_ID'     => array_unique( array_map(function($item){ return $item->INAV_ID; }, $itemsComCotas) ),
    ]);

    $items = array_map(function($value){
      $ano = date('Y', strtotime($value->INAR_DATA));
      $mes = date('m');
      $dia = date('d', strtotime($value->INAR_DATA));
      $value->INAR_DATA = "{$ano}-{$mes}-{$dia}";
      return $value;
    }, $items);

    return $items;
  }
  private function I_ordem($options) { // ok
    $items = $this->I_ordem->get($options); 
    $itemsFiltro = $this->I_filtroByOrdem($items);

    $response['items']       = $items;
    $response['itemsFiltro'] = $itemsFiltro;
    return $response;
  }
  private function I_itemsOrdem($options) { // ok
    $items = $this->I_item->get($options);
    $itemsFiltro = $this->I_filtroByOperacoes($items);

    $response['items']       = $items;
    $response['itemsFiltro'] = $itemsFiltro;
    return $response;
  }
  private function I_itemsOperacoes($options) {
    $items = array_values($this->I_item->get($options));

    $itemsFiltro = $this->I_filtroByOperacoes($items); // monta ItemFiltro

    $response['items']       = $items;
    $response['itemsFiltro'] = $itemsFiltro;
    return $response;
  }
  private function C_itemsExtrato($options) { // ---
    $items = $this->C_item->get($options);

    $response['items']       = $items;
    // $response['itemsFiltro'] = $itemsFiltro;
    return $response;
  }

  // AUX
  private function validaIdGet($strID) {
    if(!isset($_GET[$strID]))
      die(
        json_encode([
          'STATUS' => 'error', 
          'msg' => "o '{$strID}' é uma parametro obrigatório"])
      );
  }
  private function validaModules($getRetorno) {
    $retorno = array_filter(explode('|', $getRetorno), function($r) {return $r; });

    $C_ = false;
    $F_ = false;
    $I_ = false;

    foreach($retorno as $value) {
      $str = substr($value,0,2);
      if($str == 'C_') $C_ = true;
      if($str == 'F_') $F_ = true;
      if($str == 'I_') $I_ = true;
    }

    if($C_){
      $this->C_item = new C_ItemModel;
      $this->C_tipo = new C_TipoModel;
    }
    if($F_){
      $this->F_busca     = new F_BuscaModel;
      $this->F_item      = new F_ItemModel;
      $this->F_item      = new F_ItemModel;
      $this->F_listaFixa = new F_financaListaFixa;
    }
    if($I_){
      $this->I_carteiraItem    = new I_CarteiraItem;
      $this->I_busca           = new I_BuscaModel;
      $this->I_item            = new I_ItemModel;
      $this->I_ativoRendimento = new I_AtivoRendimento;
      $this->I_ativoCotacao    = new I_AtivoCotacao;
      $this->I_ativoSplit      = new I_AtivoSplit;
      $this->I_ordem           = new I_Ordem;
    }

    
    return [
      'retorno' => $retorno,
      'C_' => $C_,
      'F_' => $F_,
      'I_' => $I_,
    ];
  }
  private function I_analiseCotacao($options, $INAV_IDs) {
    $items = $this->I_ativoCotacao->get([
      'usuario'     => $options['usuario'],
      'INAV_ID'     => $INAV_IDs,
      'INAC_STATUS' => 1,
      'orderby'     => 'INAC_DATA:ASC',
    ]);
    
    foreach($items as $key => $item) {
      $aliasID   = "INAV_{$item->INAV_ID}";
      $aliasDATA = date('Y-m', strtotime($item->INAC_DATA));

      $items[$aliasID][$aliasDATA][] = $item;
      
      unset($aliasID);
      unset($aliasDATA);
      unset($items[$key]);
    }
    return $items;
  }
  private function I_analiseSplitInplit($options, $INAV_IDs) {
    $items = $this->I_ativoSplit->get([
      'usuario'     => $options['usuario'],
      'INAV_ID'     => $INAV_IDs,
      'INAS_STATUS' => 1,
      'orderby'     => 'INAS_DATA:ASC',
    ]);

    foreach($items as $key_item => $item) {
      $aliasID   = "INAV_{$item->INAV_ID}";
      $aliasDATA = date('Y-m', strtotime($item->INAS_DATA));

      $items[$aliasID][$aliasDATA][] = $item;

      unset($aliasID);
      unset($aliasDATA);
      unset($items[$key_item]);
    }
    
    return $items;
  }
  private function I_analiseRendimento($options, $INAV_IDs, $INCT_IDs) {

    $items = $this->I_ativoRendimento->get([
      'usuario' => $options['usuario'],
      'INAV_ID' => $INAV_IDs,
      'INCT_ID' => $INCT_IDs,
      'dataAte' => $options['dataAte'],
    ]);

    foreach($items as $key_item => $item) {
      $aliasID   = "INAV_{$item->INAV_ID}";
      $aliasDATA = date('Y-m', strtotime($item->INAR_DATA));

      $items[$aliasID][$aliasDATA][] = $item;
      
      unset($aliasID);
      unset($aliasDATA);
      unset($items[$key_item]);
    }
    
    return $items;
  }
  private function I_analiseItensBase($items){

    foreach ($items as $key_item => $item) {
      
      $items[$key_item] = (object)array_merge(
        [
          'INCTC_ID'       => $item->INCTC_ID,
          'INCTC_DATA'     => $item->INCTC_DATA,
          'INAV_ID'        => $item->INAV_ID,
          'INAV_DESCRICAO' => $item->INAV_DESCRICAO,
          'INAV_STATUS'    => $item->INAV_STATUS,
          'INAV_CODIGO'    => $item->INAV_CODIGO,
          'INAV_CPNJ'      => $item->INAV_CPNJ,
          'INAV_SITE'      => $item->INAV_SITE,
          'INAV_LIQUIDEZ'  => $item->INAV_LIQUIDEZ,
          'INAV_VENC'      => $item->INAV_VENC,
          'INAT_ID'        => $item->INAT_ID,
          'INAT_DESCRICAO' => $item->INAT_DESCRICAO,
          'INAT_STATUS'    => $item->INAT_STATUS,
          'INTP_ID'        => $item->INTP_ID,
          'INTP_DESCRICAO' => $item->INTP_DESCRICAO,
          'INTP_STATUS'    => $item->INTP_STATUS,
          'INCR_ID'        => $item->INCR_ID,
          'INCR_DESCRICAO' => $item->INCR_DESCRICAO,
          'INCR_STATUS'    => $item->INCR_STATUS,
          'INCT_ID'        => $item->INCT_ID,
          'INCT_DESCRICAO' => $item->INCT_DESCRICAO,
          'INCT_STATUS'    => $item->INCT_STATUS,
          'INCT_PAINEL'    => $item->INCT_PAINEL,
          'USUA_ID'        => $item->USUA_ID,
          'USUA_NOME'      => $item->USUA_NOME,
        ],
        (array)json_decode($item->INCTC_CONTENT)
      );
    }

    return array_values($items);
  }
  private function I_apuraArrayItemsByKey($items, $alias) {
    
    foreach ($items as $item_key => $item) {
      $DESCRICAO = ($alias == 'INAV') ?  "{$alias}_CODIGO" : "{$alias}_DESCRICAO";
      $ID        = "{$alias}_ID";
      $tmp_key   = $item->$ID;
      
      if(!isset($items["aux_{$tmp_key}"])){
        $items["aux_{$tmp_key}"] = (object)[
          // "USUA_ID"          => $item->USUA_ID,
          // "USUA_NOME"        => $item->USUA_NOME,

          // "INCT_ID"          => $item->INCT_ID,
          // "INCT_DESCRICAO"   => $item->INCT_DESCRICAO,
          // "INCT_STATUS"      => $item->INCT_STATUS,
          
          "INCR_ID"          => $item->INCR_ID,
          "INCR_DESCRICAO"   => $item->INCR_DESCRICAO,
          // "INCR_STATUS"      => $item->INCR_STATUS,
          // "CORRETORA"        => $item->INCR_DESCRICAO,
          
          "$ID"              => $item->$ID,
          "$DESCRICAO"       => $item->$DESCRICAO,
          "DESCRICAO"        => $item->$DESCRICAO,
          // "INAV_LIQUIDEZ"    => $item->INAV_LIQUIDEZ,
          // "INAV_VENC"        => $item->INAV_VENC,
          // "INAV_STATUS"      => $item->INAV_STATUS,

          "INTP_ID"          => $item->INTP_ID,
          "INTP_DESCRICAO"   => $item->INTP_DESCRICAO,
          // "INTP_STATUS"      => $item->INTP_STATUS,
          
          "INAT_ID"          => $item->INAT_ID,
          "INAT_DESCRICAO"   => $item->INAT_DESCRICAO,
          // "INAT_STATUS"      => $item->INAT_STATUS,

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

      $items["aux_{$tmp_key}"]->COTAS_COMPRA     = number_format($items["aux_{$tmp_key}"]->COTAS_COMPRA     + $item->COTAS_COMPRA, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_COMPRA     = number_format($items["aux_{$tmp_key}"]->TOTAL_COMPRA     + $item->TOTAL_COMPRA, 2, '.', '');
      $items["aux_{$tmp_key}"]->COTAS_VENDA      = number_format($items["aux_{$tmp_key}"]->COTAS_VENDA      + $item->COTAS_VENDA, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_VENDA      = number_format($items["aux_{$tmp_key}"]->TOTAL_VENDA      + $item->TOTAL_VENDA, 2, '.', '');
      $items["aux_{$tmp_key}"]->COTAS            = number_format($items["aux_{$tmp_key}"]->COTAS            + $item->COTAS, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL            = number_format($items["aux_{$tmp_key}"]->TOTAL            + $item->TOTAL, 2, '.', '');
      $items["aux_{$tmp_key}"]->BRUTO            = number_format($items["aux_{$tmp_key}"]->BRUTO            + $item->BRUTO, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_RENDIMENTO = number_format($items["aux_{$tmp_key}"]->TOTAL_RENDIMENTO + $item->TOTAL_RENDIMENTO, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_DIVIDENDO  = number_format($items["aux_{$tmp_key}"]->TOTAL_DIVIDENDO  + $item->TOTAL_DIVIDENDO, 2, '.', '');
      $items["aux_{$tmp_key}"]->TOTAL_JSCP       = number_format($items["aux_{$tmp_key}"]->TOTAL_JSCP       + $item->TOTAL_JSCP, 2, '.', '');
      $items["aux_{$tmp_key}"]->MES_RENDIMENTO   = number_format($items["aux_{$tmp_key}"]->MES_RENDIMENTO   + $item->MES_RENDIMENTO, 2, '.', '');
      $items["aux_{$tmp_key}"]->MES_DIVIDENDO    = number_format($items["aux_{$tmp_key}"]->MES_DIVIDENDO    + $item->MES_DIVIDENDO, 2, '.', '');
      $items["aux_{$tmp_key}"]->MES_JSCP         = number_format($items["aux_{$tmp_key}"]->MES_JSCP         + $item->MES_JSCP, 2, '.', '');
      
      unset($items[$item_key]);
    }

    foreach ($items as $item_key => $item) {
      // APURA PREÇO MÉDIO
      if( $items[$item_key]->COTAS > 0){
        $items[$item_key]->PRECO_MEDIO = number_format($items[$item_key]->TOTAL / $items[$item_key]->COTAS, 2, '.', '');
      } else {
        $items[$item_key]->PRECO_MEDIO = 0;
      }
    }

    return $items;
  }

  // FILTRO 
  private function F_filtroByItems($items) {
    $arrFiltro = (object)[
      'FITP' => [],
      'FIGP' => [],
      'FICT' => [],
      'FNIS' => [],
    ];

    foreach($items as $item){
      if( !key_exists($item->FITP_ID, $arrFiltro->FITP) ){
        $arrFiltro->FITP[$item->FITP_ID] = (object)[
          'FITP_ID'        => $item->FITP_ID,
          'FITP_DESCRICAO' => $item->FITP_DESCRICAO,
        ];
      }
      if( !key_exists($item->FIGP_ID, $arrFiltro->FIGP) ){
        $arrFiltro->FIGP[$item->FIGP_ID] = (object)[
          'FIGP_ID'        => $item->FIGP_ID,
          'FIGP_DESCRICAO' => $item->FIGP_DESCRICAO,
        ];
      }
      if( !key_exists($item->FICT_ID, $arrFiltro->FICT) ){
        $arrFiltro->FICT[$item->FICT_ID] = (object)[
          'FICT_ID'        => $item->FICT_ID,
          'FICT_DESCRICAO' => $item->FICT_DESCRICAO,
        ];
      }
      if( !key_exists($item->FNIS_ID, $arrFiltro->FNIS) ){
        $arrFiltro->FNIS[$item->FNIS_ID] = (object)[
          'FNIS_ID'        => $item->FNIS_ID,
          'FNIS_DESCRICAO' => $item->FNIS_DESCRICAO,
        ];
      }
    }

    $arrFiltro->FITP = $this->orderArrayFiltro([$arrFiltro->FITP],'FITP_DESCRICAO')[0];
    $arrFiltro->FIGP = $this->orderArrayFiltro([$arrFiltro->FIGP],'FIGP_DESCRICAO')[0];
    $arrFiltro->FICT = $this->orderArrayFiltro([$arrFiltro->FICT],'FICT_DESCRICAO')[0];
    $arrFiltro->FNIS = $this->orderArrayFiltro([$arrFiltro->FNIS],'FNIS_DESCRICAO')[0];

    return $arrFiltro;
  }
  private function I_filtroByItems($items) {
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
  private function I_filtroByOperacoes($items) {
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
  private function I_filtroByOrdem($items) {

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
  private function I_filtroByAtivoSplit($items) {
    
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
  private function I_filtroByRendimento($items) {
    
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
        $val = (array)$val;
        return $val["$alias"]; 
        }, array_values($arrFiltro[$key])
      );

      array_multisort($column, $arrFiltro[$key], SORT_ASC);

      $arrFiltro[$key] = array_values($arrFiltro[$key]);
    }

    return $arrFiltro;
  }
  

  // private function F_mesGeralSomaTipo($resultado, $arrTotal, $item, $key) {
  //   $key_ID         = $item["{$key}_ID"];
  //   $key_DESCRICAO  = $item["{$key}_DESCRICAO"];
  //   $FNIT_VALOR     = $item["FNIT_VALOR"];
  //   $FNIS_ID        = $item["FNIS_ID"];
  //   $FITP_DESCRICAO = strtoupper($item["FITP_DESCRICAO"]); // DESCRIÇÃO DO TIPO EM MAIUSCULO
    

  //   // ADICIONA VALORES
  //   if( $FNIS_ID == 1) $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PAGO     += number_format($FNIT_VALOR, 2,'.', '');
  //   if( $FNIS_ID == 2) $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PENDENTE += number_format($FNIT_VALOR, 2,'.', '');
  //   if( $FNIS_ID == 3) $resultado[$FITP_DESCRICAO]->$key[$key_ID]->TALVEZ   += number_format($FNIT_VALOR, 2,'.', '');

  //   // ADICIONA TOTAL
  //   $FICT_PAGO     = $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PAGO;
  //   $FICT_PENDENTE = $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PENDENTE;
  //   $FICT_TALVEZ   = $resultado[$FITP_DESCRICAO]->$key[$key_ID]->TALVEZ;

  //   $soma = ($FICT_PAGO + $FICT_PENDENTE + $FICT_TALVEZ);
  //   $resultado[$FITP_DESCRICAO]->$key[$key_ID]->TOTAL = number_format($soma, 2, '.', '');

  //   // ADICIONA PERCENTUAL
  //   $FICT_TOTAL      = $resultado[$FITP_DESCRICAO]->$key[$key_ID]->TOTAL;
  //   $FICT_PERCENTUAL = ( $FICT_TOTAL / $arrTotal[$FITP_DESCRICAO]) * 100;
  //   $FICT_PERCENTUAL = number_format( $FICT_PERCENTUAL , 2 , '.', '');

  //   $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PERCENTUAL = "{$FICT_PERCENTUAL}%";

  //   return $resultado;
  // }
  

  
  private function I_carteiraAnaliseTipoAtivo($itemsMes) {
    $tmpRetorno = [
      'items'         => [],
      // 'items_GRAFICO' => [],
    ];

    if(count($itemsMes) == 0) return $tmpRetorno;
      
    // --

    $itemsMes = $this->I_apuraArrayItemsByKey($itemsMes, 'INAT');

    // // VALORES PARA GRAFICO
    // $itemsMesGrafico = [];
    // foreach ($itemsMes as $key => $item) {
    //   $itemsMesGrafico[$item->DESCRICAO]['label'][]   = $item->DESCRICAO;
    //   $itemsMesGrafico[$item->DESCRICAO]['valores'][] = $item->BRUTO;
    //   $itemsMesGrafico[$item->DESCRICAO]['valores'][] = $item->TOTAL;
    // }

    $tmpRetorno['items']         = array_values($itemsMes);
    // $tmpRetorno['items_GRAFICO'] = array_values($itemsMesGrafico);

    return $tmpRetorno;
  }


  // private function I_carteiraAnaliseAtivo($items) {
  //   $tmpRetorno = [
  //     'items'         => [],
  //     // 'items_GRAFICO' => [],
  //   ];

  //   if(count($items) == 0) return $tmpRetorno;
      
  //   // --

  //   $items = $this->I_apuraArrayItemsByKey($items, 'INAV');

  //   // // VALORES PARA GRAFICO
  //   // $itemsGrafico = [];
  //   // foreach ($items as $key => $item) {
  //   //   $itemsGrafico[$item->DESCRICAO]['label'][]   = $item->DESCRICAO;
  //   //   $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->BRUTO;
  //   //   $itemsGrafico[$item->DESCRICAO]['valores'][] = $item->TOTAL;
  //   // }

  //   $tmpRetorno['items']         = array_values($items);
  //   // $tmpRetorno['items_GRAFICO'] = array_values($itemsGrafico);

  //   return $tmpRetorno;
  // }
  
}