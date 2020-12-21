<?php

namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class AtivoCotacao extends Model {

  private $DBTables;

  protected $table;
  
  protected $primaryKey = 'INAC_ID';

  public  $timestamps = false;
  
  // --
  
  public function __construct()
  {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestAtivoCotacao;
  }

  public function get($get, $options = []) {
    $USUA_ID     = $get['usuario'];
    $data        = isset($get['data'])        ? $get['data']        : false;
    $dataDe      = isset($get['dataDe'])      ? $get['dataDe']      : false;
    $dataAte     = isset($get['dataAte'])     ? $get['dataAte']     : false;
    $INAC_STATUS = isset($get['INAC_STATUS']) ? $get['INAC_STATUS'] : false;
    $INAC_ID     = isset($get['INAC_ID'])     ? $get['INAC_ID']     : false;
    $INAV_ID     = isset($get['INAV_ID'])     ? $get['INAV_ID']     : false;
    $orderby     = isset($get['orderby'])     ? $get['orderby']     : false;

    // --
    
    $query  ="SELECT ";
    if( !isset($options['SELECT']) ) {
      $query .="INAC.INAC_ID, INAC.INAC_VALOR, INAC.INAC_DATA, INAC.INAC_STATUS, ";
      $query .="INAV.INAV_ID, INAV.INAV_DESCRICAO, INAV.INAV_CODIGO, INAV_CPNJ, INAV_SITE, INAV_LIQUIDEZ, INAV_VENC, INAV.INAV_STATUS, ";
      $query .="INAT.INAT_ID, INAT.INAT_DESCRICAO, INAT.INAT_STATUS, ";
      $query .="INTP.INTP_ID, INTP.INTP_DESCRICAO, INTP.INTP_STATUS, ";
      $query .="USUA.USUA_ID, USUA.USUA_NOME ";
      
    } else if( isset($options['SELECT']) && $options['SELECT'] == 'INAC') {
      $query .="INAC.INAC_ID, INAC.INAC_VALOR, INAC.INAC_DATA, INAC.INAC_STATUS, ";
      $query .="INAV.INAV_ID, ";
      $query .="INAT.INAT_ID, ";
      $query .="INTP.INTP_ID, ";
      $query .="USUA.USUA_ID ";

    }
    $query .="FROM {$this->DBTables->InvestAtivoCotacao}      INAC ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivo}      INAV ON INAV.INAV_ID = INAC.INAV_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivoTipo}  INAT ON INAT.INAT_ID = INAV.INAT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestTipo}       INTP ON INTP.INTP_ID = INAT.INTP_ID ";
    $query .= "INNER JOIN {$this->DBTables->usuario}          USUA ON USUA.USUA_ID = INAC.USUA_ID ";
    $query .="WHERE USUA.USUA_ID = {$USUA_ID} ";

    if($dataDe !== false && $dataAte !== false && $data == false) {
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
        $DATA_FORMATE = "DATE_FORMAT(INAC.INAC_DATA, '{$FORMAT}')";
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
        $DATA_FORMATE = "DATE_FORMAT(INAC.INAC_DATA, '{$FORMAT}')";
      } 

      $query .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false && $data == false){
      if(strlen($dataAte) == 4)  $FORMAT = '%Y';
      if(strlen($dataAte) == 7)  $FORMAT = '%Y-%m';
      if(strlen($dataAte) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INAC.INAC_DATA, '{$FORMAT}') <= '$dataAte' ";

    }else if($data !== false) {
      if(strlen($data) == 4)  $FORMAT = '%Y';
      if(strlen($data) == 7)  $FORMAT = '%Y-%m';
      if(strlen($data) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INAC.INAC_DATA, '{$FORMAT}') = '$data' ";
    }

    if($INAC_ID     !== false) $query .="AND INAC.INAC_ID     = {$INAC_ID} ";
    if($INAC_STATUS !== false) $query .="AND INAC.INAC_STATUS = {$INAC_STATUS} ";
    if($INAV_ID     !== false && !is_array($INAV_ID)) $query .="AND INAV.INAV_ID = {$INAV_ID} ";
    if($INAV_ID     !== false && (is_array($INAV_ID) && count($INAV_ID) > 0) ) $query .="AND INAV.INAV_ID IN (" . implode(',', $INAV_ID) . ") ";
    
    if($orderby){
      $orderby = explode('|', $orderby);
      $orderby = array_map( function($val){ return  substr($val, 0, 4) . "." . str_replace(':', ' ', $val); }, $orderby);
      $orderby = implode(', ',$orderby);
      $query .= "ORDER BY {$orderby}; ";

    } else {
      $query .= "ORDER BY INAC.INAC_DATA DESC ";

    }

    $query .=";";
    // die($query);
    return DB::select($query);
  }
}