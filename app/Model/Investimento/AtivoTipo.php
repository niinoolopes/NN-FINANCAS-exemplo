<?php
namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class AtivoTipo extends Model {

  private $DBTables;

  protected $table;
  
  protected $primaryKey = 'INAT_ID';

  public  $timestamps = false;
  
  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestAtivoTipo;
  }

  public function get($get, $id = false) {
    $USUA_ID     = $get['usuario'];
    $INAT_ID     = isset($get['INAT_ID']) ? $get['INAT_ID'] : false;
    $INAT_STATUS = isset($get['status'])  ? $get['status']  : false;

    // --
    
    $sql  = "SELECT ";
    $sql .= "INAT.INAT_ID, INAT.INAT_DESCRICAO, INAT.INAT_STATUS, ";
    $sql .= "INTP.INTP_ID, INTP.INTP_DESCRICAO, INTP.INTP_STATUS, ";
    $sql .= "USUA.USUA_ID, USUA.USUA_NOME ";
    $sql .= "FROM {$this->DBTables->InvestAtivoTipo}  INAT ";
    $sql .= "INNER JOIN {$this->DBTables->InvestTipo} INTP ON INTP.INTP_ID = INAT.INTP_ID ";
    $sql .= "INNER JOIN {$this->DBTables->usuario}    USUA ON USUA.USUA_ID = INAT.USUA_ID ";
    $sql .= "WHERE INAT.USUA_ID = {$USUA_ID} ";

    if( $INAT_ID     ) $sql .= "AND INAT.INAT_ID = {$INAT_ID} ";
    if( $INAT_STATUS ) $sql .= "AND INAT.INAT_STATUS = {$INAT_STATUS} ";

    // die($sql);
    return DB::select($sql);
  }

}