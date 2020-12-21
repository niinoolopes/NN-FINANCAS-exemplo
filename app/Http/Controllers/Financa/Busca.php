<?php

namespace App\Http\Controllers\Financa;

use App\Http\Controllers\Controller;
use App\Model\Financa\Busca     as F_BuscaModel;
use App\Model\Financa\Item      as F_ItemModel;
use App\Model\Financa\Tipo      as F_TipoModel;
use App\Model\Financa\Situacao  as F_SituacaoModel;
use App\Model\Financa\ListaFixa as F_financaListaFixa;

class Busca extends Controller
{
  private $busca;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->busca     = new F_BuscaModel;
    $this->item      = new F_ItemModel;
    $this->tipo      = new F_TipoModel;
    $this->situacao  = new F_SituacaoModel;
    $this->listaFixa = new F_financaListaFixa;
  }


  public function get($tipo) {

    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

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
    
    try{
      $STATUS = 'success';
      $result = $this->$method();
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => "Erro ao executar Controller/Financa/Busca/{$tipoOriginal}"
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }


  // --

  // public function getSugestao() {
  //   if( !isset($_GET['FINC_ID'])) return response()->json(['STATUS' => 'error', 'msg' => 'o FINC_ID é obrigatório']);

  //   $items = $this->busca->sugestao($_GET);

  //   // --

  //   foreach ($items as $key => $value) {
  //     $value = (object)$value;

  //     $dia = date('d', strtotime($value->FNIT_DATA));
  //     $mes = date('m');
  //     $ano = date('Y', strtotime($value->FNIT_DATA));

  //     $items[$key] = (object)[
  //         "FNIT_ID"        => $value->FNIT_ID,
  //         "FNIT_VALOR"     => $value->FNIT_VALOR,
  //         "FNIT_DATA"      => "{$ano}-{$mes}-{$dia}",
  //         "FNIT_OBS"       => $value->FNIT_OBS,
  //         "FNIT_STATUS"    => $value->FNIT_STATUS,
  //         "FNIS_ID"        => $value->FNIS_ID,
  //         "FITP_ID"        => $value->FITP_ID,
  //         "FIGP_ID"        => $value->FIGP_ID,
  //         "FICT_ID"        => $value->FICT_ID,
  //         "FINC_ID"        => $value->FINC_ID,
  //         "USUA_ID"        => $value->USUA_ID,
  //         "FNIS_DESCRICAO" => $value->FNIS_DESCRICAO,
  //         "FITP_DESCRICAO" => $value->FITP_DESCRICAO,
  //         "FIGP_DESCRICAO" => $value->FIGP_DESCRICAO,
  //         "FICT_DESCRICAO" => $value->FICT_DESCRICAO,
  //         "FINC_DESCRICAO" => $value->FINC_DESCRICAO,
  //     ];
  //   }
  //   $itemsFiltro = $this->filtroByItems($items);

  //   return [
  //     'items' => $items,
  //     'itemsFiltro' => $itemsFiltro,
  //   ];
  // }


  // --


  // public function getItem() {
  //   if( !isset($_GET['FINC_ID'])) return response()->json(['STATUS' => 'error', 'msg' => 'o FINC_ID é obrigatório']);
  //   if( !isset($_GET['FNIT_ID'])) return response()->json(['STATUS' => 'error', 'msg' => 'o FNIT_ID é obrigatório']);

  //   return $this->item->get($_GET);
  // }

 

  // feito
  public function getMesGeral() {
    if( !isset($_GET['FINC_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o FINC_ID é obrigatório']) );
    if( !isset($_GET['mes']))     die( json_encode(['STATUS' => 'error', 'msg' => 'o mes é obrigatório']) );

    $_GET['status'] = 1;
    $_GET['mes']    = date('Y-m', strtotime($_GET['mes']));
    $items = $this->busca->mesGeral($_GET);

    // --

    $arrTotal = [];
    // $resultado = [];
    $resultado['RECEITA'] = (object)[ 
      'FIGP' => [], 
      'FICT' => [], 
      'FIGP_GRAFICO' => [ 'label' => [], 'valores' => [] ], 
      'FICT_GRAFICO' => [ 'label' => [], 'valores' => [] ] 
    ];
    $resultado['DESPESA'] = (object)[ 
      'FIGP' => [], 
      'FICT' => [], 
      'FIGP_GRAFICO' => [ 'label' => [], 'valores' => [] ], 
      'FICT_GRAFICO' => [ 'label' => [], 'valores' => [] ] 
    ];
    
    if( count($items) == 0) return (object)$resultado;


    foreach($items as $item) {
      $item = (object)$item;
      
      $FNIT_VALOR     = $item->FNIT_VALOR;
      $FITP_DESCRICAO = strtoupper($item->FITP_DESCRICAO); // DESCRIÇÃO DO TIPO EM MAIUSCULO
      $FIGP_ID        = $item->FIGP_ID;
      $FIGP_DESCRICAO = $item->FIGP_DESCRICAO;
      $FICT_ID        = $item->FICT_ID;
      $FICT_DESCRICAO = $item->FICT_DESCRICAO;

      // ADICIONA ARRAY DE TOTAL
      if( !isset( $arrTotal[$FITP_DESCRICAO] ) ) {
        $arrTotal[$FITP_DESCRICAO] = 0;
      }

      // ADICIONA ARRAY DE GRUPO
      if( !key_exists($FIGP_ID, $resultado[$FITP_DESCRICAO]->FIGP) ) {

        $resultado[$FITP_DESCRICAO]->FIGP[$FIGP_ID] = (object)[
          'DESCRICAO'  => $FIGP_DESCRICAO,
          'TOTAL'      => 0,
          'PERCENTUAL' => 0,
          'PAGO'       => 0,
          'PENDENTE'   => 0,
          'TALVEZ'     => 0,
        ];
      }

      // ADICIONA ARRAY DE CATEGORIAS
      if( !key_exists($FICT_ID, $resultado[$FITP_DESCRICAO]->FICT) ) {

        $resultado[$FITP_DESCRICAO]->FICT[$FICT_ID] = (object)[
          'DESCRICAO'  => $FICT_DESCRICAO,
          'TOTAL'      => 0,
          'PERCENTUAL' => 0,
          'PAGO'       => 0,
          'PENDENTE'   => 0,
          'TALVEZ'     => 0,
        ];
      }
      
      // SOMA VALORES TOTAIS 
      $arrTotal[$FITP_DESCRICAO] += number_format($FNIT_VALOR, 2,'.', '');
    }

    // apura items criando consolidado por GRUPO e CATEGORIA
    foreach($items as $item) {
      $item = (array)$item;

      $resultado = $this->getMesGeral_value($resultado, $arrTotal, $item, 'FIGP'); 
      $resultado = $this->getMesGeral_value($resultado, $arrTotal, $item, 'FICT'); 
    }
    
    // CONVERT 'Object of Objects' TO 'ARRAY of Objects'
    foreach ($resultado as $key_tipo => $tipo) {

      foreach ($tipo as $key_items => $items) {
        if(strlen($key_items) == 4){

          // IDENTIFICA ARRAY
          $arr = $resultado[$key_tipo]->$key_items;
          $arr = array_values($arr);
          
          // ORDENAÇÃO POR DESCRICAO
          $columns = array_map(function($val) { return $val->DESCRICAO; }, $arr);
          array_multisort($columns, $arr);

          // CONVERT
          $resultado[$key_tipo]->$key_items = $arr;
  
          // --
  
          // IDENTIFICA LABEL PARA GRAFICO
          $label = array_map(function($val){ return $val->DESCRICAO; }, $arr);
          $label = array_values($label);
          
          // IDENTIFICA VALORES PARA GRAFICO
          $valores = array_map(function($val){ return $val->PAGO; }, $arr);
          $valores = array_values($valores);
  
          // ADICIONA LABEL E VALORES
          $key_grafico = "{$key_items}_GRAFICO";
          $resultado[$key_tipo]->$key_grafico = (object)[ 
              'label'   => $label, 
              'valores' => $valores
            ];
        }
      }
    }

    return (object)$resultado;
  }
  
  
  private function getMesGeral_value($resultado, $arrTotal, $item, $key) {
    $key_ID         = $item["{$key}_ID"];
    $key_DESCRICAO  = $item["{$key}_DESCRICAO"];
    $FNIT_VALOR     = $item["FNIT_VALOR"];
    $FNIS_ID        = $item["FNIS_ID"];
    $FITP_DESCRICAO = strtoupper($item["FITP_DESCRICAO"]); // DESCRIÇÃO DO TIPO EM MAIUSCULO
    

    // ADICIONA VALORES
    if( $FNIS_ID == 1) $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PAGO     += number_format($FNIT_VALOR, 2,'.', '');
    if( $FNIS_ID == 2) $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PENDENTE += number_format($FNIT_VALOR, 2,'.', '');
    if( $FNIS_ID == 3) $resultado[$FITP_DESCRICAO]->$key[$key_ID]->TALVEZ   += number_format($FNIT_VALOR, 2,'.', '');

    // ADICIONA TOTAL
    $FICT_PAGO     = $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PAGO;
    $FICT_PENDENTE = $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PENDENTE;
    $FICT_TALVEZ   = $resultado[$FITP_DESCRICAO]->$key[$key_ID]->TALVEZ;

    $soma = ($FICT_PAGO + $FICT_PENDENTE + $FICT_TALVEZ);
    $resultado[$FITP_DESCRICAO]->$key[$key_ID]->TOTAL = number_format($soma, 2, '.', '');

    // ADICIONA PERCENTUAL
    $FICT_TOTAL      = $resultado[$FITP_DESCRICAO]->$key[$key_ID]->TOTAL;
    $FICT_PERCENTUAL = ( $FICT_TOTAL / $arrTotal[$FITP_DESCRICAO]) * 100;
    $FICT_PERCENTUAL = number_format( $FICT_PERCENTUAL , 2 , '.', '');

    $resultado[$FITP_DESCRICAO]->$key[$key_ID]->PERCENTUAL = "{$FICT_PERCENTUAL}%";

    return $resultado;
  } 

  // feito
  // public function getMesExtrato() {
  //   $_GET['status'] = 1;

  //   $items = $this->busca->mesExtrato($_GET);

  //   $itemsFiltro = $this->filtroByItems($items);

  //   return [
  //     'items' => $items,
  //     'itemsFiltro' => $itemsFiltro,
  //   ];
  // }


  // feito
  public function getMesConsolidadoValores() {
    if( !isset($_GET['FINC_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o FINC_ID é obrigatório']) );
    if( !isset($_GET['mes']))     die( json_encode(['STATUS' => 'error', 'msg' => 'o mes é obrigatório']) );

    $items = $this->busca->mesConsolidadoValores($_GET);

    // --

    $somaItemsPagos = [
      'RECEITA'  => 0,
      'DESPESA'  => 0,
      'SOBRA'    => 0,
      'ESTIMADO' => 0,
    ];

    $somaItems = [
      'RECEITA'  => 0,
      'DESPESA'  => 0,
    ];

    foreach ($items as $item) {

      // soma valores PAGOS, consolidar sobra
      if( $item->FNIS_ID == 1 ){
        $somaItemsPagos['RECEITA'] += ( $item->FITP_ID == 1 ) ? $item->FNIT_VALOR : 0; // SOMA DESPESA
        $somaItemsPagos['DESPESA'] += ( $item->FITP_ID == 2 ) ? $item->FNIT_VALOR : 0; // SOMA DESPESA
      }
      
      // soma todos os valores, consolidar estimado
      $somaItems['RECEITA'] += ( $item->FITP_ID == 1 ) ? $item->FNIT_VALOR : 0; // SOMA DESPESA
      $somaItems['DESPESA'] += ( $item->FITP_ID == 2 ) ? $item->FNIT_VALOR : 0; // SOMA DESPESA
    }

    return (object) [
      'RECEITA'  => floatval (number_format( $somaItemsPagos['RECEITA'], 2 , '.' ,'')),
      'DESPESA'  => floatval (number_format( $somaItemsPagos['DESPESA'], 2 , '.' ,'')),
      'SOBRA'    => floatval (number_format( $somaItemsPagos['RECEITA'] - $somaItemsPagos['DESPESA'], 2 , '.' ,'')),
      'ESTIMADO' => floatval (number_format( $somaItems['RECEITA'] - $somaItems['DESPESA'], 2 , '.' ,'')),
    ];
  }

  // feito
  public function getAnoConsolidadoValores() {
    if( !isset($_GET['FINC_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o FINC_ID é obrigatório']) );
    if( !isset($_GET['mes']))     die( json_encode(['STATUS' => 'error', 'msg' => 'o mes é obrigatório']) );

    $_GET['ano'] = substr($_GET['mes'], 0, 4);
    
    $items = $this->busca->anoConsolidadoValores($_GET);
    
    // --

    $somaItemsPagos = [];
    $somaItems      = [];

    for ($i=1; $i<=12; $i++) { 
      $alias = "Mes_" . $i;

      $somaItemsPagos[$alias] = (object)[
        'RECEITA'  => 0,
        'DESPESA'  => 0,
        'SOBRA'    => 0,
        'ESTIMADO' => 0,
      ];
      $somaItems[$alias]      = (object)[
        'RECEITA'  => 0,
        'DESPESA'  => 0,
        'SOBRA'    => 0,
        'ESTIMADO' => 0,
      ];
    }


    foreach ($items as $item) {
      $alias = "Mes_" . date('n', strtotime($item->FNIT_DATA));

      // soma valores PAGOS, consolidar sobra
      if( $item->FNIS_ID == 1 ){
        $somaItemsPagos[$alias]->RECEITA += ( $item->FITP_ID == 1 ) ? $item->FNIT_VALOR : 0; // SOMA DESPESA
        $somaItemsPagos[$alias]->DESPESA += ( $item->FITP_ID == 2 ) ? $item->FNIT_VALOR : 0; // SOMA DESPESA
      }
      
      // soma todos os valores, consolidar estimado
      $somaItems[$alias]->RECEITA += ( $item->FITP_ID == 1 ) ? $item->FNIT_VALOR : 0; // SOMA DESPESA
      $somaItems[$alias]->DESPESA += ( $item->FITP_ID == 2 ) ? $item->FNIT_VALOR : 0; // SOMA DESPESA
    }
    unset($items);

  
    foreach ($somaItemsPagos as $key => $mes) {
      $somaItemsPagos[$key]->SOBRA    = $mes->RECEITA - $mes->DESPESA;
      $somaItemsPagos[$key]->ESTIMADO = $somaItems[$key]->RECEITA - $somaItems[$key]->DESPESA;
    }
    unset($somaItems);

    $arrRECEITA  = array_values( array_map(function($mes){ return number_format($mes->RECEITA, 2, '.', '');  }, $somaItemsPagos) );
    $arrDESPESA  = array_values( array_map(function($mes){ return number_format($mes->DESPESA, 2, '.', '');  }, $somaItemsPagos) );
    $arrSOBRA    = array_values( array_map(function($mes){ return number_format($mes->SOBRA, 2, '.', '');    }, $somaItemsPagos) );
    $arrESTIMADO = array_values( array_map(function($mes){ return number_format($mes->ESTIMADO, 2, '.', ''); }, $somaItemsPagos) );


    $arrResult[] = array_merge( ['RECEITA'],   $arrRECEITA );
    $arrResult[] = array_merge( ['DESPESA'],   $arrDESPESA );
    $arrResult[] = array_merge( ['SOBRA'],       $arrSOBRA );
    $arrResult[] = array_merge( ['ESTIMADO'], $arrESTIMADO );

    return $arrResult;
  }


  // feito
  // public function getInativo() {
  //   $_GET['status'] = 0;

  //   $items = $this->item->get($_GET);
    
  //   $itemsFiltro = $this->filtroByItems($items);

  //   return [
  //     'items' => $items,
  //     'itemsFiltro' => $itemsFiltro,
  //   ];
  // }

  // --

  public function getAnaliseGrupo() {
    
    $anoAte  = substr($_GET['mes'], 0 , 4);
    $anoDe   =  $anoAte - 4;
    
    $_GET['anoDe']  = "{$anoAte}";
    $_GET['anoAte'] = "{$anoAte}";

    $itemsAno = $this->busca->analiseGrupo($_GET);

    $_GET['anoDe']  = "{$anoDe}";
    $_GET['anoAte'] = "{$anoAte}";
    $itemsUltimosAnos = $this->busca->analiseGrupo($_GET);
    
    // --
    
    $temsAno = $this->getAnaliseGrupo_itemsAno($itemsAno);

    $tableAno = $this->getAnaliseGrupo_tableAno($itemsAno);

    $graficoAno = $this->getAnaliseGrupo_graficoAno($tableAno);

    $tableUltimosAnos = $this->getAnaliseGrupo_tableUltimosAnos($anoAte, $anoDe, $itemsUltimosAnos);

    return [
      'itemsAno'         => $temsAno,
      'tableAno'         => $tableAno,
      'graficoAno'       => $graficoAno,
      'tableUltimosAnos' => $tableUltimosAnos,
    ];
  }

  private function getAnaliseGrupo_itemsAno($itemsAno) {
    $listaItems = [];
    for ($i=1; $i <= 12 ; $i++) { 
      $data   = "01-$i-2020";
      $mes    = ucfirst( strftime('%B', strtotime($data)) );
      $mesNum = intval( date( 'm', strtotime($data)) );
      $listaItems[$mesNum] = (object)[
        'MES'   => utf8_encode($mes),
        'LISTA' => [],
      ];
    }
    foreach ($itemsAno as $item) {
      $item = (object)$item; 
      $mesNum = intval( date( 'm', strtotime($item->FNIT_DATA)) );
      $listaItems[$mesNum]->LISTA[] = $item;
    }

    return array_values($listaItems);
  }
  private function getAnaliseGrupo_tableAno($itemsAno) {
    $analiseAno = [];
    
    // - MONTA ARRAY DE 12 MESES
    for ($i=1; $i <= 12 ; $i++) { 
      $data   = "01-$i-2020";
      $mes    = ucfirst( strftime('%B', strtotime($data)) );
      $mesNum = date( 'm', strtotime($data));

      $analiseAno[$mesNum] = (object)[ 'MES' => utf8_encode($mes), 'VALOR' => 0 ];
    }

    // - ATRIBUI VALORES POR MES
    foreach ($itemsAno as $item) {
        $item   = (object)$item; 
        $mesNum = date( 'm', strtotime($item->FNIT_DATA));

        $analiseAno[$mesNum]->VALOR += number_format($item->FNIT_VALOR, 2, '.', '');
    }

    return array_values($analiseAno);
  }

  private function getAnaliseGrupo_graficoAno($tableAno) {
    $grafico_ano = (object)[
      'label' => [],
      'valores' => [],
    ];

    // - MONTA A ARRAY PARA GRAFICO
    foreach ($tableAno as $mes) {
        $grafico_ano->label[]   = $mes->MES;
        $grafico_ano->valores[] = $mes->VALOR;
    }

    return $grafico_ano;
  }

  private function getAnaliseGrupo_tableUltimosAnos($anoAte, $anoDe, $itemsUltimosAnos) {
    $meses = [];
    $analiseUltimosAnos = [];

    // MONTA ARRAY MESES
    for ($i=1; $i <= 12 ; $i++) { 
        $data   = "01-$i-2020";
        $mesNum = intval( date( 'm', strtotime($data)) );
        $meses[$mesNum] = 0;
    }

    ksort($meses);

    // MONTA ARRAY DE RETORNO
    for ( $ano = $anoAte ; $ano >= $anoDe ; $ano-- ) {
        $analiseUltimosAnos[$ano] = (object)[
            'ANO'   => $ano,
            'TOTAL' => 0,
            'MESES' => $meses
        ];
    }

    // ATRIBUI VALOR 
    foreach ($itemsUltimosAnos as $item) {
      $item   = (object)$item; 
      $mesNum = intval( date( 'm', strtotime($item->FNIT_DATA)) );
      $anoNum = date( 'Y', strtotime($item->FNIT_DATA));

      // SOMA VALOR TOTAL
      $soma = $analiseUltimosAnos[$anoNum]->TOTAL + $item->FNIT_VALOR;
      $analiseUltimosAnos[$anoNum]->TOTAL = number_format( $soma, 2, '.', '');

      // SOMA VALOR DO MES
      $soma = $analiseUltimosAnos[$anoNum]->MESES[$mesNum] + $item->FNIT_VALOR;
      $analiseUltimosAnos[$anoNum]->MESES[$mesNum] = number_format( $soma, 2, '.', '');
    }


    // FORMATA RETORNO
    // [ ANO, TOTAL, 'VALORES DOS 12 MESES' ]
    foreach ($analiseUltimosAnos as $key => $item) {
        $item = (object)$item; 

        $tmp = [
          $item->ANO,
          $item->TOTAL,
        ];

        $tmp = array_merge( $tmp, $item->MESES );

        $analiseUltimosAnos[$key] = $tmp;
    }
    return  array_values($analiseUltimosAnos);
  }


  // --

  public function getAnaliseUltimosMeses() {

    $arrDados['usuario'] = $_GET['usuario'];
    $arrDados['FINC_ID'] = $_GET['FINC_ID'];
    $arrDados['FITP_ID'] = isset($_GET['FITP_ID']) ? $_GET['FITP_ID'] : '';
    $arrDados['anoAte']  = $_GET['mes'];
    $arrDados['anoDe']   = date('Y-m', strtotime('-12 month', strtotime($arrDados['anoAte'])) );
    $arrDados['status']  = 1;

    // -- 

    $resultado = [
      'RECEITA' => [ 
        'FIGP' => [], 
        'FICT' => [],
        'FIGP_GRAFICO' => (object)['labels' => [], 'valores' => [] ], 
        'FICT_GRAFICO' => (object)['labels' => [], 'valores' => [] ],
      ],
      'DESPESA' => [ 
        'FIGP' => [], 
        'FICT' => [],
        'FIGP_GRAFICO' => (object)['labels' => [], 'valores' => [] ], 
        'FICT_GRAFICO' => (object)['labels' => [], 'valores' => [] ],
      ],
      'th' => [
        'Origem', 'Total'
      ]
    ];
    $meses = [];

    // --

    // DATAS INICIAIS
    $dateInit = date('d-m-Y', strtotime($_GET['mes'] . '-01'));
    for ($i = 0; $i < 12; $i++) { 
      // atualiza para -1 mes apos a primeira interação
      if($i > 0) $dateInit = date("d-m-Y", strtotime($dateInit. "-1 month"));

      // adicione em um array os meses para usar na tabela
      $resultado['th'][] = date("m-Y", strtotime($dateInit));

      // cria array retorno
      $key_mes = date('m_Y', strtotime($dateInit));
      $meses[$key_mes] = 0;
    }

    // CONSULTA PARA MODELAR
    $items = $this->busca->analiseUltimosMeses($arrDados);

    foreach ($items as $item) {
      $item = (object)$item;
      
      $FITP_DESCRICAO = strtoupper($item->FITP_DESCRICAO);
      $key_mes = date('m_Y', strtotime($item->FNIT_DATA));

      foreach (['FIGP','FICT'] as $alias) {

        $ALIAS      = $alias;
        $ALIAS_DESC = "{$alias}_DESCRICAO";
        
        if( !isset($resultado[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC]) )
          $resultado[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC] = $meses;

          $soma = $resultado[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC][$key_mes] + $item->FNIT_VALOR;
          // SOMA
          $resultado[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC][$key_mes] = number_format($soma, 2, '.', '');
      }
    }
    unset($items);


    // ORDENA POR NOME
    foreach ($resultado as $key_TIPO => $TIPO) {
      foreach ($TIPO as $key_DESC => $items) {
        if(strlen($key_DESC) == 4){
          $column = array_keys($resultado[$key_TIPO][$key_DESC]);
          array_multisort($column, $resultado[$key_TIPO][$key_DESC], SORT_ASC);
        }
      }
    }


    // FORMATA RETORNO
    foreach ($resultado as $key_TIPO => $TIPO) {
      if( $key_TIPO != 'th' ) {

        foreach ($TIPO as $key_DESC => $items) {
          if(strlen($key_DESC) == 4){
            
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

              unset($resultado[$key_TIPO][$key_DESC][$key_ITEM]);
              $resultado[$key_TIPO][$key_DESC][] = $tmp;

            }

            foreach ($resultado[$key_TIPO][$key_DESC] as $value) {
              $resultado[$key_TIPO]["{$key_DESC}_GRAFICO"]->labels[]  = $value[0];
              $resultado[$key_TIPO]["{$key_DESC}_GRAFICO"]->valores[] = $value[1];
            }
          }
        }
      }
    }

    return $resultado;
  }

  // --

  public function getAnaliseAno() {
    $arrDados['usuario'] = $_GET['usuario'];
    $arrDados['FINC_ID'] = $_GET['FINC_ID'];
    $arrDados['FITP_ID'] = isset($_GET['FITP_ID']) ? $_GET['FITP_ID'] : '';
    $arrDados['ano']     = substr($_GET['mes'],0,4);
    $arrDados['status']  = 1;

    // -- 

    $resultado = [
      'RECEITA' => [
        'FIGP' => [], 
        'FICT' => [], 
        'FIGP_GRAFICO' => (object)['labels' => [], 'valores' => [] ], 
        'FICT_GRAFICO' => (object)['labels' => [], 'valores' => [] ],
      ],
      'DESPESA' => [ 
        'FIGP' => [], 
        'FICT' => [], 
        'FIGP_GRAFICO' => (object)['labels' => [], 'valores' => [] ], 
        'FICT_GRAFICO' => (object)['labels' => [], 'valores' => [] ],
      ],
    ];
    $meses = [];

    // --

    // DATAS INICIAIS
    $dateInit = $arrDados['ano'] . '-01-01';

    for ($i = 0; $i < 12; $i++) { 
      // atualiza para +1 mes apos a primeira interação
      if($i > 0) $dateInit = date("d-m-Y", strtotime($dateInit. "+1 month"));

      // cria array retorno
      $key_mes = date('m_Y', strtotime($dateInit));
      $meses[$key_mes] = 0;
    }

    // CONSULTA PARA MODELAR
    $items = $this->busca->analiseAno($arrDados);


    foreach ($items as $key => $item) {
      $item = (object)$item;
      
      $FITP_DESCRICAO = strtoupper($item->FITP_DESCRICAO);
      $key_mes = date('m_Y', strtotime($item->FNIT_DATA));

      foreach (['FIGP','FICT'] as $alias) {

        $ALIAS      = $alias;
        $ALIAS_DESC = "{$alias}_DESCRICAO";
        
        if( !isset($resultado[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC]) )
          $resultado[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC] = $meses;

          $soma = $resultado[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC][$key_mes] + $item->FNIT_VALOR;
          // SOMA
          $resultado[$FITP_DESCRICAO][$ALIAS][$item->$ALIAS_DESC][$key_mes] = number_format($soma, 2, '.', '');
      }
    }
    unset($items);

    
    // ORDENA POR NOME
    foreach ($resultado as $key_TIPO => $TIPO) {
      foreach ($TIPO as $key_DESC => $items) {
        if(strlen($key_DESC) == 4){
          $column = array_keys($resultado[$key_TIPO][$key_DESC]);
          array_multisort($column, $resultado[$key_TIPO][$key_DESC], SORT_ASC);
        }
      }
    }

    // FORMATA RETORNO
    foreach ($resultado as $key_TIPO => $TIPO) {

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

            unset($resultado[$key_TIPO][$key_DESC][$key_ITEM]);
            $resultado[$key_TIPO][$key_DESC][] = $tmp;
          }

          // alimenta key grafico com valores formatado
          foreach ($resultado[$key_TIPO][$key_DESC] as $value) {
            $resultado[$key_TIPO]["{$key_DESC}_GRAFICO"]->labels[]  = $value[0];
            $resultado[$key_TIPO]["{$key_DESC}_GRAFICO"]->valores[] = $value[1];
          }
        }
      }
    }

    return $resultado;
  }

  // -- 

  public function getAnaliseAnoConsolidado() {
    
    $arrDados['usuario'] = $_GET['usuario'];
    $arrDados['FINC_ID'] = $_GET['FINC_ID'];
    $arrDados['ano']     = substr($_GET['mes'],0,4);
    $arrDados['status']  = 1;

    // -- 

    $meses = [];
    for ($i = 1; $i <= 12; $i ++) { 
      $key = strlen($i) == 1 ? "0{$i}" : "{$i}";
      $meses[$key] = number_format(0 , 2, '.', '');
    }
    
    $resultadoPago = (object)[
      'RECEITA'  => $meses,
      'DESPESA'  => $meses,
      'SOBRA'    => $meses,
      'ESTIMADO' => $meses,
    ];
    
    $resultadoEstimado = (object)[
      'RECEITA'  => $meses,
      'DESPESA'  => $meses,
      'ESTIMADO' => $meses,
    ];

    // --

    $items = $this->busca->analiseAnoConsolidado($arrDados);
    // ADICIONA VALOR EM RECEITA E DESPESA
    foreach ($items as $item) {
      $SOMA    = $item->FNIT_SOMA;
      $MES     = $item->FNIT_MES;
      $FITP_ID = $item->FITP_ID;

      if($item->FNIS_ID == 1) {
        if( $FITP_ID == 1 ) $resultadoPago->RECEITA[$MES] = $SOMA;
        if( $FITP_ID == 2 ) $resultadoPago->DESPESA[$MES] = $SOMA;
      } 
      if( $FITP_ID == 1 ) $resultadoEstimado->RECEITA[$MES] = $SOMA;
      if( $FITP_ID == 2 ) $resultadoEstimado->DESPESA[$MES] = $SOMA;
    }

    // ADICIONA VALOR EM SOBRA
    for ($i = 1; $i <= 12; $i ++) { 
      $key_mes = strlen($i) == 1 ? "0{$i}" : "{$i}";

      $RECEITA = $resultadoPago->RECEITA[$key_mes];
      $DESPESA = $resultadoPago->DESPESA[$key_mes];
      $resultadoPago->SOBRA[$key_mes] = number_format($RECEITA - $DESPESA, 2, '.', '');
      
      $RECEITA = $resultadoEstimado->RECEITA[$key_mes];
      $DESPESA = $resultadoEstimado->DESPESA[$key_mes];
      $resultadoEstimado->ESTIMADO[$key_mes] = number_format($RECEITA - $DESPESA, 2, '.', '');
    }

    $resultadoPago->RECEITA  = array_values($resultadoPago->RECEITA);
    $resultadoPago->DESPESA  = array_values($resultadoPago->DESPESA);
    $resultadoPago->SOBRA    = array_values($resultadoPago->SOBRA);
    $resultadoPago->ESTIMADO = array_values($resultadoEstimado->ESTIMADO);
    
    return $resultadoPago;
  }

  // -- 

  // public function getListaFixa() {

  //   $options['usuario'] = $_GET['usuario'];
  //   $options['FINC_ID'] = $_GET['FINC_ID'];
  //   if(isset($options['orderby'])) $options['orderby'] = $_GET['orderby'];

  //   $items = $this->listaFixa->get($options);

  //   $itemsFiltro = $this->filtroByItems($items);
    
  //   // --

  //   return [
  //     'items'       => $items,
  //     'itemsFiltro' => $itemsFiltro,
  //   ];
  // }

  // --

  // public function getHistorico() {
  //   $_GET['limit']   = isset($_GET['limit'])   ? $_GET['limit']   : 30;
  //   $_GET['dataAte'] = isset($_GET['dataAte']) ? $_GET['dataAte'] : date('Y-m-d');
  //   $_GET['dataDe']  = isset($_GET['dataDe'])  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'] . '-90 day' ));

  //   // --

  //   $items = $this->busca->historico($_GET);
    
  //   $itemsFiltro = $this->filtroByItems($items);

  //   // --

  //   return [
  //     'items'       => $items,
  //     'itemsFiltro' => $itemsFiltro,
  //     'dataDe'      => $_GET['dataDe'],
  //     'dataAte'     => $_GET['dataAte'],
  //     'limit'       => $_GET['limit'],
  //   ];
  // }
  
  // // --

  // public function getMovimentacao() {
  //   if( !isset($_GET['FINC_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o FINC_ID é obrigatório']) );

  //   $_GET['limit']   = isset($_GET['limit'])   ? $_GET['limit']   : 30;
  //   $_GET['dataAte'] = isset($_GET['dataAte']) ? $_GET['dataAte'] : date('Y-m-d');
  //   $_GET['dataDe']  = isset($_GET['dataDe'])  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'] . '-30 day' ));

  //   $items = $this->busca->movimentacao($_GET);
    
  //   $itemsFiltro = $this->filtroByItems($items);

  //   return [
  //     'items' => $items,
  //     'itemsFiltro' => $itemsFiltro,
  //     'dataDe' => $_GET['dataDe'],
  //     'dataAte' => $_GET['dataAte'],
  //   ];
  // }

  // -- metodos aux

  private function filtroByItems($items) {
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

    $column = array_map(function($val){ return $val->FITP_DESCRICAO; }, array_values($arrFiltro->FITP));
    array_multisort($column, $arrFiltro->FITP, SORT_ASC);

    $column = array_map(function($val){ return $val->FIGP_DESCRICAO; }, array_values($arrFiltro->FIGP));
    array_multisort($column, $arrFiltro->FIGP, SORT_ASC);

    $column = array_map(function($val){ return $val->FICT_DESCRICAO; }, array_values($arrFiltro->FICT));
    array_multisort($column, $arrFiltro->FICT, SORT_ASC);

    $column = array_map(function($val){ return $val->FNIS_DESCRICAO; }, array_values($arrFiltro->FNIS));
    array_multisort($column, $arrFiltro->FNIS, SORT_ASC);


    $arrFiltro->FITP = array_values($arrFiltro->FITP);
    $arrFiltro->FIGP = array_values($arrFiltro->FIGP);
    $arrFiltro->FICT = array_values($arrFiltro->FICT);
    $arrFiltro->FNIS = array_values($arrFiltro->FNIS);

    return $arrFiltro;
  }
  
}