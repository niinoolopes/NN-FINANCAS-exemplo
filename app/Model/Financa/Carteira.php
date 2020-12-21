<?php

namespace App\Model\Financa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Carteira extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'FINC_ID';

  public $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->financaCarteira;
  }
  
  // --

  public function get($get)
  {
    $USUA_ID     = $get['usuario'];
    $FINC_ID     = isset($get['FINC_ID']) ? $get['FINC_ID'] : false;
    $FINC_STATUS = isset($get['status'])  ? $get['status']  : false;

    // --
    
    $TAB_CARTEIRA   = "{$this->DBTables->financaCarteira}   FINC";
    $TAB_CATEGORIA  = "{$this->DBTables->financaCategoria}  FICT";
    $TAB_GRUPO      = "{$this->DBTables->financaGrupo}      FIGP";
    $TAB_INTEGRANTE = "{$this->DBTables->financaIntegrante} FITG";
    $TAB_ITEM       = "{$this->DBTables->financaItem}       FNIT";
    $TAB_LISTAFIXA  = "{$this->DBTables->financaListaFixa}  FNLF";
    $TAB_SITUACAO   = "{$this->DBTables->financaSituacao}   FNIS";
    $TAB_TIPO       = "{$this->DBTables->financaTipo}       FITP";
    $TAB_USUARIO    = "{$this->DBTables->usuario}           USUA";


    $sql  = "SELECT ";
    $sql .= "* ";
    $sql .= "FROM {$TAB_CARTEIRA} ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON FINC.FINC_ID = FITG.FINC_ID ";
    $sql .= "WHERE FITG.USUA_ID = {$USUA_ID} ";

    if( $FINC_ID )     $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";
    if( $FINC_STATUS ) $sql .= "AND FINC.FINC_STATUS = {$FINC_STATUS} ";

    $sql .= "ORDER BY FINC.FINC_PAINEL DESC, FINC.FINC_DESCRICAO";
    // die($sql);
    return DB::select($sql);
  }
}


