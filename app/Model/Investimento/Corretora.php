<?php
namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Corretora extends Model {

  private $DBTables;

  protected $table;
  
  protected $primaryKey = 'INCR_ID';

  public  $timestamps = false;
  
  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestCorretora;
  }

  public function get($get) {
    $USUA_ID     = $get['usuario'];
    $INCR_ID     = isset($get['INCR_ID'])  ? $get['INCR_ID']  : false;
    $INCR_STATUS = isset($get['status'])  ? $get['status']  : false;

    // --
    
    $sql  = "SELECT ";
    $sql .= "* ";
    $sql .= "FROM {$this->DBTables->InvestCorretora} INCR ";
    $sql .= "WHERE INCR.USUA_ID = {$USUA_ID} ";

    if( $INCR_ID )     $sql .= "AND INCR.INCR_ID = {$INCR_ID} ";
    if( $INCR_STATUS ) $sql .= "AND INCR.INCR_STATUS = {$INCR_STATUS} ";

    // die($sql);
    return DB::select($sql);
  }
}