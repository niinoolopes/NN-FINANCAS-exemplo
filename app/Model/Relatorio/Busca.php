<?php

namespace App\Model\Relatorio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Busca extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'RLTR_ID';

  public $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->relatorio;
  }

  public function distinctFinanca($get) {
    $USUA_ID     = $get['usuario'];
    $FINC_ID     = $get['carteira'];

    // --

    $sql  = "SELECT ";
    $sql .= "DATE_FORMAT(FNIT_DATA,'%Y-%m') DATA ";
    $sql .= "FROM {$this->DBTables->financaItem} ";
    $sql .= "WHERE USUA_ID = {$USUA_ID} ";
    $sql .= "  AND FINC_ID = {$FINC_ID} ";
    $sql .= "GROUP BY date_format(FNIT_DATA,'%Y-%m') ";
    $sql .= "ORDER BY FNIT_DATA DESC; ";

    // die($sql);
    return DB::select($sql);
  }
  
  public function distinctCofre($get) {
    $USUA_ID     = $get['usuario'];
    $COCT_ID     = $get['carteira'];

    // --

    $sql  = "SELECT ";
    $sql .= "DATE_FORMAT(COIT_DATA,'%Y-%m') DATA ";
    $sql .= "FROM {$this->DBTables->cofreItem} ";
    $sql .= "WHERE USUA_ID = {$USUA_ID} ";
    $sql .= "  AND COCT_ID = {$COCT_ID} ";
    $sql .= "GROUP BY date_format(COIT_DATA,'%Y-%m') ";
    $sql .= "ORDER BY COIT_DATA DESC; ";

    return DB::select($sql);
  }

}