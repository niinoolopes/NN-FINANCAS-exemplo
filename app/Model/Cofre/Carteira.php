<?php

namespace App\Model\Cofre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Carteira extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'COCT_ID';

  public $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->cofreCarteira;
  }
  
  // --

  public function get($get)
  {
    $USUA_ID     = $get['usuario'];
    $COCT_ID     = isset($get['COCT_ID']) ? $get['COCT_ID'] : false;
    $COIT_STATUS = isset($get['status'])  ? $get['status']  : false;

    // --
    
    // $TAB_ITEM       = "{$this->DBTables->cofreItem}       COIT";
    // $TAB_TIPO       = "{$this->DBTables->cofreTipo}       COTP";
    $TAB_CARTEIRA   = "{$this->DBTables->cofreCarteira}   COCT";
    $TAB_INTEGRANTE = "{$this->DBTables->cofreIntegrante} COTG";
    // $TAB_USUARIO    = "{$this->DBTables->usuario}         USUA";


    $sql  = "SELECT ";
    $sql .= "* ";
    $sql .= "FROM {$TAB_CARTEIRA} ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON COCT.COCT_ID = COTG.COCT_ID ";
    $sql .= "WHERE COTG.USUA_ID = {$USUA_ID} ";

    if( $COCT_ID )     $sql .= "AND COCT.COCT_ID = {$COCT_ID} ";
    if( $COIT_STATUS ) $sql .= "AND COCT.COCT_STATUS = {$COIT_STATUS} ";

    $sql .= "ORDER BY COCT.COCT_ID DESC";
    // die($sql);
    return DB::select($sql);
  }
}


