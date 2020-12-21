<?php

namespace App\Rule;

class Investimento_Carteira_composicao {
  
  public $get;
  public $ITEMS;
  public $M_ATIVORENDIMENTO;
  public $M_CARTEIRA;
  
  public function exec()
  {

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
          "RENDIMENTO_TOTAL" => 0,
          "DIVIDENDO_TOTAL"  => 0,
          "JSCP_TOTAL"       => 0,
          "RENDIMENTO_MES"   => 0,
          "DIVIDENDO_MES"    => 0,
          "JSCP_MES"         => 0,
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
    // dd($this->ITEMS);

    $tmp = [];
    foreach ($this->ITEMS as $key => $item) {

      $rendimentos      = $this->M_ATIVORENDIMENTO->rendimentoPorAtivoCorretora($this->get,  $item->INCR_ID, $item->INAV_ID );
      $RENDIMENTO_TOTAL = 0;
      $DIVIDENDO_TOTAL  = 0;
      $JSCP_TOTAL       = 0;
      $RENDIMENTO_MES   = 0;
      $DIVIDENDO_MES        = 0;
      $JSCP_MES             = 0;

      // dd($rendimentos);
      foreach ($rendimentos as $key_r => $rendimento) {
        $mesIgual =  $rendimento->INAR_DATA == $this->get['mes'];

        // RENDA FIXA
        if($item->INTP_ID == 1 ) { 
          $RENDIMENTO_TOTAL += $rendimento->INAR_VALOR;          // soma rendimentos totais acumulados
          if($mesIgual) $RENDIMENTO_MES += $rendimento->INAR_VALOR;  // soma rendimentos do mes atual

        } 

        // RENDA VARIAVEL
        else if ($item->INTP_ID == 2){       

          // DIVIDENDOS
          if($rendimento->INAR_TIPO === 'D'){
            $DIVIDENDO_TOTAL += $rendimento->INAR_VALOR;          // soma DIVIDENDOS totais acumulados
            if($mesIgual) $DIVIDENDO_MES += $rendimento->INAR_VALOR;  // soma DIVIDENDOS do mes atual
          }

          // JSCP
          if($rendimento->INAR_TIPO === 'J'){
            $JSCP_TOTAL += $rendimento->INAR_VALOR;          // soma JSCP totais acumulados
            if($mesIgual) $JSCP_MES += $rendimento->INAR_VALOR;  // soma JSCP do mes atual

          }
        }

        unset($rendimentos[$key_r]);
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
        $this->ITEMS[$key]->RENDIMENTO_MES        = $RENDIMENTO_MES;


      } else if ($item->INTP_ID == 2){ // RENDA VARIAVEL
        $this->ITEMS[$key]->TOTAL_BRUTO      = $this->ITEMS[$key]->TOTAL_APLICADO + $this->ITEMS[$key]->RENDIMENTO_MES;
        $this->ITEMS[$key]->DIVIDENDO_TOTAL  = $DIVIDENDO_TOTAL;
        $this->ITEMS[$key]->JSCP_TOTAL       = $JSCP_TOTAL;
        $this->ITEMS[$key]->DIVIDENDO_MES    = $DIVIDENDO_MES;
        $this->ITEMS[$key]->JSCP_MES         = $JSCP_MES;

      }

      $tmp[] = $this->ITEMS[$key];
    }

    $this->ITEMS = $tmp;
    
    return $this->ITEMS;
  }
}