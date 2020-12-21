<?php

namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Ativo extends Model {

  private $DBTables;

  protected $table;
  
  protected $primaryKey = 'INAV_ID';

  public  $timestamps = false;
  
  // --
  
  public function __construct()
  {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestAtivo;
  }

  public function get($get) {
    $USUA_ID     = $get['usuario'];
    $INAV_ID     = isset($get['INAV_ID']) ? $get['INAV_ID'] : false;
    $INAV_STATUS = isset($get['status'])  ? $get['status']  : false;

    // --

    $sql  = "SELECT ";
    $sql .="INAV.INAV_ID, INAV.INAV_DESCRICAO, INAV.INAV_CODIGO, INAV_CPNJ, INAV_SITE, INAV_LIQUIDEZ, INAV_VENC, INAV.INAV_STATUS, ";
    $sql .="INAT.INAT_ID, INAT.INAT_DESCRICAO, INAT.INAT_STATUS, ";
    $sql .="INTP.INTP_ID, INTP.INTP_DESCRICAO, INTP.INTP_STATUS ";
    $sql .= "FROM       {$this->DBTables->InvestAtivo}      INAV ";
    $sql .= "INNER JOIN {$this->DBTables->InvestAtivoTipo}  INAT ON INAT.INAT_ID = INAV.INAT_ID ";
    $sql .= "INNER JOIN {$this->DBTables->InvestTipo}       INTP ON INTP.INTP_ID = INAT.INTP_ID ";
    $sql .= "WHERE INAV.USUA_ID = {$USUA_ID} ";
    
    if( $INAV_ID )     $sql .= "AND INAV.INAV_ID = {$INAV_ID} ";
    if( $INAV_STATUS ) $sql .= "AND INAV.INAV_STATUS = {$INAV_STATUS} ";
    
    $sql .= "ORDER BY INAV.INAV_CODIGO ASC ";
    // die($sql);
    return DB::select($sql);
  }
  





  
  // ainda falta refatorar, quando criar as rotas PAGE apos usar apagar
  public function ativoCarteira($get, $id) {

    $query  = 'SELECT ';
    $query .= "INCR.INCR_ID, ";
    $query .= "INCR.INCR_DESCRICAO, ";
    $query .= "INCR.INCR_STATUS, ";
    $query .= "INTP.INTP_ID, ";
    $query .= "INTP.INTP_DESCRICAO, ";
    $query .= "INTP.INTP_STATUS, ";
    $query .= "INAT.INAT_ID, ";
    $query .= "INAT.INAT_DESCRICAO, ";
    $query .= "INAT.INAT_STATUS, ";
    $query .= "INAV.INAV_ID, ";
    $query .= "INAV.INAV_CODIGO, ";
    $query .= "INAV.INAV_LIQUIDEZ, ";
    $query .= "INAV.INAV_VENC, ";
    $query .= "INAV.INAV_STATUS ";

    $query .= "FROM {$this->DBTables->InvestItem} INIT ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivo}      INAV ON INAV.INAV_ID = INIT.INAV_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestAtivoTipo}  INAT ON INAT.INAT_ID = INAV.INAT_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestTipo}       INTP ON INTP.INTP_ID = INAT.INTP_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestOrdem}      INOD ON INOD.INOD_ID = INIT.INOD_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCorretora}  INCR ON INCR.INCR_ID = INOD.INCR_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestIntegrante} INTG ON INTG.USUA_ID = INAV.USUA_ID ";
    $query .= "INNER JOIN {$this->DBTables->InvestCarteira}   INCT ON INCT.INCT_ID = INTG.INCT_ID ";

    $query .= "WHERE INCT.INCT_ID     = $id ";
    $query .= "AND   INTG.USUA_ID     = {$get['usuario']} ";
    $query .= "AND   INIT.INIT_STATUS = 1 ";
    $query .= "AND   INOD.INOD_STATUS = 1 ";

    $query .= "GROUP BY INCR.INCR_ID, INAV.INAT_ID, INAV.INAV_ID ";
    $query .= "ORDER BY INCR.INCR_DESCRICAO, INTP.INTP_DESCRICAO, INAT.INAT_DESCRICAO, INAV.INAV_CODIGO; ";

    // die($query);
    return DB::select($query);
  }
}