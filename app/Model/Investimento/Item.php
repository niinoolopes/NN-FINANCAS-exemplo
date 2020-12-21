<?php

namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Item extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'INIT_ID';

  public  $timestamps = false;

  // --

  public function __construct()
  {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestItem;
  }

  public function get($get)
  {
    $usuario     = $get['usuario'];
    $data        = isset($get['data'])        ? $get['data']        : false;
    $dataDe      = isset($get['dataDe'])      ? $get['dataDe']      : false;
    $dataAte     = isset($get['dataAte'])     ? $get['dataAte']     : false;
    $INIT_STATUS = isset($get['INIT_STATUS']) ? $get['INIT_STATUS'] : false;
    $INIT_ID     = isset($get['INIT_ID'])     ? $get['INIT_ID']     : false;
    $INAV_ID     = isset($get['INAV_ID'])     ? $get['INAV_ID']     : false;
    $INAT_ID     = isset($get['INAT_ID'])     ? $get['INAT_ID']     : false;
    $INTP_ID     = isset($get['INTP_ID'])     ? $get['INTP_ID']     : false;
    $INOD_ID     = isset($get['INOD_ID'])     ? $get['INOD_ID']     : false;
    $INCR_ID     = isset($get['INCR_ID'])     ? $get['INCR_ID']     : false;
    $INCT_ID     = isset($get['INCT_ID'])     ? $get['INCT_ID']     : false;
    $orderby     = isset($get['orderby'])     ? $get['orderby']     : false;
   
    $query  = "SELECT ";
    $query .= "INIT.INIT_ID, INIT.INIT_NEGOCIACAO, INIT.INIT_STATUS, INIT.INIT_CV, INIT.INIT_MERCADO, INIT.INIT_DC, INIT.INIT_COTAS, INIT.INIT_PRECO_UNICO, INIT.INIT_PRECO_TOTAL, ";
    $query .= "INAV.INAV_ID, INAV.INAV_DESCRICAO, INAV.INAV_STATUS, INAV.INAV_CODIGO, INAV.INAV_CPNJ, INAV.INAV_SITE, INAV.INAV_LIQUIDEZ, INAV.INAV_VENC, ";
    $query .= "INAT.INAT_ID, INAT.INAT_DESCRICAO, INAT.INAT_STATUS, ";
    $query .= "INTP.INTP_ID, INTP.INTP_DESCRICAO, INTP.INTP_STATUS, ";
    $query .= "INCR.INCR_ID, INCR.INCR_DESCRICAO, INCR.INCR_STATUS, ";
    $query .= "INCT.INCT_ID, INCT.INCT_DESCRICAO, INCT.INCT_STATUS, INCT.INCT_PAINEL, ";
    $query .= "INOD.INOD_ID, INOD.INOD_DESCRICAO, INOD.INOD_STATUS, INOD.INOD_DATA, ";
    $query .= "INTX.INTX_ID, INTX.INTX_VALOR_LIQUIDO_OPERACOES, INTX.INTX_TAXA_LIQUIDACAO, INTX.INTX_TAXA_REGISTRO, 
               INTX.INTX_TAXA_TERMO_OPERACOES, INTX.INTX_TAXA_ANA, INTX.INTX_EMOLUMENTOS, INTX.INTX_TAXA_OPERACIONAL, 
               INTX.INTX_EXECUCAO, INTX.INTX_TAXA_CUSTODIA, INTX.INTX_IMPOSTOS, INTX.INTX_IRRF_OPERACOES, INTX.INTX_OUTRO, INTX.INTX_STATUS, ";
    $query .= "USUA.USUA_ID, USUA.USUA_NOME ";
    $query .= "FROM       {$this->DBTables->InvestItem}       INIT ";
    $query .= "INNER JOIN {$this->DBTables->InvestOrdem}      INOD ON INOD.INOD_ID = INIT.INOD_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestTaxas}      INTX ON INTX.INTX_ID = INOD.INTX_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivo}      INAV ON INAV.INAV_ID = INIT.INAV_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivoTipo}  INAT ON INAT.INAT_ID = INAV.INAT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestTipo}       INTP ON INTP.INTP_ID = INAT.INTP_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCorretora}  INCR ON INCR.INCR_ID = INOD.INCR_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCarteira}   INCT ON INCT.INCT_ID = INOD.INCT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestIntegrante} INTG ON INTG.INTG_ID = INCT.INCT_ID ";
    $query .= "INNER JOIN {$this->DBTables->usuario}          USUA ON USUA.USUA_ID = INTG.USUA_ID ";
    
    $query .= "WHERE USUA.USUA_ID = {$usuario} ";
    $query .= "AND INOD.INOD_STATUS = 1 ";
    
    if($dataDe !== false && $dataAte !== false && $data == false) {
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
        $DATA_FORMATE = "DATE_FORMAT(INOD.INOD_DATA, '{$FORMAT}')";
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
        $DATA_FORMATE = "DATE_FORMAT(INOD.INOD_DATA, '{$FORMAT}')";
      } 

      $query .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false && $data == false) {
      if(strlen($dataAte) == 4)  $FORMAT = '%Y';
      if(strlen($dataAte) == 7)  $FORMAT = '%Y-%m';
      if(strlen($dataAte) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INOD.INOD_DATA, '{$FORMAT}') <= '$dataAte' ";

    }else if( $data !== false) {
      if(strlen($data) == 4)  $FORMAT = '%Y';
      if(strlen($data) == 7)  $FORMAT = '%Y-%m';
      if(strlen($data) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INOD.INOD_DATA, '{$FORMAT}') = '$data' ";
    }

    if($INIT_STATUS !== false) $query .= "AND INIT.INIT_STATUS = {$INIT_STATUS} ";
    if($INIT_ID !== false)     $query .= "AND INIT.INIT_ID     = {$INIT_ID} ";
    if($INAT_ID !== false)     $query .= "AND INAT.INAT_ID     = {$INAT_ID} ";
    if($INTP_ID !== false)     $query .= "AND INTP.INTP_ID     = {$INTP_ID} ";
    if($INOD_ID !== false)     $query .= "AND INOD.INOD_ID     = {$INOD_ID} ";
    if($INCR_ID !== false)     $query .= "AND INCR.INCR_ID     = {$INCR_ID} ";
    if($INCT_ID !== false)     $query .= "AND INCT.INCT_ID     = {$INCT_ID} ";
    
    if($INAV_ID !== false && !is_array($INAV_ID)) $query .= "AND INAV.INAV_ID = {$INAV_ID} ";
    if($INAV_ID !== false && (is_array($INAV_ID) && count($INAV_ID) > 0)) $query .= "AND INAV.INAV_ID IN (". implode(', ', $INAV_ID).") ";

    if($orderby !== false){
      $orderby = explode('|', $orderby);
      $orderby = array_map( function($val){ return  substr($val, 0, 4) . "." . str_replace(':', ' ', $val); }, $orderby);
      $orderby = implode(', ',$orderby);
      $query .= "ORDER BY {$orderby}; ";

    } else {
      $query .= "ORDER BY INCR.INCR_DESCRICAO, INTP.INTP_DESCRICAO, INAT.INAT_DESCRICAO, INAV.INAV_CODIGO; ";

    }
    
    // die($query);
    return DB::select($query);
  }
}


