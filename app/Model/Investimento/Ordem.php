<?php

namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Ordem extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'INOD_ID';

  public  $timestamps = false;

  // --

  public function __construct()
  {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestOrdem;
  }

  public function get($get) {
    $USUA_ID     = $get['usuario'];
    $data        = isset($get['data'])        ? $get['data']        : false;
    $dataDe      = isset($get['dataDe'])      ? $get['dataDe']      : false;
    $dataAte     = isset($get['dataAte'])     ? $get['dataAte']     : false;
    $INOD_ID     = isset($get['INOD_ID'])     ? $get['INOD_ID']     : false;
    $INOD_STATUS = isset($get['INOD_STATUS']) ? $get['INOD_STATUS'] : false;
    $INCR_ID     = isset($get['INCR_ID'])     ? $get['INCR_ID']     : false;
    $INTG_ID     = isset($get['INTG_ID'])     ? $get['INTG_ID']     : false;
    $limit       = isset($get['limit'])       ? $get['limit']       : false;
    $orderby     = isset($get['orderby'])     ? $get['orderby']     : false;

    // --

    $query  = "SELECT ";
    $query .= "INOD.*, ";
    $query .= "INTX.*, ";
    $query .= "INCR.INCR_DESCRICAO, ";
    $query .= "INCR.INCR_CPNJ, ";
    $query .= "INCR.INCR_SITE, ";
    $query .= "INCR.INCR_STATUS, ";
    $query .= "INCT.INCT_DESCRICAO, ";
    $query .= "INCT.INCT_PAINEL, ";
    $query .= "INCT.INCT_STATUS, "; 
    $query .= "USUA.USUA_NOME ";

    $query .= "FROM       {$this->DBTables->InvestOrdem}      INOD ";
    $query .= "INNER JOIN {$this->DBTables->InvestTaxas}      INTX ON INTX.INTX_ID = INOD.INTX_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCorretora}  INCR ON INCR.INCR_ID = INOD.INCR_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCarteira}   INCT ON INCT.INCT_ID = INOD.INCT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestIntegrante} INTG ON INTG.INCT_ID = INCT.INCT_ID ";
    $query .= "INNER JOIN {$this->DBTables->usuario}          USUA ON USUA.USUA_ID = INTG.USUA_ID ";
    $query .= "WHERE USUA.USUA_ID = {$USUA_ID} ";

    if($INOD_STATUS !== false) $query .= "AND INOD.INOD_STATUS = {$INOD_STATUS} ";
    if($INOD_ID     !== false) $query .= "AND INOD.INOD_ID = {$INOD_ID} ";
    if($INCR_ID     !== false) $query .= "AND INCR.INCR_ID = {$INCR_ID} ";
    if($INTG_ID     !== false) $query .= "AND INTG.INTG_ID = {$INTG_ID} ";

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

    }else if($data !== false) {
      if(strlen($data) == 4)  $FORMAT = '%Y';
      if(strlen($data) == 7)  $FORMAT = '%Y-%m';
      if(strlen($data) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INOD.INOD_DATA, '{$FORMAT}') = '$data' ";
    }
    
    if($orderby !== false){
      $orderby = explode('|', $orderby);
      $orderby = array_map( function($val){ return str_replace(':', ' ', $val); }, $orderby);
      $orderby = implode(', ',$orderby);
      $query .= "ORDER BY {$orderby} ";

    } else {
      $query .= "ORDER BY INOD.INOD_DATA desc, INOD.INOD_ID ";
    }
    
    if($limit) $query .= "limit {$limit} ";

    // die($query);
    return DB::select($query);
  }
}


