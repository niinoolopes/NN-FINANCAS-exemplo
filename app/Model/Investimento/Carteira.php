<?php
namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Carteira extends Model {

  private $DBTables;

  protected $table;
  
  protected $primaryKey = 'INCT_ID';

  public  $timestamps = false;
  
  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestCarteira;
  }

  public function get($get) {
    $USUA_ID     = $get['usuario'];
    $INCT_ID     = isset($get['INCT_ID']) ? $get['INCT_ID'] : false;
    $INCT_STATUS = isset($get['status'])  ? $get['status']  : false;

    // --
    
    $sql  = "SELECT ";
    $sql .= "* ";
    $sql .= "FROM {$this->DBTables->InvestCarteira}         INCT ";
    $sql .= "INNER JOIN {$this->DBTables->InvestIntegrante} INTG ON INCT.INCT_ID = INTG.INCT_ID ";
    $sql .= "WHERE INTG.USUA_ID = {$USUA_ID} ";

    if( $INCT_ID )     $sql .= "AND INCT.INCT_ID = {$INCT_ID} ";
    if( $INCT_STATUS ) $sql .= "AND INCT.INCT_STATUS = {$INCT_STATUS} ";

    $sql .= "ORDER BY INCT.INCT_PAINEL DESC, INCT.INCT_DESCRICAO";
    // die($sql);
    return DB::select($sql);
  }
}