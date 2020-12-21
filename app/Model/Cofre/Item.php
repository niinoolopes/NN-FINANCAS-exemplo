<?php

namespace App\Model\Cofre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Item extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'COIT_ID';

  public $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->cofreItem;
  }
  
  // --

  public function get($get) {
    $USUA_ID     = $get['usuario'];
    $data        = isset($get['data'])        ? $get['data']        : false;
    $dataDe      = isset($get['dataDe'])      ? $get['dataDe']      : false;
    $dataAte     = isset($get['dataAte'])     ? $get['dataAte']     : false;
    $dataAno     = isset($get['dataAno'])     ? $get['dataAno']     : false;
    $COIT_STATUS = isset($get['COIT_STATUS']) ? $get['COIT_STATUS'] : false;
    // $COIT_STATUS = isset($get['status'])      ? $get['status']      : false;
    $COIT_ID     = isset($get['COIT_ID'])     ? $get['COIT_ID']     : false;
    $COCT_ID     = isset($get['COCT_ID'])     ? $get['COCT_ID']     : false;
    $COIT_STATUS = isset($get['status'])      ? $get['status']      : false;
    $orderby     = isset($get['orderby'])     ? $get['orderby']     : false;

    // --

    $sql  = "SELECT ";
    $sql .= "COIT.COIT_ID, COIT.COIT_VALOR, COIT.COIT_DATA, COIT.COIT_OBS, COIT.COIT_PROPOSITO, COIT.COIT_STATUS, ";
    $sql .= "COTP.COTP_ID, COTP.COTP_DESCRICAO, COTP.COTP_STATUS, ";
    $sql .= "COCT.COCT_ID, COCT.COCT_DESCRICAO, COCT.COCT_STATUS, ";
    $sql .= "USUA.USUA_NOME ";
    $sql .= "FROM       {$this->DBTables->cofreItem}       COIT ";
    $sql .= "INNER JOIN {$this->DBTables->cofreTipo}       COTP ON COTP.COTP_ID = COIT.COTP_ID ";
    $sql .= "INNER JOIN {$this->DBTables->cofreCarteira}   COCT ON COCT.COCT_ID = COIT.COCT_ID ";
    $sql .= "INNER JOIN {$this->DBTables->cofreIntegrante} COTG ON COTG.COCT_ID = COCT.COCT_ID ";
    $sql .= "INNER JOIN {$this->DBTables->usuario}         USUA ON USUA.USUA_ID = COTG.USUA_ID ";
    $sql .= "WHERE (COTG.USUA_ID = {$USUA_ID} AND COIT.USUA_ID = {$USUA_ID}) ";
    $sql .= "AND COTP.COTP_STATUS = 1 ";
    $sql .= "AND COCT.COCT_STATUS = 1 ";

    if($COCT_ID     !== false ) $sql .= "AND COCT.COCT_ID     = {$COCT_ID} ";
    if($COIT_ID     !== false ) $sql .= "AND COIT.COIT_ID     = {$COIT_ID} ";
    if($COIT_STATUS !== false ) $sql .= "AND COIT.COIT_STATUS = {$COIT_STATUS} ";

    if($dataDe !== false && $dataAte !== false && $data == false && $dataAno == false) {
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
        $DATA_FORMATE = "DATE_FORMAT(COIT.COIT_DATA, '{$FORMAT}')";
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
        $DATA_FORMATE = "DATE_FORMAT(COIT.COIT_DATA, '{$FORMAT}')";
      } 

      $sql .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false && $data == false && $dataAno == false) {
      if(strlen($dataAte) == 4)  $FORMAT = '%Y';
      if(strlen($dataAte) == 7)  $FORMAT = '%Y-%m';
      if(strlen($dataAte) == 10) $FORMAT = '%Y-%m-%d';

      $sql .= "AND DATE_FORMAT(COIT.COIT_DATA, '{$FORMAT}') <= '$dataAte' ";

    }else if( $data !== false && $dataAno == false) {
      if(strlen($data) == 4)  $FORMAT = '%Y';
      if(strlen($data) == 7)  $FORMAT = '%Y-%m';
      if(strlen($data) == 10) $FORMAT = '%Y-%m-%d';

      $sql .= "AND DATE_FORMAT(COIT.COIT_DATA, '{$FORMAT}') = '$data' ";

    }else if( $dataAno !== false) {
      $dataAno = substr($dataAno, 0, 4);

      $sql .= "AND DATE_FORMAT(COIT.COIT_DATA, '%Y') = '$dataAno' ";
    }

    if($orderby !== false){
      $orderby = explode('|', $orderby);
      $orderby = array_map( function($val){ return  substr($val, 0, 4) . "." . str_replace(':', ' ', $val); }, $orderby);
      $orderby = implode(', ',$orderby);
      $sql .= "ORDER BY {$orderby}; ";

    } else {
      $sql .= "ORDER BY COIT.COIT_DATA DESC, COIT.COCT_ID ASC, COIT.COIT_PROPOSITO;";

    }
    
    // die($sql);
    return DB::select($sql);
  }

  
  public function consolidado($get) {
    $USUA_ID     = $get['usuario'];
    $COCT_ID     = $get['COCT_ID'];
    $COIT_STATUS = isset($get['COIT_STATUS']) ? $get['COIT_STATUS'] : false;

    // --

    $sql  = "SELECT ";
    $sql .= "COIT.COIT_PROPOSITO, ";
    $sql .= "sum(COIT.COIT_VALOR) COIT_SOMA, ";
    $sql .= "0 COIT_PERCENTUAL, ";
    $sql .= "COTP.COTP_ID ";
    $sql .= "FROM       {$this->DBTables->cofreItem}       COIT ";
    $sql .= "INNER JOIN {$this->DBTables->cofreTipo}       COTP ON COTP.COTP_ID = COIT.COTP_ID ";
    $sql .= "INNER JOIN {$this->DBTables->cofreCarteira}   COCT ON COCT.COCT_ID = COIT.COCT_ID ";
    $sql .= "INNER JOIN {$this->DBTables->cofreIntegrante} COTG ON COTG.COCT_ID = COCT.COCT_ID ";
    $sql .= "INNER JOIN {$this->DBTables->usuario}         USUA ON USUA.USUA_ID = COTG.USUA_ID ";
    $sql .= "WHERE (COTG.USUA_ID = {$USUA_ID} AND COIT.USUA_ID = {$USUA_ID}) ";
    $sql .= "AND COTP.COTP_STATUS = 1 ";
    $sql .= "AND COCT.COCT_STATUS = 1 ";
    $sql .= "AND COCT.COCT_ID = {$COCT_ID} ";

    if($COIT_STATUS !== false) $sql .= "AND COIT.COIT_STATUS = {$COIT_STATUS} ";

    $sql .= "GROUP BY COIT.COIT_PROPOSITO, COIT.COTP_ID ";
    $sql .= "ORDER BY COIT.COIT_PROPOSITO ASC;";
    // die($sql);
    return DB::select($sql);
  }

  // public function get($get) {
  //   $USUA_ID     = $get['usuario'];
  //   $COIT_STATUS = isset($get['status'])  ? $get['status']  : false;
  //   $COIT_DATA   = isset($get['mes'])     ? $get['mes']     : false;
  //   $COIT_ID     = isset($get['COIT_ID']) ? $get['COIT_ID'] : false;
  //   $COCT_ID     = isset($get['COCT_ID']) ? $get['COCT_ID'] : false;
  //   $COIT_STATUS = isset($get['status'])  ? $get['status']  : false;
  //   $dataAno     = isset($get['dataAno']) ? $get['dataAno'] : false;

  //   // --
    
  //   $TAB_ITEM       = "{$this->DBTables->cofreItem}       COIT";
  //   $TAB_TIPO       = "{$this->DBTables->cofreTipo}       COTP";
  //   $TAB_CARTEIRA   = "{$this->DBTables->cofreCarteira}   COCT";
  //   $TAB_INTEGRANTE = "{$this->DBTables->cofreIntegrante} COTG";
  //   $TAB_USUARIO    = "{$this->DBTables->usuario}         USUA";

  //   $sql  = "SELECT ";
  //   $sql .= "COIT.COIT_ID, COIT.COIT_VALOR, COIT.COIT_DATA, COIT.COIT_OBS, COIT.COIT_PROPOSITO, COIT.COIT_STATUS, ";
  //   $sql .= "COTP.COTP_ID, COTP.COTP_DESCRICAO, COTP.COTP_STATUS, ";
  //   $sql .= "COCT.COCT_ID, COCT.COCT_DESCRICAO, COCT.COCT_STATUS, ";
  //   $sql .= "USUA.USUA_NOME ";
  //   $sql .= "FROM {$TAB_ITEM} ";
  //   $sql .= "INNER JOIN $TAB_TIPO       ON COTP.COTP_ID = COIT.COTP_ID ";
  //   $sql .= "INNER JOIN $TAB_CARTEIRA   ON COCT.COCT_ID = COIT.COCT_ID ";
  //   $sql .= "INNER JOIN $TAB_INTEGRANTE ON COTG.COCT_ID = COCT.COCT_ID ";
  //   $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = COTG.USUA_ID ";
  //   $sql .= "WHERE (COTG.USUA_ID = {$USUA_ID} AND COIT.USUA_ID = {$USUA_ID}) ";
  //   $sql .= "AND COTP.COTP_STATUS = 1 ";
  //   $sql .= "AND COCT.COCT_STATUS = 1 ";

  //   if( $COCT_ID )     $sql .= "AND COCT.COCT_ID     = {$COCT_ID} ";
  //   if( $COIT_ID )     $sql .= "AND COIT.COIT_ID     = {$COIT_ID} ";
  //   if( $COIT_STATUS ) $sql .= "AND COIT.COIT_STATUS = {$COIT_STATUS} ";
  //   if( $COIT_STATUS ) $sql .= "AND COIT.COIT_STATUS = {$COIT_STATUS} ";
  //   if( $COIT_STATUS ) $sql .= "AND COIT.COIT_STATUS = {$COIT_STATUS} ";

  //   if( $COIT_DATA ) {

  //     if(strlen($COIT_DATA) == 4)  $FORMAT = '%Y';
  //     if(strlen($COIT_DATA) == 7)  $FORMAT = '%Y-%m';
  //     if(strlen($COIT_DATA) == 10) $FORMAT = '%Y-%m-%d';

  //     $sql .= "AND DATE_FORMAT(COIT.COIT_DATA, '{$FORMAT}') = '$COIT_DATA' ";
  //   }

  //   if($dataAno) {
  //     $sql .= "AND DATE_FORMAT(COIT.COIT_DATA, '%Y') = '$dataAno' ";
  //   }

  //   $sql .= "ORDER BY COIT.COIT_DATA, COIT.COIT_PROPOSITO;";
  //   return DB::select($sql);
  // }
}


