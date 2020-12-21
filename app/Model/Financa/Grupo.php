<?php

namespace App\Model\Financa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Grupo extends Model {

  private $DBTables;

  protected $table;

  protected $primaryKey = 'FIGP_ID';

  public $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->financaGrupo;
  }

  // --

  public function get($get)
  {
    $USUA_ID     = $get['usuario'];
    $FIGP_STATUS = isset($get['status'])  ? $get['status']  : false;
    $FIGP_ID     = isset($get['FIGP_ID']) ? $get['FIGP_ID'] : false;
    $FITP_ID     = isset($get['FITP_ID']) ? $get['FITP_ID'] : false;
    $FINC_ID     = isset($get['FINC_ID']) ? $get['FINC_ID'] : false;

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
    $sql .= "FIGP.FIGP_ID, FIGP.FIGP_DESCRICAO, FIGP.FIGP_STATUS, ";
    $sql .= "FITP.FITP_ID, FITP.FITP_DESCRICAO, FITP.FITP_STATUS, ";
    $sql .= "FINC.FINC_ID, FINC.FINC_DESCRICAO, FINC.FINC_STATUS, FINC.FINC_PAINEL ";
    $sql .= "FROM {$TAB_GRUPO} ";
    $sql .= "INNER JOIN $TAB_TIPO ON FITP.FITP_ID = FIGP.FITP_ID ";
    $sql .= "INNER JOIN $TAB_CARTEIRA ON FINC.FINC_ID = FIGP.FINC_ID ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON FINC.FINC_ID = FITG.FINC_ID ";
    $sql .= "WHERE FITG.USUA_ID = {$USUA_ID} ";

    if( $FIGP_ID )     $sql .= "AND FINC.FIGP_ID = {$FIGP_ID} ";
    if( $FITP_ID )     $sql .= "AND FINC.FITP_ID = {$FITP_ID} ";
    if( $FINC_ID )     $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";
    if( $FIGP_STATUS ) $sql .= "AND FIGP.FIGP_STATUS = {$FIGP_STATUS} ";

    $sql .= "ORDER BY FIGP.FIGP_DESCRICAO";
    // die($sql);
    return DB::select($sql);
  }

}


