<?php

namespace App\Rule;

use App\Model\Investimento\Item         as I_ItemModel;
use App\Model\Investimento\AtivoCotacao as I_AtivoCotacao;
use App\Model\Investimento\AtivoSplit   as I_AtivoSplit;
use App\Model\Investimento\AtivoRendimento as I_AtivoRendimento;
use App\Model\Investimento\CarteiraItem as I_CarteiraItem;

class Investimento_CarteiraItem {

  public $options = [];

  public function exec(){
    // $mesHoje = date('Y-m') . '-01';
    
    $this->USUA_ID = $this->options['USUA_ID'];
    $this->INCT_ID = $this->options['INCT_ID'];
    $this->INAV_ID = $this->options['INAV_ID'];
    $this->INTP_ID = $this->options['INTP_ID'];
    $this->dataDe  = $this->options['dataDe'];
    $this->dataAte = $this->options['dataAte'];

    $this->dataDe  = $this->formatDate($this->dataDe, 'fisrt');
    $this->dataAte = $this->formatDate($this->dataAte, 'last');
    $mesHoje       = $this->formatDate(date('Y-m-d'), 'last');
    // --

    $this->carteiraItem = new I_CarteiraItem;


    $this->arrCotacao    = [];
    $this->arrSplit      = [];
    $this->arrRendimento = [];
    $this->itemsOrdem    = [];
    $this->itemsDataInicial = [];
    $this->carteiraConsolidado = [];

    
    $this->getCotacao();
    $this->getSplitInplit();
    $this->getRendimento();
    $this->getItems();
    $this->getItemsDataInicial();
    $this->getConsolidadoCarteiraItem();


    // LOOP para cada INAV_ID e sua data inicial
    $arrConsolidado = [];
    $arrConsolidadoControll = [];
    foreach($this->itemsDataInicial as $value) {

      $INAV_ID   = $value->INAV_ID;
      $INCR_ID   = $value->INCR_ID;
      $INOD_DATA = $value->INOD_DATA;
      $mes       = date('Y-m', strtotime($INOD_DATA)) . '-01';

      $INCT_INCR_INAV = "{$this->INCT_ID}_{$INCR_ID}_{$INAV_ID}";

      // LOOP daraInicial até a dataHoje
      while( strtotime($mes) <= strtotime(($mesHoje)) ){

        $arrSplit       = $this->searchSplit($INAV_ID, $mes);
        $arrCotacao     = $this->searchCotacao($INAV_ID, $mes);
        $arrRendimento  = $this->searchRendimento($INAV_ID, $mes);
        $arrItemsOrdem  = $this->searchItems($INAV_ID, $INCR_ID, $mes) ;


        // LOOP consolidado de cada mes do INAV_ID
        foreach($arrItemsOrdem as $key_item => $item){

          // OBJETO DADOS
          if(!isset($arrConsolidado[$INCT_INCR_INAV])){
            $arrConsolidado[$INCT_INCR_INAV] = [
              // "USUA_ID"          => $item->USUA_ID,
              // "USUA_NOME"        => $item->USUA_NOME,

              "INCT_ID"          => $item->INCT_ID,
              // "INCT_DESCRICAO"   => $item->INCT_DESCRICAO,
              // "INCT_STATUS"      => $item->INCT_STATUS,

              "INCR_ID"          => $item->INCR_ID,
              // "INCR_DESCRICAO"   => $item->INCR_DESCRICAO,
              // "INCR_STATUS"      => $item->INCR_STATUS,

              "INAV_ID"          => $item->INAV_ID,
              // "INAV_CODIGO"      => $item->INAV_CODIGO,
              // "INAV_DESCRICAO"   => $item->INAV_DESCRICAO,
              // "INAV_CPNJ"        => $item->INAV_CPNJ,
              // "INAV_SITE"        => $item->INAV_SITE,
              // "INAV_LIQUIDEZ"    => $item->INAV_LIQUIDEZ,
              // "INAV_VENC"        => $item->INAV_VENC,
              // "INAV_STATUS"      => $item->INAV_STATUS,

              // "INTP_ID"          => $item->INTP_ID,
              // "INTP_DESCRICAO"   => $item->INTP_DESCRICAO,
              // "INTP_STATUS"      => $item->INTP_STATUS,

              // "INAT_ID"          => $item->INAT_ID,
              // "INAT_DESCRICAO"   => $item->INAT_DESCRICAO,
              // "INAT_STATUS"      => $item->INAT_STATUS,
              
              "MES_CONSOLIDADO"   => '',

              "COTAS"             => 0,
              "COTAS_COMPRA"      => 0,
              "COTAS_VENDA"       => 0,
              "H_COTAS_COMPRA"    => 0,
              "H_COTAS_VENDA"     => 0,

              "TOTAL"             => 0,
              "TOTAL_COMPRA"      => 0,
              "TOTAL_VENDA"       => 0,
              "H_TOTAL_COMPRA"    => 0,
              "H_TOTAL_VENDA"     => 0,

              "BRUTO"             => 0,
              
              "VALORIZACAO_UNIDADE" => 0, // uso em RENDA VARIAVEL
              "VALORIZACAO_TOTAL"   => 0, // uso em RENDA VARIAVEL

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
          }

          // APLICA SPLIT/INPLIT
          if(count($arrSplit)) {
            foreach ($arrSplit as $key_INAS => $INAS) {
              /* REGRA DE USO
              * INAS_TIPO == 'S' MULTIPLICA
              * INAS_TIPO == 'I' DIVIDE
              */
              if($arrConsolidado[$INCT_INCR_INAV]['COTAS_COMPRA'] > 0) {
                if($INAS->INAS_TIPO == 'S') $arrConsolidado[$INCT_INCR_INAV]['COTAS_COMPRA'] *= ($INAS->INAS_QUANTIDADE + 1);
                if($INAS->INAS_TIPO == 'I') $arrConsolidado[$INCT_INCR_INAV]['COTAS_COMPRA'] /= ($INAS->INAS_QUANTIDADE + 1);
              }
              if($arrConsolidado[$INCT_INCR_INAV]['COTAS_VENDA'] > 0) {
                if($INAS->INAS_TIPO == 'S') $arrConsolidado[$INCT_INCR_INAV]['COTAS_VENDA'] *= ($INAS->INAS_QUANTIDADE + 1);
                if($INAS->INAS_TIPO == 'I') $arrConsolidado[$INCT_INCR_INAV]['COTAS_VENDA'] /= ($INAS->INAS_QUANTIDADE + 1);
              }
              unset($arrSplit[$key_INAS]);
            }
          }

          // BUSCA RENDIMENTO PARA O ATIVO/MES
          $TOTAL_RENDIMENTO  = 0;
          $TOTAL_DIVIDENDO   = 0;
          $TOTAL_JSCP        = 0;
          $MES_RENDIMENTO    = 0;
          $MES_DIVIDENDO     = 0;
          $MES_JSCP          = 0;

          // ADICIONA RENDIMENTOS
          foreach ($arrRendimento as $key_rendimento => $rendimento) {
            $mesIgual = $mes == date('Y-m', strtotime($rendimento->INAR_DATA));
            // RENDA FIXA
            if($item->INTP_ID == 1 ){ // RENDIMENTO
              $TOTAL_RENDIMENTO += $rendimento->INAR_VALOR;              // soma rendimentos totais acumulados
              if($mesIgual) $MES_RENDIMENTO += $rendimento->INAR_VALOR;  // soma rendimentos do mes atual
            } 
            // RENDA VARIAVEL
            else if ($item->INTP_ID == 2){       
              if($rendimento->INAR_TIPO === 'D'){ // DIVIDENDOS
                $TOTAL_DIVIDENDO += $rendimento->INAR_VALOR;              // soma DIVIDENDOS totais acumulados
                if($mesIgual) $MES_DIVIDENDO += $rendimento->INAR_VALOR;  // soma DIVIDENDOS do mes atual
              }
              if($rendimento->INAR_TIPO === 'J'){  // JSCP
                $TOTAL_JSCP += $rendimento->INAR_VALOR;              // soma JSCP totais acumulados
                if($mesIgual) $MES_JSCP += $rendimento->INAR_VALOR;  // soma JSCP do mes atual
              }
            }
            unset($arrRendimento["INAV_{$item->INAV_ID}"][$key_rendimento]);
          }
          
          $arrConsolidado[$INCT_INCR_INAV]['TOTAL_RENDIMENTO'] = number_format($arrConsolidado[$INCT_INCR_INAV]['TOTAL_RENDIMENTO'] + $TOTAL_RENDIMENTO, 2, '.', '');
          $arrConsolidado[$INCT_INCR_INAV]['TOTAL_DIVIDENDO']  = number_format($arrConsolidado[$INCT_INCR_INAV]['TOTAL_DIVIDENDO']  + $TOTAL_DIVIDENDO, 2, '.', '');
          $arrConsolidado[$INCT_INCR_INAV]['TOTAL_JSCP']       = number_format($arrConsolidado[$INCT_INCR_INAV]['TOTAL_JSCP']       + $TOTAL_JSCP, 2, '.', '');
          $arrConsolidado[$INCT_INCR_INAV]['MES_RENDIMENTO']   = number_format($arrConsolidado[$INCT_INCR_INAV]['MES_RENDIMENTO']   + $MES_RENDIMENTO, 2, '.', '');
          $arrConsolidado[$INCT_INCR_INAV]['MES_DIVIDENDO']    = number_format($arrConsolidado[$INCT_INCR_INAV]['MES_DIVIDENDO']    + $MES_DIVIDENDO, 2, '.', '');
          $arrConsolidado[$INCT_INCR_INAV]['MES_JSCP']         = number_format($arrConsolidado[$INCT_INCR_INAV]['MES_JSCP']         + $MES_JSCP, 2, '.', '');
          // BUSCA RENDIMENTO PARA O ATIVO/MES - FIM
          

          // ATRUBI VALORES DE COMPRA OU VENDA
          if($item->INIT_CV == 'C'){
            $arrConsolidado[$INCT_INCR_INAV]['H_TOTAL_COMPRA'] = number_format($arrConsolidado[$INCT_INCR_INAV]['H_TOTAL_COMPRA'] + $item->INIT_PRECO_TOTAL, 2, '.', '');
            $arrConsolidado[$INCT_INCR_INAV]['H_COTAS_COMPRA'] = number_format($arrConsolidado[$INCT_INCR_INAV]['H_COTAS_COMPRA'] + $item->INIT_COTAS, 2, '.', '');
            $arrConsolidado[$INCT_INCR_INAV]['TOTAL_COMPRA']   = number_format($arrConsolidado[$INCT_INCR_INAV]['TOTAL_COMPRA']   + $item->INIT_PRECO_TOTAL, 2, '.', '');
            $arrConsolidado[$INCT_INCR_INAV]['COTAS_COMPRA']   = number_format($arrConsolidado[$INCT_INCR_INAV]['COTAS_COMPRA']   + $item->INIT_COTAS, 2, '.', '');
          }
          if($item->INIT_CV == 'V'){
            $arrConsolidado[$INCT_INCR_INAV]['H_TOTAL_VENDA'] = number_format($arrConsolidado[$INCT_INCR_INAV]['H_TOTAL_VENDA'] + $item->INIT_PRECO_TOTAL, 2, '.', '');
            $arrConsolidado[$INCT_INCR_INAV]['H_COTAS_VENDA'] = number_format($arrConsolidado[$INCT_INCR_INAV]['H_COTAS_VENDA'] + $item->INIT_COTAS, 2, '.', '');
            $arrConsolidado[$INCT_INCR_INAV]['TOTAL_VENDA']   = number_format($arrConsolidado[$INCT_INCR_INAV]['TOTAL_VENDA']   + $item->INIT_PRECO_TOTAL, 2, '.', '');
            $arrConsolidado[$INCT_INCR_INAV]['COTAS_VENDA']   = number_format($arrConsolidado[$INCT_INCR_INAV]['COTAS_VENDA']   + $item->INIT_COTAS, 2, '.', '');
          }

          // COTAS
          $arrConsolidado[$INCT_INCR_INAV]['COTAS'] = number_format($arrConsolidado[$INCT_INCR_INAV]['COTAS_COMPRA'] - $arrConsolidado[$INCT_INCR_INAV]['COTAS_VENDA'], 2, '.', '');

          // TOTAL
          $arrConsolidado[$INCT_INCR_INAV]['TOTAL'] = number_format($arrConsolidado[$INCT_INCR_INAV]['TOTAL_COMPRA'] - $arrConsolidado[$INCT_INCR_INAV]['TOTAL_VENDA'], 2, '.', '');

          // PRECO_MEDIO
          if($arrConsolidado[$INCT_INCR_INAV]['COTAS'] > 0) 
            $arrConsolidado[$INCT_INCR_INAV]['PRECO_MEDIO'] = number_format($arrConsolidado[$INCT_INCR_INAV]['TOTAL'] / $arrConsolidado[$INCT_INCR_INAV]['COTAS'], 2, '.', '');
          

          // --


          // REGRA RENDA FIXA 
          if($item->INTP_ID == 1){
            $arrConsolidado[$INCT_INCR_INAV]['BRUTO'] = $item->TOTAL + $item->TOTAL_RENDIMENTO;
          }
          
          // REGRA RENDA VARIAVEL 
          if($item->INTP_ID == 2) {
            // COTAÇÃO
            if(count($arrCotacao)){
              $cotacaoMes = end($arrCotacao);
              $PRECO_COTACAO = $cotacaoMes->INAC_VALOR;

            } else {
              $PRECO_COTACAO = $arrConsolidado[$INCT_INCR_INAV]['PRECO_MEDIO'];
            }


            $arrConsolidado[$INCT_INCR_INAV]['PRECO_COTACAO'] = $PRECO_COTACAO;
            // PREÇO BRUTO, 'PREÇO DA COTA X QUANTIDADE'  
            $arrConsolidado[$INCT_INCR_INAV]['BRUTO']               = number_format($PRECO_COTACAO * $arrConsolidado[$INCT_INCR_INAV]['COTAS'] , 2, '.', '');
            // VALORIZACAO_UNIDADE
            $arrConsolidado[$INCT_INCR_INAV]['VALORIZACAO_UNIDADE'] = number_format($PRECO_COTACAO - $arrConsolidado[$INCT_INCR_INAV]['PRECO_MEDIO'] , 2, '.', '');
            // VALORIZACAO_TOTAL
            $arrConsolidado[$INCT_INCR_INAV]['VALORIZACAO_TOTAL']   = number_format(($PRECO_COTACAO - $arrConsolidado[$INCT_INCR_INAV]['PRECO_MEDIO']) * $arrConsolidado[$INCT_INCR_INAV]['COTAS'] , 2, '.', '');
            

            // QUANTO NÃO HOUVER COTAS, ZERA ALGUMAS VALORES
            if($arrConsolidado[$INCT_INCR_INAV]['COTAS'] <= 0 ) {
              

              $LUCRO_VENDA = number_format( $arrConsolidado[$INCT_INCR_INAV]['TOTAL_VENDA'] - $arrConsolidado[$INCT_INCR_INAV]['TOTAL_COMPRA'] , 2, '.', '');

              $arrConsolidado[$INCT_INCR_INAV]['LUCRO_VENDA']       = number_format($arrConsolidado[$INCT_INCR_INAV]['LUCRO_VENDA'] + $LUCRO_VENDA, 2, '.', '');

              $arrConsolidado[$INCT_INCR_INAV]['TOTAL']             = 0;
              $arrConsolidado[$INCT_INCR_INAV]['TOTAL_COMPRA']      = 0;
              $arrConsolidado[$INCT_INCR_INAV]['TOTAL_VENDA']       = 0;
        
              $arrConsolidado[$INCT_INCR_INAV]['COTAS']             = 0;
              $arrConsolidado[$INCT_INCR_INAV]['COTAS_COMPRA']      = 0;
              $arrConsolidado[$INCT_INCR_INAV]['COTAS_VENDA']       = 0;
        
              $arrConsolidado[$INCT_INCR_INAV]['PRECO_MEDIO']       = 0;
              $arrConsolidado[$INCT_INCR_INAV]['PRECO_COTACAO']     = 0;
        
              $arrConsolidado[$INCT_INCR_INAV]['VALORIZACAO_UNIDADE'] = 0;
              $arrConsolidado[$INCT_INCR_INAV]['VALORIZACAO_TOTAL']   = 0;

              // --

              $H_LUCRO_VENDA['INOD_DATA']   = $item->INOD_DATA;
              $H_LUCRO_VENDA['LUCRO_VENDA'] = $LUCRO_VENDA;
              $H_LUCRO_VENDA['IR']          = 0;  

              if( ($item->INAT_ID == 1 && $LUCRO_VENDA >= 20000) || $item->INAT_ID == 6) {
                $H_LUCRO_VENDA['IR'] = number_format( (($LUCRO_VENDA * 0.15) ), 2, '.', '');
              }
              if($item->INAT_ID == 4) {
                $H_LUCRO_VENDA['IR'] = number_format( (($LUCRO_VENDA * 0.20) ), 2, '.', '');
              }

              $arrConsolidado[$INCT_INCR_INAV]['H_LUCRO_VENDA'][] = $H_LUCRO_VENDA;
              
            }
          }
          
          unset($arrItemsOrdem[$key_item], $TOTAL_RENDIMENTO, $TOTAL_DIVIDENDO, $TOTAL_JSCP, $MES_RENDIMENTO, $MES_DIVIDENDO, $MES_JSCP, $item);
        }
        unset($arrSplit, $arrCotacao, $arrRendimento, $arrItemsOrdem, $key_item, $item);

        
        // salvar consolidado
        foreach ($arrConsolidado as $keyMes => $consolidado) {
          $consolidado = (object)$consolidado;
          
          if( !isset($arrconsolidadosConsolidadoControll[$INCT_INCR_INAV][$mes]) ) {
            $DATA      = "DATA_{$mes}";
            $alias     = "INAV_{$consolidado->INAV_ID}";
            $consolidado->MES_CONSOLIDADO = $mes;
            
            if( isset($this->carteiraConsolidado["{$DATA}-{$alias}"]) ) {
              $INCTC_ID     = $this->carteiraConsolidado["{$DATA}-{$alias}"]->INCTC_ID;
              $carteiraItem = $this->carteiraItem->find($INCTC_ID);
              $carteiraItem->INCTC_CONTENT = json_encode($consolidado);
              $carteiraItem->save();
              
            } else {
              $carteiraItem = new I_CarteiraItem;
              $carteiraItem->INCTC_DATA    = $mes;
              $carteiraItem->INCTC_CONTENT = json_encode($consolidado);
              $carteiraItem->INCT_ID       = $this->INCT_ID;
              $carteiraItem->INCR_ID       = $INCR_ID;
              $carteiraItem->INAV_ID       = $INAV_ID;
              $carteiraItem->USUA_ID       = $this->USUA_ID;
              $carteiraItem->save();
            }

            unset($INCTC_ID, $carteiraItem, $item);
          }
          $arrConsolidadoControll[$INCT_INCR_INAV][$mes] = true;
        }
        $mes = date('Y-m-d', strtotime($mes . '+1 month'));
      }

      unset($arrConsolidado, $INAV_ID, $INCR_ID, $INOD_DATA);
    }
    unset($arrConsolidadoControll);

    return true;
  }

  private function getCotacao() {
    $I_ativoCotacao = new I_AtivoCotacao;

    $items = $I_ativoCotacao->get(
      [
        'usuario'     => $this->USUA_ID,
        'INAV_ID'     => $this->INAV_ID,
        'INAC_STATUS' => 1,
        'orderby'     => 'INAC_DATA:ASC',
      ],
      [
        'SELECT' => 'INAC'
      ]
    );

    if(count($items) == 0) return;

    foreach($items as $key => $item) {
      $aliasID   = "INAV_{$item->INAV_ID}";
      $aliasDATA = date('Y-m', strtotime($item->INAC_DATA));

      $items[$aliasID][$aliasDATA][] = $item;
      
      unset($aliasID); unset($aliasDATA); unset($items[$key]);
    }
    
    $this->arrCotacao = $items;

    unset($I_ativoCotacao); unset($items);
  }

  private function getSplitInplit() {
    $I_AtivoSplit = new I_AtivoSplit;

    $items = $I_AtivoSplit->get([
      'usuario'     => $this->USUA_ID,
      'INAV_ID'     => $this->INAV_ID,
      'INAS_STATUS' => 1,
      'orderby'     => 'INAS_DATA:ASC',
    ]);

    if(count($items) == 0) return;

    foreach($items as $key => $item) {
      $aliasID   = "INAV_{$item->INAV_ID}";
      $aliasDATA = date('Y-m', strtotime($item->INAS_DATA));

      $items[$aliasID][$aliasDATA][] = $item;
      
      unset($aliasID); unset($aliasDATA); unset($items[$key]);
    }
    
    $this->arrSplit = $items;
    
    unset($I_AtivoSplit); unset($items);
  }

  private function getRendimento() {
    $I_ativoRendimento = new I_AtivoRendimento;

    $items = $I_ativoRendimento->get([
      'usuario' => $this->USUA_ID,
      'INCT_ID' => $this->INCT_ID,
      'INAV_ID' => $this->INAV_ID,
      'dataAte' => $this->dataAte,
    ]);
    
    if(count($items) == 0) return;

    foreach($items as $key_item => $item) {
      $aliasID   = "INAV_{$item->INAV_ID}";
      $aliasDATA = date('Y-m', strtotime($item->INAR_DATA));

      $items[$aliasID][$aliasDATA][] = $item;
      
      unset($aliasID); unset($aliasDATA); unset($items[$key_item]);
    }
    
    $this->arrRendimento = $items;
    
    unset($I_ativoRendimento); unset($items);
  }

  private function getItems() {
    $I_item = new I_ItemModel;
    
    if($this->INAV_ID !== false){
      $explode = explode('|', $this->INAV_ID);
      $this->INAV_ID = array_filter($explode, function($value){ return $value; });
    }

    $this->itemsOrdem =  $I_item->get([
      'usuario' => $this->USUA_ID,
      'INCT_ID' => $this->INCT_ID,
      'dataAte' => $this->dataAte,
      'INAV_ID' => $this->INAV_ID,
      'INTP_ID' => $this->INTP_ID,
      'orderby' => 'INOD_DATA:ASC|INIT_ID:ASC|INCR_DESCRICAO:ASC|INTP_DESCRICAO:ASC|INAT_DESCRICAO:ASC|INAV_CODIGO:ASC'
    ]);
  }

  private function getItemsDataInicial() {

    $INAV_ID = array_map(function($e){ return $e->INAV_ID; }, $this->itemsOrdem);
    $INAV_ID = array_unique($INAV_ID);
    $INAV_ID = array_values($INAV_ID);
    sort($INAV_ID);

    $this->itemsDataInicial = $this->carteiraItem->dataInicial([
      'usuario' => $this->USUA_ID,
      'INCT_ID' => $this->INCT_ID,
      'INAV_ID' => $this->INAV_ID,
      'dataDe'  => $this->dataDe,
    ]);
  }
  
  private function getConsolidadoCarteiraItem() {

    $INAV_ID = array_map(function($e){ return $e->INAV_ID; }, $this->itemsOrdem);
    $INAV_ID = array_unique($INAV_ID);
    $INAV_ID = array_values($INAV_ID);
    sort($INAV_ID);


    $result = $this->carteiraItem->get([
      'usuario' => $this->USUA_ID,
      'INCT_ID' => $this->INCT_ID,
      'INAV_ID' => $INAV_ID,
    ]);

    foreach ($result as $item) {
      $DATA  = "DATA_{$item->INCTC_DATA}";
      $alias = "INAV_{$item->INAV_ID}";
      $this->carteiraConsolidado["{$DATA}-{$alias}"] = (object)[
        'INCTC_ID'   => $item->INCTC_ID,
        'INCTC_DATA' => $item->INCTC_DATA,
      ];
    }
    unset($INAV_ID, $result, $item);
  }

  private function searchSplit($INAV_ID, $mes) {
    $retorno = [];
    $mes = date('Y-m', strtotime($mes));

    if(isset($this->arrSplit["INAV_{$INAV_ID}"]))
    {
      foreach ($this->arrSplit["INAV_{$INAV_ID}"] as $key_items => $items) {
        foreach ($items as $key_item => $item) {
          if(date('Y-m', strtotime($item->INAS_DATA)) == $mes) $retorno[] = $item;
        }
      }
    }

    return $retorno;
  }

  private function searchCotacao($INAV_ID, $mes) {
    $mes = date('Y-m', strtotime($mes));
    
    if( isset($this->arrCotacao["INAV_{$INAV_ID}"]) && isset($this->arrCotacao["INAV_{$INAV_ID}"][$mes]) )
      return $this->arrCotacao["INAV_{$INAV_ID}"][$mes];

    return [];
  }

  private function searchRendimento($INAV_ID, $mes) {
    $retorno = [];
    $mes = date('Y-m', strtotime($mes));
    
    if(isset($this->arrRendimento["INAV_{$INAV_ID}"]))
    {
      foreach ($this->arrRendimento["INAV_{$INAV_ID}"] as $key_items => $items) {
        foreach ($items as $key_item => $item) {
          
          $diff = date_diff(
            date_create(date('Y-m', strtotime($item->INAR_DATA))), 
            date_create($mes)
          );
    
          if($diff->invert) $retorno[] = $item;
        }
      }
    }
    return $retorno;
  }

  private function searchItems($INAV_ID, $INCR_ID, $mes) {
    $retorno = [];
    $mes = date('Y-m', strtotime($mes));
    
    foreach($this->itemsOrdem as $item){

      if(
        $INAV_ID == $item->INAV_ID && 
        $INCR_ID == $item->INCR_ID && 
        $mes == date('Y-m', strtotime($item->INOD_DATA))
      ) {
        $retorno[] = $item;
      }
    }

    return $retorno;
  }

  private function formatDate($strDate, $day = '') {
      if($day == '') die("Informe o retorno de 'day'.");

      if($strDate !== false) {
      
      if(strlen($strDate) == 7) {
        $date = $strDate;
      }
      if(strlen($strDate) == 10) {
        $strAno = date('Y', strtotime($strDate));
        $strMes = date('m', strtotime($strDate));
        $date   = "{$strAno}-{$strMes}";
      }

      if($day == 'first') {
        $date .= '-01';
      } else {
        $date = $date . '-01';

        $strAno = date('Y', strtotime($date));
        $strMes = date('m', strtotime($date));
        $strDia = cal_days_in_month(CAL_GREGORIAN, $strMes , $strAno);
        
        $date = "{$strAno}-{$strMes}-{$strDia}";
      }

      return $date;
    }
  }
}