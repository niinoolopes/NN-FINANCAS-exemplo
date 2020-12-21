<?php

namespace App\Model\Cofre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Busca extends Model {

  private $DBTables;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
  }

  public function inativo($get) {
    $USUA_ID     = $get['usuario'];
    $COCT_ID     = $get['COCT_ID'];

    // --
    
    $TAB_ITEM       = "{$this->DBTables->cofreItem}       COIT";
    $TAB_TIPO       = "{$this->DBTables->cofreTipo}       COTP";
    $TAB_CARTEIRA   = "{$this->DBTables->cofreCarteira}   COCT";
    $TAB_INTEGRANTE = "{$this->DBTables->cofreIntegrante} COTG";
    $TAB_USUARIO    = "{$this->DBTables->usuario}         USUA";

    $sql  = "SELECT ";
    $sql .= "COIT.COIT_ID, COIT.COIT_VALOR, COIT.COIT_DATA, COIT.COIT_OBS, COIT.COIT_PROPOSITO, COIT.COIT_STATUS, ";
    $sql .= "COTP.COTP_ID, COTP.COTP_DESCRICAO, COTP.COTP_STATUS, ";
    $sql .= "COCT.COCT_ID, COCT.COCT_DESCRICAO, COCT.COCT_STATUS, ";
    $sql .= "USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN $TAB_TIPO       ON COTP.COTP_ID = COIT.COTP_ID ";
    $sql .= "INNER JOIN $TAB_CARTEIRA   ON COCT.COCT_ID = COIT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON COTG.COCT_ID = COCT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = COTG.USUA_ID ";
    $sql .= "WHERE (COTG.USUA_ID = {$USUA_ID} AND COIT.USUA_ID = {$USUA_ID}) ";
    $sql .= "AND COCT.COCT_ID = {$COCT_ID} ";
    $sql .= "AND COIT.COIT_STATUS = 0 ";
    $sql .= "AND COTP.COTP_STATUS = 1 ";
    $sql .= "AND COCT.COCT_STATUS = 1 ";
    $sql .= "ORDER BY COIT.COIT_ID DESC;";
    // die($sql);
    return DB::select($sql);
  }

  public function mesExtrato($get) {
    $USUA_ID     = $get['usuario'];
    $COCT_ID     = $get['COCT_ID'];
    $COIT_STATUS = isset($get['status']) ? $get['status'] : false;
    $COIT_DATA   = $get['mes'];

    // --
    
    $TAB_ITEM       = "{$this->DBTables->cofreItem}       COIT";
    $TAB_TIPO       = "{$this->DBTables->cofreTipo}       COTP";
    $TAB_CARTEIRA   = "{$this->DBTables->cofreCarteira}   COCT";
    $TAB_INTEGRANTE = "{$this->DBTables->cofreIntegrante} COTG";
    $TAB_USUARIO    = "{$this->DBTables->usuario}         USUA";

    $sql  = "SELECT ";
    $sql .= "COIT.COIT_ID, COIT.COIT_VALOR, COIT.COIT_DATA, COIT.COIT_OBS, COIT.COIT_PROPOSITO, COIT.COIT_STATUS, ";
    $sql .= "COTP.COTP_ID, COTP.COTP_DESCRICAO, COTP.COTP_STATUS, ";
    $sql .= "COCT.COCT_ID, COCT.COCT_DESCRICAO, COCT.COCT_STATUS, ";
    $sql .= "USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN $TAB_TIPO       ON COTP.COTP_ID = COIT.COTP_ID ";
    $sql .= "INNER JOIN $TAB_CARTEIRA   ON COCT.COCT_ID = COIT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON COTG.COCT_ID = COCT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = COTG.USUA_ID ";
    $sql .= "WHERE (COTG.USUA_ID = {$USUA_ID} AND COIT.USUA_ID = {$USUA_ID}) ";
    $sql .= "AND COCT.COCT_ID = {$COCT_ID} ";
    if( $COIT_STATUS ) $sql .= "AND COIT.COIT_STATUS = {$COIT_STATUS} ";
    $sql .= "AND COTP.COTP_STATUS = 1 ";
    $sql .= "AND COCT.COCT_STATUS = 1 ";
    $sql .= "AND DATE_FORMAT(COIT.COIT_DATA, '%Y-%m') = '$COIT_DATA' ";

    $sql .= "ORDER BY COIT.COIT_DATA DESC;";
    // die($sql);
    return DB::select($sql);
  }

  public function historico($get) {
    $USUA_ID = $get['usuario'];
    $COCT_ID = $get['COCT_ID'];
    $limit   = $get['limit'];

    // --
    
    $TAB_ITEM       = "{$this->DBTables->cofreItem}       COIT";
    $TAB_TIPO       = "{$this->DBTables->cofreTipo}       COTP";
    $TAB_CARTEIRA   = "{$this->DBTables->cofreCarteira}   COCT";
    $TAB_INTEGRANTE = "{$this->DBTables->cofreIntegrante} COTG";
    $TAB_USUARIO    = "{$this->DBTables->usuario}         USUA";

    $sql  = "SELECT ";
    $sql .= "COIT.COIT_ID, COIT.COIT_VALOR, COIT.COIT_DATA, COIT.COIT_OBS, COIT.COIT_PROPOSITO, COIT.COIT_STATUS, ";
    $sql .= "COTP.COTP_ID, COTP.COTP_DESCRICAO, COTP.COTP_STATUS, ";
    $sql .= "COCT.COCT_ID, COCT.COCT_DESCRICAO, COCT.COCT_STATUS, ";
    $sql .= "USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN $TAB_TIPO       ON COTP.COTP_ID = COIT.COTP_ID ";
    $sql .= "INNER JOIN $TAB_CARTEIRA   ON COCT.COCT_ID = COIT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON COTG.COCT_ID = COCT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = COTG.USUA_ID ";
    $sql .= "WHERE (COTG.USUA_ID = {$USUA_ID} AND COIT.USUA_ID = {$USUA_ID}) ";
    $sql .= "AND COCT.COCT_ID = {$COCT_ID} ";
    $sql .= "AND COIT.COIT_STATUS = 1 ";
    $sql .= "AND COTP.COTP_STATUS = 1 ";
    $sql .= "AND COCT.COCT_STATUS = 1 ";

    $sql .= "ORDER BY COIT.COIT_ID DESC ";
    $sql .= "LIMIT {$limit}; ";
    // die($sql);
    return DB::select($sql);
  }

  public function movimentacao($get) {
    $USUA_ID = $get['usuario'];
    $COCT_ID = $get['COCT_ID'];
    $dataDe  = $get['dataDe'];
    $dataAte = $get['dataAte'];
    $limit   = $get['limit'];

    $FORMAT = (strlen($dataDe) == 7) ? '%Y-%m' : '%Y-%m-%d';

    // --
    
    $TAB_ITEM       = "{$this->DBTables->cofreItem}       COIT";
    $TAB_TIPO       = "{$this->DBTables->cofreTipo}       COTP";
    $TAB_CARTEIRA   = "{$this->DBTables->cofreCarteira}   COCT";
    $TAB_INTEGRANTE = "{$this->DBTables->cofreIntegrante} COTG";
    $TAB_USUARIO    = "{$this->DBTables->usuario}         USUA";

    $sql  = "SELECT ";
    $sql .= "COIT.COIT_ID, COIT.COIT_VALOR, COIT.COIT_DATA, COIT.COIT_OBS, COIT.COIT_PROPOSITO, COIT.COIT_STATUS, ";
    $sql .= "COTP.COTP_ID, COTP.COTP_DESCRICAO, COTP.COTP_STATUS, ";
    $sql .= "COCT.COCT_ID, COCT.COCT_DESCRICAO, COCT.COCT_STATUS, ";
    $sql .= "USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN $TAB_TIPO       ON COTP.COTP_ID = COIT.COTP_ID ";
    $sql .= "INNER JOIN $TAB_CARTEIRA   ON COCT.COCT_ID = COIT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON COTG.COCT_ID = COCT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = COTG.USUA_ID ";
    $sql .= "WHERE (COTG.USUA_ID = {$USUA_ID} AND COIT.USUA_ID = {$USUA_ID}) ";
    $sql .= "AND COCT.COCT_ID = {$COCT_ID} ";
    $sql .= "AND COIT.COIT_STATUS = 1 ";
    $sql .= "AND COTP.COTP_STATUS = 1 ";
    $sql .= "AND COCT.COCT_STATUS = 1 ";
    $sql .= "AND ( DATE_FORMAT(COIT.COIT_DATA, '$FORMAT') >= '$dataDe' AND DATE_FORMAT(COIT.COIT_DATA, '$FORMAT') <= '$dataAte' ) ";
    $sql .= "ORDER BY COIT.COIT_DATA DESC ";
    $sql .= "LIMIT {$limit}; ";
    // die($sql);
    return DB::select($sql);
  }

  public function proposito($get) {
    $USUA_ID     = $get['usuario'];
    $COCT_ID     = $get['COCT_ID'];

    // --
    
    $TAB_ITEM       = "{$this->DBTables->cofreItem}       COIT";
    $TAB_TIPO       = "{$this->DBTables->cofreTipo}       COTP";
    $TAB_CARTEIRA   = "{$this->DBTables->cofreCarteira}   COCT";
    $TAB_INTEGRANTE = "{$this->DBTables->cofreIntegrante} COTG";
    $TAB_USUARIO    = "{$this->DBTables->usuario}         USUA";

    $sql  = "SELECT ";
    $sql .= "COIT.COIT_PROPOSITO ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN $TAB_TIPO       ON COTP.COTP_ID = COIT.COTP_ID ";
    $sql .= "INNER JOIN $TAB_CARTEIRA   ON COCT.COCT_ID = COIT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON COTG.COCT_ID = COCT.COCT_ID ";
    $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = COTG.USUA_ID ";
    $sql .= "WHERE (COTG.USUA_ID = {$USUA_ID} AND COIT.USUA_ID = {$USUA_ID}) ";
    $sql .= "AND COCT.COCT_ID = {$COCT_ID} ";
    $sql .= "AND COTP.COTP_STATUS = 1 ";
    $sql .= "AND COCT.COCT_STATUS = 1 ";
    $sql .= "GROUP BY COIT.COIT_PROPOSITO ";
    $sql .= "ORDER BY COIT.COIT_PROPOSITO ASC;";
    // die($sql);
    return DB::select($sql);
  }

  // public function consolidado($get) {
  //   $USUA_ID     = $get['usuario'];
  //   $COCT_ID     = $get['COCT_ID'];
  //   $COIT_STATUS = isset($get['status']) ? $get['status'] : false;

  //   // --
    
  //   $TAB_ITEM       = "{$this->DBTables->cofreItem}       COIT";
  //   $TAB_TIPO       = "{$this->DBTables->cofreTipo}       COTP";
  //   $TAB_CARTEIRA   = "{$this->DBTables->cofreCarteira}   COCT";
  //   $TAB_INTEGRANTE = "{$this->DBTables->cofreIntegrante} COTG";
  //   $TAB_USUARIO    = "{$this->DBTables->usuario}         USUA";

  //   $sql  = "SELECT ";
  //   $sql .= "COIT.COIT_PROPOSITO, ";
  //   $sql .= "sum(COIT.COIT_VALOR) COIT_SOMA, ";
  //   $sql .= "0 COIT_PERCENTUAL, ";
  //   $sql .= "COTP.COTP_ID ";
  //   $sql .= "FROM {$TAB_ITEM} ";
  //   $sql .= "INNER JOIN $TAB_TIPO       ON COTP.COTP_ID = COIT.COTP_ID ";
  //   $sql .= "INNER JOIN $TAB_CARTEIRA   ON COCT.COCT_ID = COIT.COCT_ID ";
  //   $sql .= "INNER JOIN $TAB_INTEGRANTE ON COTG.COCT_ID = COCT.COCT_ID ";
  //   $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = COTG.USUA_ID ";
  //   $sql .= "WHERE (COTG.USUA_ID = {$USUA_ID} AND COIT.USUA_ID = {$USUA_ID}) ";
  //   $sql .= "AND COCT.COCT_ID = {$COCT_ID} ";
  //   if($COIT_STATUS !== false) $sql .= "AND COIT.COIT_STATUS = {$COIT_STATUS} ";
  //   $sql .= "AND COTP.COTP_STATUS = 1 ";
  //   $sql .= "AND COCT.COCT_STATUS = 1 ";
  //   $sql .= "GROUP BY COIT.COIT_PROPOSITO, COIT.COTP_ID ";
  //   $sql .= "ORDER BY COIT.COIT_PROPOSITO ASC;";
  //   // die($sql);
  //   return DB::select($sql);
  // }
}