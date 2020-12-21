<?php

namespace App\Rule;

class Investimento_Carteira_analiseTipo {
  
  public $get;
  public $ITEMS;
  public $M_ATIVORENDIMENTO;
  
  public function exec()
  {
    $this->base();
    
    if($this->get['tipo'] == 'tipo') {

      $this->render_tipo();
      $this->render__format();

    }else if($this->get['tipo'] == 'tipoAtivo') {

      $this->render_tipoAtivo();
      $this->render__format();

    }else if($this->get['tipo'] == 'ativo') {

      $this->render_ativo();
      $this->render__format();

    }

    return $this->ITEMS;
  }

  private function base() {

    foreach ($this->ITEMS as $key => $item) {
      $INCR = $item->INCR_ID;
      $INAV = $item->INAV_ID;

      if(isset($this->ITEMS["{$INCR}_{$INAV}"])){
        if($item->INIT_CV === 'C'){
          $this->ITEMS["{$INCR}_{$INAV}"]->COTAS_COMPRA = $item->INIT_COTAS;
          $this->ITEMS["{$INCR}_{$INAV}"]->TOTAL_COMPRA = $item->INIT_PRECO_TOTAL;
          
        }else if($item->INIT_CV === 'V'){
          $this->ITEMS["{$INCR}_{$INAV}"]->COTAS_VENDA = $item->INIT_COTAS;
          $this->ITEMS["{$INCR}_{$INAV}"]->TOTAL_VENDA = $item->INIT_PRECO_TOTAL;

        }

      }else{
        $this->ITEMS["{$INCR}_{$INAV}"] = (object)[
          "INIT_CV"          => $item->INIT_CV,

          "INCR_ID"          => $item->INCR_ID,
          "INCR_DESCRICAO"   => $item->INCR_DESCRICAO,
          "INCR_STATUS"      => $item->INCR_STATUS,

          "INTP_ID"          => $item->INTP_ID,
          "INTP_DESCRICAO"   => $item->INTP_DESCRICAO,
          "INTP_STATUS"      => $item->INTP_STATUS,

          "INAT_ID"          => $item->INAT_ID,
          "INAT_DESCRICAO"   => $item->INAT_DESCRICAO,
          "INAT_STATUS"      => $item->INAT_STATUS,

          "INAV_ID"          => $item->INAV_ID,
          "INAV_CODIGO"   => $item->INAV_CODIGO,
          "INAV_LIQUIDEZ"    => $item->INAV_LIQUIDEZ,
          "INAV_VENC"        => $item->INAV_VENC,
          "INAV_STATUS"      => $item->INAV_STATUS,

          "COTAS_COMPRA"     => 0,
          "TOTAL_COMPRA"     => 0,
          "COTAS_VENDA"      => 0,
          "TOTAL_VENDA"      => 0,

          "COTAS"            => 0,
          "TOTAL_APLICADO"   => 0,
          "TOTAL_BRUTO"      => 0,

          "PRECO_MEDIO"      => 0,
          "RENDIMENTO_MES"   => 0,
          "DIVIDENDO_MES"    => 0,
          "JSCP_MES"         => 0,
          "RENDIMENTO_TOTAL" => 0,
          "DIVIDENDO_TOTAL"  => 0,
          "JSCP_TOTAL"       => 0,
        ];

        if($item->INIT_CV === 'C'){
          $this->ITEMS["{$INCR}_{$INAV}"]->COTAS_COMPRA = $item->INIT_COTAS;
          $this->ITEMS["{$INCR}_{$INAV}"]->TOTAL_COMPRA = $item->INIT_PRECO_TOTAL;
          
        }else if($item->INIT_CV === 'V'){
          $this->ITEMS["{$INCR}_{$INAV}"]->COTAS_VENDA = $item->INIT_COTAS;
          $this->ITEMS["{$INCR}_{$INAV}"]->TOTAL_VENDA = $item->INIT_PRECO_TOTAL;

        }
      }
      unset($this->ITEMS[$key]);
    }
    
    $tmp = [];
    foreach ($this->ITEMS as $key => $item) {
      $result           = $this->M_ATIVORENDIMENTO->rendimentoPorAtivoCorretora($this->get,  $item->INCR_ID, $item->INAV_ID );
      $RENDIMENTO_TOTAL = 0;
      $DIVIDENDO_TOTAL  = 0;
      $JSCP_TOTAL       = 0;
      $RENDIMENTO_MES   = 0;
      $DIVIDENDO_MES    = 0;
      $JSCP_MES         = 0;

      foreach ($result as $key_r => $value) {
        $mesIgual =  $value->INAR_DATA == $this->get['mes'];

        // RENDA FIXA
        if($item->INTP_ID == 1 ) { 
          $RENDIMENTO_TOTAL += $value->INAR_VALOR;          // soma rendimentos totais acumulados
          if($mesIgual) $RENDIMENTO_MES += $value->INAR_VALOR;  // soma rendimentos do mes atual

        } 

        // RENDA VARIAVEL
        else if ($item->INTP_ID == 2){       

          // DIVIDENDOS
          if($value->INAR_TIPO === 'D'){
            $DIVIDENDO_TOTAL += $value->INAR_VALOR;          // soma DIVIDENDOS totais acumulados
            if($mesIgual) $DIVIDENDO_MES += $value->INAR_VALOR;  // soma DIVIDENDOS do mes atual
          }

          // JSCP
          if($value->INAR_TIPO === 'J'){
            $JSCP_TOTAL += $value->INAR_VALOR;          // soma JSCP totais acumulados
            if($mesIgual) $JSCP_MES += $value->INAR_VALOR;  // soma JSCP do mes atual

          }
        }

        unset($result[$key_r]);
      }

      // APURA TOTAL DE COTAS
      $this->ITEMS[$key]->COTAS = $item->COTAS_COMPRA - $item->COTAS_VENDA;
      
      //APURA TOTAL INVESTIDO
      $this->ITEMS[$key]->TOTAL_APLICADO = $item->TOTAL_COMPRA - $item->TOTAL_VENDA;
      
      // APURA PREÇO MÉDIO
      $this->ITEMS[$key]->PRECO_MEDIO = $this->ITEMS[$key]->TOTAL_APLICADO / $this->ITEMS[$key]->COTAS;

      // APURA VALORES DO ATIVO
      if($item->INTP_ID == 1) { // RENDA FIXA
        $this->ITEMS[$key]->TOTAL_BRUTO       = $this->ITEMS[$key]->TOTAL_APLICADO + $RENDIMENTO_TOTAL;
        $this->ITEMS[$key]->RENDIMENTO_TOTAL  = $RENDIMENTO_TOTAL;
        $this->ITEMS[$key]->DIVIDENDO_MES     = $DIVIDENDO_MES;


      } else if ($item->INTP_ID == 2){ // RENDA VARIAVEL
        $this->ITEMS[$key]->TOTAL_BRUTO      = $this->ITEMS[$key]->TOTAL_APLICADO + $this->ITEMS[$key]->RENDIMENTO_MES;
        $this->ITEMS[$key]->DIVIDENDO_TOTAL  = $DIVIDENDO_TOTAL;
        $this->ITEMS[$key]->DIVIDENDO_MES    = $DIVIDENDO_MES;
        $this->ITEMS[$key]->JSCP_TOTAL       = $JSCP_TOTAL;
        $this->ITEMS[$key]->JSCP_MES         = $JSCP_MES;

      }
      $tmp[] = $this->ITEMS[$key];
    }

    $this->ITEMS = $tmp;
  }

  private function render_tipo(){

    foreach ($this->ITEMS as $key => $item) {
      $INTP = $item->INTP_ID;

      if( isset($this->ITEMS["TIPO_{$INTP}"]) ){

        $this->ITEMS["TIPO_{$INTP}"]->COTAS_COMPRA     += $item->COTAS_COMPRA;
        $this->ITEMS["TIPO_{$INTP}"]->TOTAL_COMPRA     += $item->TOTAL_COMPRA;
        $this->ITEMS["TIPO_{$INTP}"]->COTAS_VENDA      += $item->COTAS_VENDA;
        $this->ITEMS["TIPO_{$INTP}"]->TOTAL_VENDA      += $item->TOTAL_VENDA;
        $this->ITEMS["TIPO_{$INTP}"]->COTAS            += $item->COTAS;
        $this->ITEMS["TIPO_{$INTP}"]->TOTAL_APLICADO   += $item->TOTAL_APLICADO;
        $this->ITEMS["TIPO_{$INTP}"]->TOTAL_BRUTO      += $item->TOTAL_BRUTO;
        $this->ITEMS["TIPO_{$INTP}"]->RENDIMENTO_TOTAL += $item->RENDIMENTO_TOTAL;
        $this->ITEMS["TIPO_{$INTP}"]->DIVIDENDO_TOTAL  += $item->DIVIDENDO_TOTAL;
        $this->ITEMS["TIPO_{$INTP}"]->JSCP_TOTAL       += $item->JSCP_TOTAL;
        $this->ITEMS["TIPO_{$INTP}"]->RENDIMENTO_MES   += $item->RENDIMENTO_MES;
        $this->ITEMS["TIPO_{$INTP}"]->DIVIDENDO_MES    += $item->DIVIDENDO_MES;
        $this->ITEMS["TIPO_{$INTP}"]->JSCP_MES         += $item->JSCP_MES;

      }else{
        $this->ITEMS["TIPO_{$INTP}"] = (object)[
          "DESCRICAO"        => $item->INTP_DESCRICAO,
          "COTAS_COMPRA"     => 0,
          "TOTAL_COMPRA"     => 0,
          "COTAS_VENDA"      => 0,
          "TOTAL_VENDA"      => 0,
          "COTAS"            => 0,
          "TOTAL_APLICADO"   => 0,
          "TOTAL_BRUTO"      => 0,
          "RENDIMENTO_TOTAL" => 0,
          "DIVIDENDO_TOTAL"  => 0,
          "JSCP_TOTAL"       => 0,
          "RENDIMENTO_MES"   => 0,
          "DIVIDENDO_MES"    => 0,
          "JSCP_MES"         => 0,
        ];

        $this->ITEMS["TIPO_{$INTP}"]->COTAS_COMPRA     = $item->COTAS_COMPRA;
        $this->ITEMS["TIPO_{$INTP}"]->TOTAL_COMPRA     = $item->TOTAL_COMPRA;
        $this->ITEMS["TIPO_{$INTP}"]->COTAS_VENDA      = $item->COTAS_VENDA;
        $this->ITEMS["TIPO_{$INTP}"]->TOTAL_VENDA      = $item->TOTAL_VENDA;
        $this->ITEMS["TIPO_{$INTP}"]->COTAS            = $item->COTAS;
        $this->ITEMS["TIPO_{$INTP}"]->TOTAL_APLICADO   = $item->TOTAL_APLICADO;
        $this->ITEMS["TIPO_{$INTP}"]->TOTAL_BRUTO      = $item->TOTAL_BRUTO;
        $this->ITEMS["TIPO_{$INTP}"]->RENDIMENTO_TOTAL = $item->RENDIMENTO_TOTAL;
        $this->ITEMS["TIPO_{$INTP}"]->DIVIDENDO_TOTAL  = $item->DIVIDENDO_TOTAL;
        $this->ITEMS["TIPO_{$INTP}"]->JSCP_TOTAL       = $item->JSCP_TOTAL;
        $this->ITEMS["TIPO_{$INTP}"]->RENDIMENTO_MES   = $item->RENDIMENTO_MES;
        $this->ITEMS["TIPO_{$INTP}"]->DIVIDENDO_MES    = $item->DIVIDENDO_MES;
        $this->ITEMS["TIPO_{$INTP}"]->JSCP_MES         = $item->JSCP_MES;
      }
      
      unset($this->ITEMS[$key]);
    }
  }

  private function render_tipoAtivo(){

    foreach ($this->ITEMS as $key => $item) {
      $INAT = $item->INAT_ID;

      if( isset($this->ITEMS["TIPO_{$INAT}"]) ){

        $this->ITEMS["TIPO_{$INAT}"]->COTAS_COMPRA     += $item->COTAS_COMPRA;
        $this->ITEMS["TIPO_{$INAT}"]->TOTAL_COMPRA     += $item->TOTAL_COMPRA;
        $this->ITEMS["TIPO_{$INAT}"]->COTAS_VENDA      += $item->COTAS_VENDA;
        $this->ITEMS["TIPO_{$INAT}"]->TOTAL_VENDA      += $item->TOTAL_VENDA;
        $this->ITEMS["TIPO_{$INAT}"]->COTAS            += $item->COTAS;
        $this->ITEMS["TIPO_{$INAT}"]->TOTAL_APLICADO   += $item->TOTAL_APLICADO;
        $this->ITEMS["TIPO_{$INAT}"]->TOTAL_BRUTO      += $item->TOTAL_BRUTO;
        $this->ITEMS["TIPO_{$INAT}"]->RENDIMENTO_TOTAL += $item->RENDIMENTO_TOTAL;
        $this->ITEMS["TIPO_{$INAT}"]->DIVIDENDO_TOTAL  += $item->DIVIDENDO_TOTAL;
        $this->ITEMS["TIPO_{$INAT}"]->JSCP_TOTAL       += $item->JSCP_TOTAL;
        $this->ITEMS["TIPO_{$INAT}"]->RENDIMENTO_MES   += $item->RENDIMENTO_MES;
        $this->ITEMS["TIPO_{$INAT}"]->DIVIDENDO_MES    += $item->DIVIDENDO_MES;
        $this->ITEMS["TIPO_{$INAT}"]->JSCP_MES         += $item->JSCP_MES;

      }else{
        $this->ITEMS["TIPO_{$INAT}"] = (object)[
          "DESCRICAO"        => $item->INAT_DESCRICAO,
          "COTAS_COMPRA"     => 0,
          "TOTAL_COMPRA"     => 0,
          "COTAS_VENDA"      => 0,
          "TOTAL_VENDA"      => 0,
          "COTAS"            => 0,
          "TOTAL_APLICADO"   => 0,
          "TOTAL_BRUTO"      => 0,
          "RENDIMENTO_TOTAL" => 0,
          "DIVIDENDO_TOTAL"  => 0,
          "JSCP_TOTAL"       => 0,
          "RENDIMENTO_MES"   => 0,
          "DIVIDENDO_MES"    => 0,
          "JSCP_MES"         => 0,
        ];

        $this->ITEMS["TIPO_{$INAT}"]->COTAS_COMPRA     = $item->COTAS_COMPRA;
        $this->ITEMS["TIPO_{$INAT}"]->TOTAL_COMPRA     = $item->TOTAL_COMPRA;
        $this->ITEMS["TIPO_{$INAT}"]->COTAS_VENDA      = $item->COTAS_VENDA;
        $this->ITEMS["TIPO_{$INAT}"]->TOTAL_VENDA      = $item->TOTAL_VENDA;
        $this->ITEMS["TIPO_{$INAT}"]->COTAS            = $item->COTAS;
        $this->ITEMS["TIPO_{$INAT}"]->TOTAL_APLICADO   = $item->TOTAL_APLICADO;
        $this->ITEMS["TIPO_{$INAT}"]->TOTAL_BRUTO      = $item->TOTAL_BRUTO;
        $this->ITEMS["TIPO_{$INAT}"]->RENDIMENTO_TOTAL = $item->RENDIMENTO_TOTAL;
        $this->ITEMS["TIPO_{$INAT}"]->DIVIDENDO_TOTAL  = $item->DIVIDENDO_TOTAL;
        $this->ITEMS["TIPO_{$INAT}"]->JSCP_TOTAL       = $item->JSCP_TOTAL;
        $this->ITEMS["TIPO_{$INAT}"]->RENDIMENTO_MES   = $item->RENDIMENTO_MES;
        $this->ITEMS["TIPO_{$INAT}"]->DIVIDENDO_MES    = $item->DIVIDENDO_MES;
        $this->ITEMS["TIPO_{$INAT}"]->JSCP_MES         = $item->JSCP_MES;
      }

      unset($this->ITEMS[$key]);
    }
  }

  private function render_ativo(){

    foreach ($this->ITEMS as $key => $item) {
      $INAV = $item->INAV_ID;

      if( isset($this->ITEMS["ATIVO_{$INAV}"]) ){

        $this->ITEMS["ATIVO_{$INAV}"]->COTAS_COMPRA     += $item->COTAS_COMPRA;
        $this->ITEMS["ATIVO_{$INAV}"]->TOTAL_COMPRA     += $item->TOTAL_COMPRA;
        $this->ITEMS["ATIVO_{$INAV}"]->COTAS_VENDA      += $item->COTAS_VENDA;
        $this->ITEMS["ATIVO_{$INAV}"]->TOTAL_VENDA      += $item->TOTAL_VENDA;
        $this->ITEMS["ATIVO_{$INAV}"]->COTAS            += $item->COTAS;
        $this->ITEMS["ATIVO_{$INAV}"]->TOTAL_APLICADO   += $item->TOTAL_APLICADO;
        $this->ITEMS["ATIVO_{$INAV}"]->TOTAL_BRUTO      += $item->TOTAL_BRUTO;
        $this->ITEMS["ATIVO_{$INAV}"]->RENDIMENTO_TOTAL += $item->RENDIMENTO_TOTAL;
        $this->ITEMS["ATIVO_{$INAV}"]->DIVIDENDO_TOTAL  += $item->DIVIDENDO_TOTAL;
        $this->ITEMS["ATIVO_{$INAV}"]->JSCP_TOTAL       += $item->JSCP_TOTAL;
        $this->ITEMS["ATIVO_{$INAV}"]->RENDIMENTO_MES   += $item->RENDIMENTO_MES;
        $this->ITEMS["ATIVO_{$INAV}"]->DIVIDENDO_MES    += $item->DIVIDENDO_MES;
        $this->ITEMS["ATIVO_{$INAV}"]->JSCP_MES         += $item->JSCP_MES;

      }else{
        $this->ITEMS["ATIVO_{$INAV}"] = (object)[
          "DESCRICAO"        => $item->INAV_CODIGO,
          "COTAS_COMPRA"     => 0,
          "TOTAL_COMPRA"     => 0,
          "COTAS_VENDA"      => 0,
          "TOTAL_VENDA"      => 0,
          "COTAS"            => 0,
          "TOTAL_APLICADO"   => 0,
          "TOTAL_BRUTO"      => 0,
          "RENDIMENTO_TOTAL" => 0,
          "DIVIDENDO_TOTAL"  => 0,
          "JSCP_TOTAL"       => 0,
          "RENDIMENTO_MES"   => 0,
          "DIVIDENDO_MES"    => 0,
          "JSCP_MES"         => 0,
        ];

        $this->ITEMS["ATIVO_{$INAV}"]->COTAS_COMPRA     = $item->COTAS_COMPRA;
        $this->ITEMS["ATIVO_{$INAV}"]->TOTAL_COMPRA     = $item->TOTAL_COMPRA;
        $this->ITEMS["ATIVO_{$INAV}"]->COTAS_VENDA      = $item->COTAS_VENDA;
        $this->ITEMS["ATIVO_{$INAV}"]->TOTAL_VENDA      = $item->TOTAL_VENDA;
        $this->ITEMS["ATIVO_{$INAV}"]->COTAS            = $item->COTAS;
        $this->ITEMS["ATIVO_{$INAV}"]->TOTAL_APLICADO   = $item->TOTAL_APLICADO;
        $this->ITEMS["ATIVO_{$INAV}"]->TOTAL_BRUTO      = $item->TOTAL_BRUTO;
        $this->ITEMS["ATIVO_{$INAV}"]->RENDIMENTO_TOTAL = $item->RENDIMENTO_TOTAL;
        $this->ITEMS["ATIVO_{$INAV}"]->DIVIDENDO_TOTAL  = $item->DIVIDENDO_TOTAL;
        $this->ITEMS["ATIVO_{$INAV}"]->JSCP_TOTAL       = $item->JSCP_TOTAL;
        $this->ITEMS["ATIVO_{$INAV}"]->RENDIMENTO_MES   = $item->RENDIMENTO_MES;
        $this->ITEMS["ATIVO_{$INAV}"]->DIVIDENDO_MES    = $item->DIVIDENDO_MES;
        $this->ITEMS["ATIVO_{$INAV}"]->JSCP_MES         = $item->JSCP_MES;
      }
      
      $tmp[] = $this->ITEMS["ATIVO_{$INAV}"];

      unset($this->ITEMS[$key]);
    }

  }

  private function render__format(){
    $tmp = [];
    foreach ($this->ITEMS as $key => $item) {
      $tmp[] = $item;
    }

    $this->ITEMS = $tmp;
  }

}