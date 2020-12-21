<?php
namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class AtivoRendimento extends Model {

  private $DBTables;

  protected $table;
  
  protected $primaryKey = 'INAR_ID';

  public  $timestamps = false;
  
  public function __construct()
  {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestAtivoRendimento;
  }

  public function get($get, $conditions = []) {
    $USUA_ID     = $get['usuario'];
    $data        = isset($get['data'])    ? $get['data']    : false;
    $dataDe      = isset($get['dataDe'])  ? $get['dataDe']  : false;
    $dataAte     = isset($get['dataAte']) ? $get['dataAte'] : false;
    $INAR_STATUS = isset($get['status'])  ? $get['status']  : false;
    $INAR_ID     = isset($get['INAR_ID']) ? $get['INAR_ID'] : false;
    $INAV_ID     = isset($get['INAV_ID']) ? $get['INAV_ID'] : false;
    $INAT_ID     = isset($get['INAT_ID']) ? $get['INAT_ID'] : false;
    $INTP_ID     = isset($get['INTP_ID']) ? $get['INTP_ID'] : false;
    $INCR_ID     = isset($get['INCR_ID']) ? $get['INCR_ID'] : false;
    $INCT_ID     = isset($get['INCT_ID']) ? $get['INCT_ID'] : false;
    $orderby     = isset($get['orderby']) ? $get['orderby'] : false;

    $query  = "SELECT ";
    $query .= "INAR.INAR_ID, INAR.INAR_VALOR, INAR.INAR_DATA, INAR.INAR_STATUS, INAR.INAR_TIPO,  ";
    if(isset($conditions['INAR_TIPO']) && $conditions['INAR_TIPO'] == 'FULL') {
      $query .= "CASE
                  WHEN INAR.INAR_TIPO = 'R' THEN 'Rendimento'
                  WHEN INAR.INAR_TIPO = 'D' THEN 'Dividendo'
                  WHEN INAR.INAR_TIPO = 'J' THEN 'Juros Sobre Capital'
                END INAR_TIPO, ";
    }else{
      $query .= "INAR.INAR_TIPO, ";
    }
    $query .= "INAV.INAV_ID, INAV.INAV_DESCRICAO, INAV.INAV_CODIGO, INAV_CPNJ, INAV_SITE, INAV_LIQUIDEZ, INAV_VENC, INAV.INAV_STATUS, ";
    $query .= "INAT.INAT_ID, INAT.INAT_DESCRICAO, INAT.INAT_STATUS, ";
    $query .= "INTP.INTP_ID, INTP.INTP_DESCRICAO, INTP.INTP_STATUS, ";
    $query .= "INCR.INCR_ID, INCR.INCR_DESCRICAO, INCR.INCR_CPNJ, INCR.INCR_SITE, INCR.INCR_STATUS, ";
    $query .= "INCT.INCT_ID, INCT.INCT_DESCRICAO, INCT.INCT_PAINEL, INCT.INCT_STATUS, ";
    $query .= "USUA.USUA_ID, USUA.USUA_NOME ";
    $query .= "FROM       {$this->DBTables->InvestAtivoRendimento} INAR ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivo}           INAV ON INAV.INAV_ID = INAR.INAV_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivoTipo}       INAT ON INAT.INAT_ID = INAV.INAT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestTipo}            INTP ON INTP.INTP_ID = INAT.INTP_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCorretora}       INCR ON INCR.INCR_ID = INAR.INCR_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCarteira}        INCT ON INCT.INCT_ID = INAR.INCT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestIntegrante}      INTG ON INTG.INCT_ID = INCT.INCT_ID ";
    $query .= "INNER JOIN {$this->DBTables->usuario}               USUA ON USUA.USUA_ID = INTG.USUA_ID ";
    
    $query .= "WHERE USUA.USUA_ID = {$USUA_ID} ";

    if($dataDe !== false && $dataAte !== false && $data == false) {
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
        $DATA_FORMATE = "DATE_FORMAT(INAR.INAR_DATA, '{$FORMAT}')";
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
        $DATA_FORMATE = "DATE_FORMAT(INAR.INAR_DATA, '{$FORMAT}')";
      } 

      $query .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false && $data == false) {
      if(strlen($dataAte) == 4)  $FORMAT = '%Y';
      if(strlen($dataAte) == 7)  $FORMAT = '%Y-%m';
      if(strlen($dataAte) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INAR.INAR_DATA, '{$FORMAT}') <= '$dataAte' ";

    }else if( $data !== false) {
      if(strlen($data) == 4)  $FORMAT = '%Y';
      if(strlen($data) == 7)  $FORMAT = '%Y-%m';
      if(strlen($data) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INAR.INAR_DATA, '{$FORMAT}') = '$data' ";
    }

    if($INAR_STATUS !== false) $query .= "AND INAR.INAR_STATUS = {$INAR_STATUS} ";
    if($INAR_ID !== false)     $query .= "AND INAR.INAR_ID     = {$INAR_ID} ";
    if($INAT_ID !== false)     $query .= "AND INAT.INAT_ID     = {$INAT_ID} ";
    if($INTP_ID !== false)     $query .= "AND INTP.INTP_ID     = {$INTP_ID} ";
    if($INCR_ID !== false)     $query .= "AND INCR.INCR_ID     = {$INCR_ID} ";
    
    if($INAV_ID !== false && !is_array($INAV_ID)) $query .= "AND INAV.INAV_ID = {$INAV_ID} ";
    if($INAV_ID !== false && (is_array($INAV_ID) && count($INAV_ID) > 0)) $query .= "AND INAV.INAV_ID IN (". implode(', ', $INAV_ID).") ";

    if($INCT_ID !== false && !is_array($INCT_ID)) $query .= "AND INCT.INCT_ID = {$INCT_ID} ";
    if($INCT_ID !== false && (is_array($INCT_ID) && count($INCT_ID) > 0)) $query .= "AND INCT.INCT_ID IN (". implode(', ', $INCT_ID).") ";

    if($orderby){
      $orderby = explode('|', $orderby);
      $orderby = array_map( function($val){ return str_replace(':', ' ', $val); }, $orderby);
      $orderby = implode(', ',$orderby);
      $query .= "ORDER BY {$orderby}; ";

    } else {
      $query .= "ORDER BY INAR.INAR_DATA desc; ";

    }

    // dd(DB::select($query));
    // die($query);
    return DB::select($query);

  }

  public function rendimentoAtivoCorretora($options){
    // $usuario = $options['usuario'];
    $INCT    = isset($options['INCT_ID']) ? $options['INCT_ID'] : false;
    $INCR    = isset($options['INCR_ID']) ? $options['INCR_ID'] : false;
    $INAV    = isset($options['INAV_ID']) ? $options['INAV_ID'] : false;
    $dataAte = isset($options['dataAte']) ? $options['dataAte'] : false;
    
    if(strlen($dataAte) == 4)  $FORMAT = '%Y';
    if(strlen($dataAte) == 7)  $FORMAT = '%Y-%m';
    if(strlen($dataAte) == 10) $FORMAT = '%Y-%m-%d';

    $query  = "SELECT ";
    $query .= "INAR_TIPO, INAR_VALOR, DATE_FORMAT(INAR_DATA,'%Y-%m') INAR_DATA, INAR_STATUS, INAV_ID, INCR_ID, INCT_ID ";
    $query .= "FROM {$this->DBTables->InvestAtivoRendimento} ";
    $query .= "WHERE INAR_STATUS = 1 ";

    if($INCT !== false && !is_array($INCT)) $query .= "AND INCT_ID = {$INCT} ";
    if($INCT !== false && (is_array($INCT) && count($INCT) > 0)) $query .= "AND INCT_ID IN (". implode(', ', $INCT).") ";

    if($INCR !== false && !is_array($INCR)) $query .= "AND INCR_ID = {$INCR} ";
    if($INCR !== false && (is_array($INCR) && count($INCR) > 0)) $query .= "AND INCR_ID IN (". implode(', ', $INCR).") ";

    if($INAV !== false && !is_array($INAV)) $query .= "AND INAV_ID = {$INAV} ";
    if($INAV !== false && (is_array($INAV) && count($INAV) > 0)) $query .= "AND INAV_ID IN (". implode(', ', $INAV).") ";

    $query .= "AND DATE_FORMAT(INAR_DATA, '{$FORMAT}') <= '$dataAte' ";
    $query .= "ORDER BY INAR_DATA ";

    // die($query);
    return DB::select($query);
  }
}