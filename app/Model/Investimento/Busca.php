<?php
namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Busca extends Model {

  // private $DBTables;

  // protected $table;
  
  // protected $primaryKey = 'INCT_ID';

  // public  $timestamps = false;
  
  // --

  public function __construct() {
    // $this->DBTables = new DBTables;
    // $this->table    = $this->DBTables->InvestCarteira;
  }

  public function items($get){
    $usuario     = $get['usuario'];
    $data        = isset($get['data'])    ? $get['data']    : false;
    $dataDe      = isset($get['dataDe'])  ? $get['dataDe']  : false;
    $dataAte     = isset($get['dataAte']) ? $get['dataAte'] : false;
    $INIT_STATUS = isset($get['status'])  ? $get['status']  : false;
    $INIT_ID     = isset($get['INIT_ID']) ? $get['INIT_ID'] : false;
    $INAV_ID     = isset($get['INAV_ID']) ? $get['INAV_ID'] : false;
    $INAT_ID     = isset($get['INAT_ID']) ? $get['INAT_ID'] : false;
    $INTP_ID     = isset($get['INTP_ID']) ? $get['INTP_ID'] : false;
    $INOD_ID     = isset($get['INOD_ID']) ? $get['INOD_ID'] : false;
    $INCR_ID     = isset($get['INCR_ID']) ? $get['INCR_ID'] : false;
    $INCT_ID     = isset($get['INCT_ID']) ? $get['INCT_ID'] : false;

    $query  = "SELECT ";
    $query .= "INIT.INIT_CV, INIT.INIT_COTAS, INIT.INIT_PRECO_TOTAL, ";
    $query .= "INAV.INAV_ID, INAV.INAV_CODIGO, INAV.INAV_LIQUIDEZ, INAV.INAV_VENC, INAV.INAV_STATUS, ";
    $query .= "INAT.INAT_ID, INAT.INAT_DESCRICAO, INAT.INAT_STATUS, ";
    $query .= "INTP.INTP_ID, INTP.INTP_DESCRICAO, INTP.INTP_STATUS, ";
    $query .= "INCR.INCR_ID, INCR.INCR_DESCRICAO, INCR.INCR_STATUS, ";
    $query .= "INCT.INCT_ID, INCT.INCT_DESCRICAO, INCT.INCT_STATUS, INCT.INCT_PAINEL, ";
    $query .= "INOD.INOD_ID, INOD.INOD_DESCRICAO, INOD.INOD_STATUS, INOD.INOD_DATA, ";
    $query .= "INTX.INTX_ID, INTX.INTX_VALOR_LIQUIDO_OPERACOES, INTX.INTX_TAXA_LIQUIDACAO, INTX.INTX_TAXA_REGISTRO, 
                INTX.INTX_TAXA_TERMO_OPERACOES, INTX.INTX_TAXA_ANA, INTX.INTX_EMOLUMENTOS, INTX.INTX_TAXA_OPERACIONAL, 
                INTX.INTX_EXECUCAO, INTX.INTX_TAXA_CUSTODIA, INTX.INTX_IMPOSTOS, INTX.INTX_IRRF_OPERACOES, INTX.INTX_OUTRO, INTX.INTX_STATUS ";
    $query .= "FROM       {$this->DBTables->InvestItem}       INIT ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivo}      INAV ON INAV.INAV_ID = INIT.INAV_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivoTipo}  INAT ON INAT.INAT_ID = INAV.INAT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestTipo}       INTP ON INTP.INTP_ID = INAT.INTP_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestOrdem}      INOD ON INOD.INOD_ID = INIT.INOD_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestTaxas}      INTX ON INTX.INTX_ID = INOD.INTX_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCorretora}  INCR ON INCR.INCR_ID = INOD.INCR_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestIntegrante} INTG ON INTG.USUA_ID = INAV.USUA_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCarteira}   INCT ON INCT.INCT_ID = INTG.INCT_ID ";
    
    $query .= "WHERE INCT.INCT_ID     = {$INCT_ID} ";
    $query .= "AND   INTG.USUA_ID     = {$usuario} ";
    $query .= "AND   INOD.INOD_STATUS = 1 ";
    
    if($dataDe !== false && $dataAte !== false) {
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
        $DATA_FORMATE = "DATE_FORMAT(INOD.INOD_DATA, '{$FORMAT}')";
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
        $DATA_FORMATE = "DATE_FORMAT(INOD.INOD_DATA, '{$FORMAT}')";
      } 

      $query .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false) {
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

    if($INIT_STATUS) $query .= "AND INIT.INIT_STATUS = {$INIT_STATUS} ";
    if($INIT_ID)     $query .= "AND INIT.INIT_ID     = {$INIT_ID} ";
    if($INAV_ID)     $query .= "AND INAV.INAV_ID     = {$INAV_ID} ";
    if($INAT_ID)     $query .= "AND INAT.INAT_ID     = {$INAT_ID} ";
    if($INTP_ID)     $query .= "AND INTP.INTP_ID     = {$INTP_ID} ";
    if($INOD_ID)     $query .= "AND INOD.INOD_ID     = {$INOD_ID} ";
    if($INCR_ID)     $query .= "AND INCR.INCR_ID     = {$INCR_ID} ";
    if($INCR_ID)     $query .= "AND INCT.INCT_ID     = {$INCT_ID} ";

    $query .= "GROUP BY INCR.INCR_ID, INAV.INAT_ID, INAV.INAV_ID, INIT.INIT_CV ";
    $query .= "ORDER BY INCR.INCR_DESCRICAO, INTP.INTP_DESCRICAO, INAT.INAT_DESCRICAO, INAV.INAV_CODIGO; ";
    
    // die($query);
    return DB::select($query);
  }

  public function rendimentos($get, $conditions = []) {
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
    $query .= "INAR.INAR_ID, INAR.INAR_VALOR, INAR.INAR_DATA, INAR.INAR_STATUS,  ";
    if(isset($conditions['INAR_TIPO']) && $conditions['INAR_TIPO'] == 'FULL') {
      $query .= "CASE
                  WHEN INAR.INAR_TIPO = 'R' THEN 'Rendimento'
                  WHEN INAR.INAR_TIPO = 'D' THEN 'Dividendo'
                  WHEN INAR.INAR_TIPO = 'J' THEN 'Juros Sobre Capital'
                END INAR_TIPO, ";
    }else{
      $query .= "INAR.INAR_TIPO, ";
    }
    $query .= "INAV.INAV_ID, INAV.INAV_CODIGO, INAV.INAV_LIQUIDEZ, INAV.INAV_VENC, INAV.INAV_STATUS, ";
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

    if($dataDe !== false && $dataAte !== false) {
      if(strlen($dataDe) == 7) {
        $FORMAT = '%Y-%m';
        $DATA_FORMATE = "DATE_FORMAT(INAR.INAR_DATA, '{$FORMAT}')";
      } 
      if(strlen($dataDe) == 10) {
        $FORMAT = '%Y-%m-%d';
        $DATA_FORMATE = "DATE_FORMAT(INAR.INAR_DATA, '{$FORMAT}')";
      } 

      $query .= "AND $DATA_FORMATE BETWEEN '$dataDe' AND '$dataAte' ";

    }else if($dataAte !== false) {
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

    if($INAR_STATUS) $query .= "AND INAR.INAR_STATUS = {$INAR_STATUS} ";
    if($INAR_ID)     $query .= "AND INAR.INAR_ID     = {$INAR_ID} ";
    if($INAV_ID)     $query .= "AND INAV.INAV_ID     = {$INAV_ID} ";
    if($INAT_ID)     $query .= "AND INAT.INAT_ID     = {$INAT_ID} ";
    if($INTP_ID)     $query .= "AND INTP.INTP_ID     = {$INTP_ID} ";
    if($INCR_ID)     $query .= "AND INCR.INCR_ID     = {$INCR_ID} ";
    if($INCT_ID)     $query .= "AND INCT.INCT_ID     = {$INCT_ID} ";

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

}