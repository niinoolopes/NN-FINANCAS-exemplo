<?php

namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class CarteiraItem extends Model {

  private $DBTables;

  protected $table;
  
  protected $primaryKey = 'INCTC_ID';

  public  $timestamps = false;
  
  // --

  public function __construct() 
  {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestCarteiraConsolidado;
  }

  public function get($get) {
    $USUA_ID     = $get['usuario'];
    $INCT_ID     = $get['INCT_ID'];
    $INAV_ID     = isset($get['INAV_ID']) ? $get['INAV_ID'] : false;
    $data        = isset($get['data'])    ? $get['data']    : false;
    $dataDe      = isset($get['dataDe'])  ? $get['dataDe']  : false;
    $dataAte     = isset($get['dataAte']) ? $get['dataAte'] : false;
    $dataAno     = isset($get['dataAno']) ? $get['dataAno'] : false;

    // --
    
    $query  = "SELECT ";
    $query .= "INCTC.INCTC_ID, INCTC.INCTC_DATA, INCTC.INCTC_CONTENT, ";
    $query .= "INAV.INAV_ID, INAV.INAV_DESCRICAO, INAV.INAV_STATUS, INAV.INAV_CODIGO, INAV.INAV_CPNJ, INAV.INAV_SITE, INAV.INAV_LIQUIDEZ, INAV.INAV_VENC, ";
    $query .= "INAT.INAT_ID, INAT.INAT_DESCRICAO, INAT.INAT_STATUS, ";
    $query .= "INTP.INTP_ID, INTP.INTP_DESCRICAO, INTP.INTP_STATUS, ";
    $query .= "INCR.INCR_ID, INCR.INCR_DESCRICAO, INCR.INCR_STATUS, ";
    $query .= "INCT.INCT_ID, INCT.INCT_DESCRICAO, INCT.INCT_STATUS, INCT.INCT_PAINEL, ";
    $query .= "USUA.USUA_ID, USUA.USUA_NOME ";

    $query .= "FROM {$this->DBTables->InvestCarteiraConsolidado} INCTC ";

    $query .= "INNER JOIN {$this->DBTables->InvestAtivo}      INAV ON INAV.INAV_ID = INCTC.INAV_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivoTipo}  INAT ON INAT.INAT_ID = INAV.INAT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestTipo}       INTP ON INTP.INTP_ID = INAT.INTP_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCorretora}  INCR ON INCR.INCR_ID = INCTC.INCR_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCarteira}   INCT ON INCT.INCT_ID = INCTC.INCT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestIntegrante} INTG ON INCT.INCT_ID = INTG.INCT_ID ";
    $query .= "INNER JOIN {$this->DBTables->usuario}          USUA ON USUA.USUA_ID = INTG.USUA_ID ";

    $query .= "WHERE USUA.USUA_ID = {$USUA_ID} ";
    $query .= "  AND INCT.INCT_ID = {$INCT_ID} ";
    
    if($dataDe !== false && $dataAte !== false && $data == false) {
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
        $DATA_FORMATE = "DATE_FORMAT(INCTC.INCTC_DATA, '{$FORMAT}')";
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
        $DATA_FORMATE = "DATE_FORMAT(INCTC.INCTC_DATA, '{$FORMAT}')";
      } 

      $query .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false && $data == false) {
      if(strlen($dataAte) == 4)  $FORMAT = '%Y';
      if(strlen($dataAte) == 7)  $FORMAT = '%Y-%m';
      if(strlen($dataAte) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INCTC.INCTC_DATA, '{$FORMAT}') <= '$dataAte' ";

    }else if( $dataAno !== false) {
      $dataAno = substr($dataAno, 0, 4);
  
      $query .= "AND DATE_FORMAT(INCTC.INCTC_DATA, '%Y') = '$dataAno' ";
    }else if( $data !== false) {
      if(strlen($data) == 4)  $FORMAT = '%Y';
      if(strlen($data) == 7)  $FORMAT = '%Y-%m';
      if(strlen($data) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INCTC.INCTC_DATA, '{$FORMAT}') = '$data' ";
    }

    if($INAV_ID !== false && !is_array($INAV_ID)) $query .= "AND INCTC.INAV_ID = {$INAV_ID} ";
    if($INAV_ID !== false && (is_array($INAV_ID) && count($INAV_ID) > 0)) $query .= "AND INCTC.INAV_ID IN (". implode(', ', $INAV_ID).") ";

    $query .= "ORDER BY INCTC.INCTC_DATA ";
    $query .= ";";

    // die($query);
    return DB::select($query);
  }
  
  public function dataInicial($options)
  {
    $usuario     = $options['usuario'];
    $INCT_ID     = isset($options['INCT_ID'])     ? $options['INCT_ID']     : false;
    $INAV_ID     = isset($options['INAV_ID'])     ? $options['INAV_ID']     : false;
    $dataDe      = isset($options['dataDe'])     ? $options['dataDe']     : false;

    $query  = "SELECT ";
    $query .= "INCT.INCT_ID, INCR.INCR_ID, INAV.INAV_ID, MIN(INOD.INOD_DATA) INOD_DATA ";
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
    $query .= "AND INCT.INCT_ID     = {$INCT_ID} ";
    
    if($dataDe !== false){
      if(strlen($dataDe) == 4)  $FORMAT = '%Y';
      if(strlen($dataDe) == 7)  $FORMAT = '%Y-%m';
      if(strlen($dataDe) == 10) $FORMAT = '%Y-%m-%d';

      $query .= "AND DATE_FORMAT(INOD.INOD_DATA, '{$FORMAT}') >= '$dataDe' ";
    }

    if($INAV_ID !== false && !is_array($INAV_ID)) 
      $query .= "AND INAV.INAV_ID = {$INAV_ID} ";

    if($INAV_ID !== false && (is_array($INAV_ID) && count($INAV_ID) > 0)) 
      $query .= "AND INAV.INAV_ID IN (". implode(', ', $INAV_ID).") ";

    $query .= "GROUP BY INCT.INCT_ID, INCR.INCR_ID, INAV.INAV_ID ";
    $query .= "; ";

    // die($query);
    return DB::select($query);
  }
}