<?php

namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class AtivoSplit extends Model {

  private $DBTables;

  protected $table;
  
  protected $primaryKey = 'INAS_ID';

  public  $timestamps = false;
  
  // --
  
  public function __construct()
  {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestAtivoSplit;
  }

  public function get($get) {
    $USUA_ID     = $get['usuario'];
    $data        = isset($get['data'])        ? $get['data']        : false;
    $dataDe      = isset($get['dataDe'])      ? $get['dataDe']      : false;
    $dataAte     = isset($get['dataAte'])     ? $get['dataAte']     : false;
    $INAS_STATUS = isset($get['INAS_STATUS']) ? $get['INAS_STATUS'] : false;
    $INAS_ID     = isset($get['INAS_ID'])     ? $get['INAS_ID']     : false;
    $INAS_TIPO   = isset($get['INAS_TIPO'])   ? $get['INAS_TIPO']   : false;
    $INAV_ID     = isset($get['INAV_ID'])     ? $get['INAV_ID']     : false;
    $orderby     = isset($get['orderby'])     ? $get['orderby']     : false;

    // --

    $query  ="SELECT ";
    $query .="INAS.INAS_ID, INAS.INAS_TIPO, INAS.INAS_QUANTIDADE, INAS.INAS_DATA, INAS.INAS_STATUS, ";
    $query .="INAV.INAV_ID, INAV.INAV_DESCRICAO, INAV.INAV_CODIGO, INAV_CPNJ, INAV_SITE, INAV_LIQUIDEZ, INAV_VENC, INAV.INAV_STATUS, ";
    $query .="INAT.INAT_ID, INAT.INAT_DESCRICAO, INAT.INAT_STATUS, ";
    $query .="INTP.INTP_ID, INTP.INTP_DESCRICAO, INTP.INTP_STATUS, ";
    $query .="USUA.USUA_ID, USUA.USUA_NOME ";
    $query .="FROM {$this->DBTables->InvestAtivoSplit}        INAS ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivo}      INAV ON INAV.INAV_ID = INAS.INAV_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivoTipo}  INAT ON INAT.INAT_ID = INAV.INAT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestTipo}       INTP ON INTP.INTP_ID = INAT.INTP_ID ";
    $query .= "INNER JOIN {$this->DBTables->usuario}          USUA ON USUA.USUA_ID = INAS.USUA_ID ";
    $query .="WHERE USUA.USUA_ID = {$USUA_ID} ";

    if($dataDe !== false && $dataAte !== false && $data == false) {
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
        $DATA_FORMATE = "DATE_FORMAT(INAS.INAS_DATA, '{$FORMAT}')";
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
        $DATA_FORMATE = "DATE_FORMAT(INAS.INAS_DATA, '{$FORMAT}')";
      } 

      $query .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false && $data == false) {
      if(strlen($dataAte) == 4)  $FORMAT = '%Y';
      if(strlen($dataAte) == 7)  $FORMAT = '%Y-%m';
      if(strlen($dataAte) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INAS.INAS_DATA, '{$FORMAT}') <= '$dataAte' ";

    }else if( $data !== false) {
      if(strlen($data) == 4)  $FORMAT = '%Y';
      if(strlen($data) == 7)  $FORMAT = '%Y-%m';
      if(strlen($data) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INAS.INAS_DATA, '{$FORMAT}') = '$data' ";
    }

    if($INAS_ID     !== false ) $query .="AND INAS.INAS_ID     = {$INAS_ID} ";
    if($INAS_TIPO   !== false ) $query .="AND INAS.INAS_TIPO   = '{$INAS_TIPO}' ";
    if($INAS_STATUS !== false ) $query .="AND INAS.INAS_STATUS = {$INAS_STATUS} ";
    if($INAV_ID !== false && !is_array($INAV_ID)) $query .="AND INAV.INAV_ID = {$INAV_ID} ";
    if($INAV_ID !== false && (is_array($INAV_ID) && count($INAV_ID) > 0)) $query .="AND INAV.INAV_ID IN (" . implode(',', $INAV_ID) . ") ";


    if($orderby){
      $orderby = explode('|', $orderby);
      $orderby = array_map( function($val){ return  substr($val, 0, 4) . "." . str_replace(':', ' ', $val); }, $orderby);
      $orderby = implode(', ',$orderby);
      $query .= "ORDER BY {$orderby}; ";

    } else {
      $query .= "ORDER BY INAS.INAS_DATA DESC ";

    }

    $query .=";";
    // die($query);
    return DB::select($query);
  }
}