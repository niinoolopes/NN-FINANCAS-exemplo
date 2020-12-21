<?php

namespace App\Model\Financa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Item extends Model {

  private $DBTables;

  protected $table;

  protected $primaryKey = 'FNIT_ID';

  public $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->financaItem;
  }

  // --

  public function get($get)
  {
    $USUA_ID     = $get['usuario'];
    $data        = isset($get['data'])        ? $get['data']        : false;
    $dataDe      = isset($get['dataDe'])      ? $get['dataDe']      : false;
    $dataAte     = isset($get['dataAte'])     ? $get['dataAte']     : false;
    $dataAno     = isset($get['dataAno'])     ? $get['dataAno']     : false;
    $FNIT_STATUS = isset($get['FNIT_STATUS']) ? $get['FNIT_STATUS'] : false;
    $FNIT_ID     = isset($get['FNIT_ID'])     ? $get['FNIT_ID']     : false;
    $FNIS_ID     = isset($get['FNIS_ID'])     ? $get['FNIS_ID']     : false;
    $FITP_ID     = isset($get['FITP_ID'])     ? $get['FITP_ID']     : false;
    $FIGP_ID     = isset($get['FIGP_ID'])     ? $get['FIGP_ID']     : false;
    $FICT_ID     = isset($get['FICT_ID'])     ? $get['FICT_ID']     : false;
    $FINC_ID     = isset($get['FINC_ID'])     ? $get['FINC_ID']     : false;
    $orderby     = isset($get['orderby'])     ? $get['orderby']     : false;

    // --

    $sql  = "SELECT ";
    $sql .= "FNIT.FNIT_ID, FNIT.FNIT_STATUS, FNIT.FNIT_VALOR, FNIT.FNIT_DATA, FNIT.FNIT_OBS, ";
    $sql .= "FNIS.FNIS_ID, FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_ID, FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_ID, FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_ID, FICT.FICT_DESCRICAO, ";
    $sql .= "FINC.FINC_ID, FINC.FINC_DESCRICAO, ";
    $sql .= "USUA.USUA_ID, USUA.USUA_NOME ";
    $sql .= "FROM       {$this->DBTables->financaItem}       FNIT ";
    $sql .= "INNER JOIN {$this->DBTables->financaSituacao}   FNIS ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaTipo}       FITP ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaGrupo}      FIGP ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaCategoria}  FICT ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN {$this->DBTables->usuario}           USUA ON USUA.USUA_ID = FNIT.USUA_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaCarteira}   FINC ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaIntegrante} FITG ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNIT.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";

    if($FNIT_STATUS !== false) $sql .= "AND FNIT.FNIT_STATUS = {$FNIT_STATUS} ";
    if($FNIT_ID     !== false) $sql .= "AND FNIT.FNIT_ID     = {$FNIT_ID}     ";
    if($FNIS_ID     !== false) $sql .= "AND FNIS.FNIS_ID     = {$FNIS_ID}     ";
    if($FITP_ID     !== false) $sql .= "AND FITP.FITP_ID     = {$FITP_ID}     ";
    if($FIGP_ID     !== false) $sql .= "AND FIGP.FIGP_ID     = {$FIGP_ID}     ";
    if($FICT_ID     !== false) $sql .= "AND FICT.FICT_ID     = {$FICT_ID}     ";
    if($FINC_ID     !== false) $sql .= "AND FINC.FINC_ID     = {$FINC_ID}     ";

    if($dataDe !== false && $dataAte !== false && $data == false && $dataAno == false) {
      if(strlen($dataDe) == 4) {
        $FORMAT = '%Y';
      } 
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
      } 
      $DATA_FORMATE = "DATE_FORMAT(FNIT.FNIT_DATA, '{$FORMAT}')";

      $sql .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false && $data == false && $dataAno == false) {
      if(strlen($dataAte) == 4)  $FORMAT = '%Y';
      if(strlen($dataAte) == 7)  $FORMAT = '%Y-%m';
      if(strlen($dataAte) == 10) $FORMAT = '%Y-%m-%d';

      $sql .= "AND DATE_FORMAT(FNIT.FNIT_DATA, '{$FORMAT}') <= '$dataAte' ";

    }else if( $data !== false && $dataAno == false) {
      if(strlen($data) == 4)  $FORMAT = '%Y';
      if(strlen($data) == 7)  $FORMAT = '%Y-%m';
      if(strlen($data) == 10) $FORMAT = '%Y-%m-%d';

      $sql .= "AND DATE_FORMAT(FNIT.FNIT_DATA, '{$FORMAT}') = '$data' ";

    }else if( $dataAno !== false) {
      $dataAno = substr($dataAno, 0, 4);

      $sql .= "AND DATE_FORMAT(FNIT.FNIT_DATA, '%Y') = '$dataAno' ";
    }

    if($orderby !== false){
      $orderby = explode('|', $orderby);
      $orderby = array_map( function($val){ return  substr($val, 0, 4) . "." . str_replace(':', ' ', $val); }, $orderby);
      $orderby = implode(', ',$orderby);
      $sql .= "ORDER BY {$orderby}; ";

    } else {
      $sql .= "ORDER BY FNIT.FNIT_DATA DESC, FNIT.FNIT_ID DESC;";

    }

    // die($sql);
    return DB::select($sql);
  }
}


