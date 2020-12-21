<?php

namespace App\Model\Financa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class ListaFixa extends Model {

  private $DBTables;

  protected $table;

  protected $primaryKey = 'FNLF_ID';

  public $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->financaListaFixa;
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
    $FNLF_ID     = isset($get['FNLF_ID'])     ? $get['FNLF_ID']     : false;
    $FNIT_ID     = isset($get['FNIT_ID'])     ? $get['FNIT_ID']     : false;
    $FNIS_ID     = isset($get['FNIS_ID'])     ? $get['FNIS_ID']     : false;
    $FITP_ID     = isset($get['FITP_ID'])     ? $get['FITP_ID']     : false;
    $FIGP_ID     = isset($get['FIGP_ID'])     ? $get['FIGP_ID']     : false;
    $FICT_ID     = isset($get['FICT_ID'])     ? $get['FICT_ID']     : false;
    $FINC_ID     = isset($get['FINC_ID'])     ? $get['FINC_ID']     : false;
    $orderby     = isset($get['orderby'])     ? $get['orderby']     : false;

    // --
    
    $sql  = "SELECT ";
    $sql .= "FNLF.FNLF_ID, FNLF.FNIT_STATUS, FNLF.FNIT_VALOR, FNLF.FNIT_DATA, FNLF.FNIT_OBS, ";
    $sql .= "FNIS.FNIS_ID, FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_ID, FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_ID, FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_ID, FICT.FICT_DESCRICAO, ";
    $sql .= "FINC.FINC_ID, FINC.FINC_DESCRICAO, ";
    $sql .= "USUA.USUA_ID, USUA.USUA_NOME ";
    $sql .= "FROM       {$this->DBTables->financaListaFixa}  FNLF ";
    $sql .= "INNER JOIN {$this->DBTables->financaSituacao}   FNIS ON FNIS.FNIS_ID = FNLF.FNIS_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaTipo}       FITP ON FITP.FITP_ID = FNLF.FITP_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaGrupo}      FIGP ON FIGP.FIGP_ID = FNLF.FIGP_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaCategoria}  FICT ON FICT.FICT_ID = FNLF.FICT_ID ";
    $sql .= "INNER JOIN {$this->DBTables->usuario}           USUA ON USUA.USUA_ID = FNLF.USUA_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaCarteira}   FINC ON FINC.FINC_ID = FNLF.FINC_ID ";
    $sql .= "INNER JOIN {$this->DBTables->financaIntegrante} FITG ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    // $sql .= "INNER JOIN {$TAB_USUARIO}    ON USUA.USUA_ID = FNLF.USUA_ID ";
    // $sql .= "INNER JOIN {$TAB_INTEGRANTE} ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNLF.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";

    if( $FNIT_STATUS !== false) $sql .= "AND FNLF.FNIT_STATUS = {$FNIT_STATUS} ";
    if( $FNLF_ID     !== false) $sql .= "AND FNLF.FNLF_ID     = {$FNLF_ID} ";
    if( $FNIT_ID     !== false) $sql .= "AND FNLF.FNIT_ID     = {$FNIT_ID} ";
    if( $FNIS_ID     !== false) $sql .= "AND FNIS.FNIS_ID     = {$FNIS_ID} ";
    if( $FITP_ID     !== false) $sql .= "AND FITP.FITP_ID     = {$FITP_ID} ";
    if( $FIGP_ID     !== false) $sql .= "AND FIGP.FIGP_ID     = {$FIGP_ID} ";
    if( $FICT_ID     !== false) $sql .= "AND FICT.FICT_ID     = {$FICT_ID} ";
    if( $FINC_ID     !== false) $sql .= "AND FINC.FINC_ID     = {$FINC_ID} ";

    if($dataDe !== false && $dataAte !== false && $data == false && $dataAno == false) {
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
        $DATA_FORMATE = "DATE_FORMAT(FNLF.FNIT_DATA, '{$FORMAT}')";
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
        $DATA_FORMATE = "DATE_FORMAT(FNLF.FNIT_DATA, '{$FORMAT}')";
      } 

      $sql .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false && $data == false && $dataAno == false) {
      if(strlen($dataAte) == 4)  $FORMAT = '%Y';
      if(strlen($dataAte) == 7)  $FORMAT = '%Y-%m';
      if(strlen($dataAte) == 10) $FORMAT = '%Y-%m-%d';

      $sql .= "AND DATE_FORMAT(FNLF.FNIT_DATA, '{$FORMAT}') <= '$dataAte' ";

    }else if( $data !== false && $dataAno == false) {
      if(strlen($data) == 4)  $FORMAT = '%Y';
      if(strlen($data) == 7)  $FORMAT = '%Y-%m';
      if(strlen($data) == 10) $FORMAT = '%Y-%m-%d';

      $sql .= "AND DATE_FORMAT(FNLF.FNIT_DATA, '{$FORMAT}') = '$data' ";

    }else if( $dataAno !== false) {
      $dataAno = substr($dataAno, 0, 4);

      $sql .= "AND DATE_FORMAT(FNLF.FNIT_DATA, '%Y') = '$dataAno' ";
    }

    if($orderby !== false){
      $orderby = explode('|', $orderby);
      $orderby = array_map( function($val){ return  substr($val, 0, 4) . "." . str_replace(':', ' ', $val); }, $orderby);
      $orderby = implode(', ',$orderby);
      $sql .= "ORDER BY {$orderby}  ";

    } else {
      $sql .= "ORDER BY FNLF.FNIT_DATA DESC, FNLF.FNLF_ID DESC ";

    }
    
    // die($sql);
    return DB::select($sql);
  }
}


