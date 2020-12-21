<?php

namespace App\Model\Financa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Busca extends Model {

  private $DBTables;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
  }

  public function sugestao($get) {
    $USUA_ID     = $get['usuario'];
    $FINC_ID     = $get['FINC_ID'];

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
    $sql .= "FNIT.*, ";
    $sql .= "FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_DESCRICAO, ";
    $sql .= "FINC.FINC_DESCRICAO ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN $TAB_SITUACAO   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN $TAB_TIPO       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN $TAB_GRUPO      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN $TAB_CATEGORIA  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN $TAB_CARTEIRA   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON FITG.FINC_ID = FINC.FINC_ID ";
    $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = FITG.USUA_ID ";
    $sql .= "WHERE FITG.USUA_ID = {$USUA_ID} ";
    $sql .= "  AND FINC.FINC_ID = {$FINC_ID} ";
    
    $sql .= "GROUP BY FICT.FICT_ID ";
    $sql .= "ORDER BY FNIT.FNIT_DATA DESC, FNIT.FITP_ID  ";

    // die($sql);
    return DB::select($sql);
  }

  public function mesExtrato($get)
  {
    $USUA_ID     = $get['usuario'];
    $FNIT_STATUS = isset($get['status'])  ? $get['status']  : false;
    $FNIT_DATA   = isset($get['mes'])     ? $get['mes']     : false;
    $FNIT_ID     = isset($get['FNIT_ID']) ? $get['FNIT_ID'] : false;
    $FNIS_ID     = isset($get['FNIS_ID']) ? $get['FNIS_ID'] : false;
    $FITP_ID     = isset($get['FITP_ID']) ? $get['FITP_ID'] : false;
    $FIGP_ID     = isset($get['FIGP_ID']) ? $get['FIGP_ID'] : false;
    $FICT_ID     = isset($get['FICT_ID']) ? $get['FICT_ID'] : false;
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
    $sql .= "FNIT.FNIT_ID, FNIT.FNIT_STATUS, FNIT.FNIT_VALOR, FNIT.FNIT_DATA, FNIT.FNIT_OBS, ";
    $sql .= "FNIS.FNIS_ID, FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_ID, FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_ID, FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_ID, FICT.FICT_DESCRICAO, ";
    $sql .= "FINC.FINC_ID, FINC.FINC_DESCRICAO, ";
    $sql .= "USUA.USUA_ID, USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN {$TAB_SITUACAO}   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN {$TAB_TIPO}       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN {$TAB_GRUPO}      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN {$TAB_CATEGORIA}  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN {$TAB_USUARIO}    ON USUA.USUA_ID = FNIT.USUA_ID ";
    $sql .= "INNER JOIN {$TAB_CARTEIRA}   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN {$TAB_INTEGRANTE} ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNIT.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";

    if( $FNIT_STATUS !== false ) $sql .= "AND FNIT.FNIT_STATUS = {$FNIT_STATUS} ";
    if( $FNIT_DATA ) {
        $len = strlen($FNIT_DATA);

        $FORMAT = ($len == 7) ? '%Y-%m' : '%Y-%m-%d';
      
        $sql .= "AND DATE_FORMAT(FNIT.FNIT_DATA, '{$FORMAT}') = '$FNIT_DATA' ";
    }
    if( $FNIT_ID ) $sql .= "AND FNIT.FNIT_ID = {$FNIT_ID} ";
    if( $FNIS_ID ) $sql .= "AND FNIS.FNIS_ID = {$FNIS_ID} ";
    if( $FITP_ID ) $sql .= "AND FITP.FITP_ID = {$FITP_ID} ";
    if( $FIGP_ID ) $sql .= "AND FIGP.FIGP_ID = {$FIGP_ID} ";
    if( $FICT_ID ) $sql .= "AND FICT.FICT_ID = {$FICT_ID} ";
    if( $FINC_ID ) $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";

    $sql .= "ORDER BY FNIT.FNIT_DATA DESC, FNIT.FITP_ID  ";

    // die($sql);
    return DB::select($sql);
  }


  public function mesGeral($get) {
    
    $USUA_ID     = $get['usuario'];
    $FNIT_STATUS = isset($get['status'])  ? $get['status']  : false;
    $FNIT_DATA   = isset($get['mes'])     ? $get['mes']     : false;
    
    $FNIT_ID     = isset($get['FNIT_ID']) ? $get['FNIT_ID'] : false;
    $FNIS_ID     = isset($get['FNIS_ID']) ? $get['FNIS_ID'] : false;
    $FITP_ID     = isset($get['FITP_ID']) ? $get['FITP_ID'] : false;
    $FIGP_ID     = isset($get['FIGP_ID']) ? $get['FIGP_ID'] : false;
    $FICT_ID     = isset($get['FICT_ID']) ? $get['FICT_ID'] : false;
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
    $sql .= "FNIT.FNIT_ID, FNIT.FNIT_VALOR, FNIT.FNIT_DATA, ";
    $sql .= "FNIS.FNIS_ID, FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_ID, FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_ID, FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_ID, FICT.FICT_DESCRICAO ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN {$TAB_SITUACAO}   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN {$TAB_TIPO}       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN {$TAB_GRUPO}      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN {$TAB_CATEGORIA}  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN {$TAB_USUARIO}    ON USUA.USUA_ID = FNIT.USUA_ID ";
    $sql .= "INNER JOIN {$TAB_CARTEIRA}   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN {$TAB_INTEGRANTE} ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNIT.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";
    $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";
    $sql .= "AND FNIT.FNIT_STATUS = 1 ";
    if( $FITP_ID ) $sql .= "AND FITP.FITP_ID = {$FITP_ID} ";
      
    $FORMAT = (strlen($FNIT_DATA) == 7) ? '%Y-%m' : '%Y-%m-%d';
    $sql .= "AND 
      (
        DATE_FORMAT(FNIT.FNIT_DATA, '{$FORMAT}') >= '$FNIT_DATA' AND
        DATE_FORMAT(FNIT.FNIT_DATA, '{$FORMAT}') <= '$FNIT_DATA'
      ) ";
          
    $sql .= "ORDER BY FNIT.FNIT_DATA DESC ";

    // die($sql);
    return DB::select($sql);
  }

  public function mesConsolidadoValores($get) {
    $USUA_ID     = $get['usuario'];
    $FNIT_DATA   = $get['mes'];
    $FINC_ID     = $get['FINC_ID'];

    // --

    $TAB_CARTEIRA   = "{$this->DBTables->financaCarteira}   FINC";
    $TAB_CATEGORIA  = "{$this->DBTables->financaCategoria}  FICT";
    $TAB_GRUPO      = "{$this->DBTables->financaGrupo}      FIGP";
    $TAB_INTEGRANTE = "{$this->DBTables->financaIntegrante} FITG";
    $TAB_ITEM       = "{$this->DBTables->financaItem}       FNIT";
    $TAB_SITUACAO   = "{$this->DBTables->financaSituacao}   FNIS";
    $TAB_TIPO       = "{$this->DBTables->financaTipo}       FITP";
    $TAB_USUARIO    = "{$this->DBTables->usuario}           USUA";

    $sql  = "SELECT ";
    $sql .= "FNIT.FNIT_STATUS, FNIT.FNIT_VALOR, FNIT.FITP_ID, FNIT.FNIS_ID ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN {$TAB_SITUACAO}   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN {$TAB_TIPO}       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN {$TAB_GRUPO}      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN {$TAB_CATEGORIA}  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN {$TAB_USUARIO}    ON USUA.USUA_ID = FNIT.USUA_ID ";
    $sql .= "INNER JOIN {$TAB_CARTEIRA}   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN {$TAB_INTEGRANTE} ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNIT.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";
    $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";
    $sql .= "AND DATE_FORMAT(FNIT.FNIT_DATA, '%Y-%m') = '$FNIT_DATA' ";
    $sql .= "AND FNIT.FNIT_STATUS = 1 ";

    // die($sql);
    return DB::select($sql);
  }

  public function anoConsolidadoValores($get) {
    $USUA_ID = $get['usuario'];
    $ano     = $get['ano'];
    $FINC_ID = $get['FINC_ID'];

    // --

    $TAB_CARTEIRA   = "{$this->DBTables->financaCarteira}   FINC";
    $TAB_CATEGORIA  = "{$this->DBTables->financaCategoria}  FICT";
    $TAB_GRUPO      = "{$this->DBTables->financaGrupo}      FIGP";
    $TAB_INTEGRANTE = "{$this->DBTables->financaIntegrante} FITG";
    $TAB_ITEM       = "{$this->DBTables->financaItem}       FNIT";
    $TAB_SITUACAO   = "{$this->DBTables->financaSituacao}   FNIS";
    $TAB_TIPO       = "{$this->DBTables->financaTipo}       FITP";
    $TAB_USUARIO    = "{$this->DBTables->usuario}           USUA";

    $sql  = "SELECT ";
    $sql .= "FNIT.FNIT_DATA, FNIT.FNIT_VALOR, FNIT.FITP_ID, FNIT.FNIS_ID ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN {$TAB_SITUACAO}   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN {$TAB_TIPO}       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN {$TAB_GRUPO}      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN {$TAB_CATEGORIA}  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN {$TAB_USUARIO}    ON USUA.USUA_ID = FNIT.USUA_ID ";
    $sql .= "INNER JOIN {$TAB_CARTEIRA}   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN {$TAB_INTEGRANTE} ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNIT.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";
    $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";
    $sql .= "AND DATE_FORMAT(FNIT.FNIT_DATA, '%Y') = '{$ano}' ";
    $sql .= "AND FNIT.FNIT_STATUS = 1 ";

    // die($sql);
    return DB::select($sql);
  }

  public function analiseGrupo($get) {
    $USUA_ID     = $get['usuario'];
    $anoDe       = $get['anoDe'];
    $anoAte      = $get['anoAte'];

    // $FNIT_ID     = isset($get['FNIT_ID']) ? $get['FNIT_ID'] : false;
    // $FNIS_ID     = isset($get['FNIS_ID']) ? $get['FNIS_ID'] : false;
    // $FITP_ID     = isset($get['FITP_ID']) ? $get['FITP_ID'] : false;
    $FIGP_ID     = isset($get['FIGP_ID']) ? $get['FIGP_ID'] : false;
    $FICT_ID     = isset($get['FICT_ID']) ? $get['FICT_ID'] : false;
    $FINC_ID     = isset($get['FINC_ID']) ? $get['FINC_ID'] : false;

    // --

    $TAB_CARTEIRA   = "{$this->DBTables->financaCarteira}   FINC";
    $TAB_CATEGORIA  = "{$this->DBTables->financaCategoria}  FICT";
    $TAB_GRUPO      = "{$this->DBTables->financaGrupo}      FIGP";
    $TAB_INTEGRANTE = "{$this->DBTables->financaIntegrante} FITG";
    $TAB_ITEM       = "{$this->DBTables->financaItem}       FNIT";
    // $TAB_LISTAFIXA  = "{$this->DBTables->financaListaFixa}  FNLF";
    $TAB_SITUACAO   = "{$this->DBTables->financaSituacao}   FNIS";
    $TAB_TIPO       = "{$this->DBTables->financaTipo}       FITP";
    $TAB_USUARIO    = "{$this->DBTables->usuario}           USUA";

    $sql  = "SELECT ";
    $sql .= "FNIT.FNIT_ID, FNIT.FNIT_STATUS, FNIT.FNIT_VALOR, FNIT.FNIT_DATA, FNIT.FNIT_OBS, ";
    $sql .= "FNIS.FNIS_ID, FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_ID, FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_ID, FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_ID, FICT.FICT_DESCRICAO, ";
    $sql .= "FINC.FINC_ID, FINC.FINC_DESCRICAO, ";
    $sql .= "USUA.USUA_ID, USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN {$TAB_SITUACAO}   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN {$TAB_TIPO}       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN {$TAB_GRUPO}      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN {$TAB_CATEGORIA}  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN {$TAB_USUARIO}    ON USUA.USUA_ID = FNIT.USUA_ID ";
    $sql .= "INNER JOIN {$TAB_CARTEIRA}   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN {$TAB_INTEGRANTE} ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNIT.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";
    $sql .= "AND FNIT.FNIT_STATUS = 1 ";

    $sql .= "AND 
      (
        DATE_FORMAT(FNIT.FNIT_DATA, '%Y') >= '$anoDe' AND
        DATE_FORMAT(FNIT.FNIT_DATA, '%Y') <= '$anoAte'
      )";

    // if( $FNIT_ID ) $sql .= "AND FNIT.FNIT_ID = {$FNIT_ID} ";
    // if( $FNIS_ID ) $sql .= "AND FNIS.FNIS_ID = {$FNIS_ID} ";
    // if( $FITP_ID ) $sql .= "AND FITP.FITP_ID = {$FITP_ID} ";
    if( $FIGP_ID ) $sql .= "AND FIGP.FIGP_ID = {$FIGP_ID} ";
    if( $FICT_ID ) $sql .= "AND FICT.FICT_ID = {$FICT_ID} ";
    if( $FINC_ID ) $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";

    $sql .= "ORDER BY FNIT.FNIT_DATA DESC";

    // die($sql);
    return DB::select($sql);
  }

  public function analiseUltimosMeses($get) {
    $USUA_ID     = $get['usuario'];
    $anoDe       = $get['anoDe'];
    $anoAte      = $get['anoAte'];

    // $FNIT_ID     = isset($get['FNIT_ID']) ? $get['FNIT_ID'] : false;
    // $FNIS_ID     = isset($get['FNIS_ID']) ? $get['FNIS_ID'] : false;
    $FITP_ID     = isset($get['FITP_ID']) ? $get['FITP_ID'] : false;
    // $FIGP_ID     = isset($get['FIGP_ID']) ? $get['FIGP_ID'] : false;
    // $FICT_ID     = isset($get['FICT_ID']) ? $get['FICT_ID'] : false;
    $FINC_ID     = isset($get['FINC_ID']) ? $get['FINC_ID'] : false;

    // --

    $TAB_CARTEIRA   = "{$this->DBTables->financaCarteira}   FINC";
    $TAB_CATEGORIA  = "{$this->DBTables->financaCategoria}  FICT";
    $TAB_GRUPO      = "{$this->DBTables->financaGrupo}      FIGP";
    $TAB_INTEGRANTE = "{$this->DBTables->financaIntegrante} FITG";
    $TAB_ITEM       = "{$this->DBTables->financaItem}       FNIT";
    // $TAB_LISTAFIXA  = "{$this->DBTables->financaListaFixa}  FNLF";
    $TAB_SITUACAO   = "{$this->DBTables->financaSituacao}   FNIS";
    $TAB_TIPO       = "{$this->DBTables->financaTipo}       FITP";
    $TAB_USUARIO    = "{$this->DBTables->usuario}           USUA";

    $sql  = "SELECT ";
    $sql .= "FNIT.FNIT_ID, FNIT.FNIT_STATUS, FNIT.FNIT_VALOR, FNIT.FNIT_DATA, FNIT.FNIT_OBS, ";
    $sql .= "FNIS.FNIS_ID, FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_ID, FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_ID, FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_ID, FICT.FICT_DESCRICAO, ";
    $sql .= "FINC.FINC_ID, FINC.FINC_DESCRICAO, ";
    $sql .= "USUA.USUA_ID, USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN {$TAB_SITUACAO}   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN {$TAB_TIPO}       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN {$TAB_GRUPO}      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN {$TAB_CATEGORIA}  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN {$TAB_USUARIO}    ON USUA.USUA_ID = FNIT.USUA_ID ";
    $sql .= "INNER JOIN {$TAB_CARTEIRA}   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN {$TAB_INTEGRANTE} ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNIT.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";
    $sql .= "AND FNIT.FNIT_STATUS = 1 ";

    $sql .= "AND 
      (
        DATE_FORMAT(FNIT.FNIT_DATA, '%Y-%m') > '$anoDe' AND
        DATE_FORMAT(FNIT.FNIT_DATA, '%Y-%m') <= '$anoAte'
      )";

    // if( $FNIT_ID ) $sql .= "AND FNIT.FNIT_ID = {$FNIT_ID} ";
    // if( $FNIS_ID ) $sql .= "AND FNIS.FNIS_ID = {$FNIS_ID} ";
    if( $FITP_ID ) $sql .= "AND FITP.FITP_ID = {$FITP_ID} ";
    // if( $FIGP_ID ) $sql .= "AND FIGP.FIGP_ID = {$FIGP_ID} ";
    // if( $FICT_ID ) $sql .= "AND FICT.FICT_ID = {$FICT_ID} ";
    if( $FINC_ID ) $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";

    $sql .= "ORDER BY FNIT.FNIT_DATA DESC";

    // die($sql);
    return DB::select($sql);
  }

  public function analiseAno($get) {
    $USUA_ID = $get['usuario'];
    $ano     = $get['ano'];

    // $FNIT_ID     = isset($get['FNIT_ID']) ? $get['FNIT_ID'] : false;
    // $FNIS_ID     = isset($get['FNIS_ID']) ? $get['FNIS_ID'] : false;
    $FITP_ID     = isset($get['FITP_ID']) ? $get['FITP_ID'] : false;
    // $FIGP_ID     = isset($get['FIGP_ID']) ? $get['FIGP_ID'] : false;
    // $FICT_ID     = isset($get['FICT_ID']) ? $get['FICT_ID'] : false;
    $FINC_ID     = isset($get['FINC_ID']) ? $get['FINC_ID'] : false;

    // --

    $TAB_CARTEIRA   = "{$this->DBTables->financaCarteira}   FINC";
    $TAB_CATEGORIA  = "{$this->DBTables->financaCategoria}  FICT";
    $TAB_GRUPO      = "{$this->DBTables->financaGrupo}      FIGP";
    $TAB_INTEGRANTE = "{$this->DBTables->financaIntegrante} FITG";
    $TAB_ITEM       = "{$this->DBTables->financaItem}       FNIT";
    // $TAB_LISTAFIXA  = "{$this->DBTables->financaListaFixa}  FNLF";
    $TAB_SITUACAO   = "{$this->DBTables->financaSituacao}   FNIS";
    $TAB_TIPO       = "{$this->DBTables->financaTipo}       FITP";
    $TAB_USUARIO    = "{$this->DBTables->usuario}           USUA";

    $sql  = "SELECT ";
    $sql .= "FNIT.FNIT_ID, FNIT.FNIT_STATUS, FNIT.FNIT_VALOR, FNIT.FNIT_DATA, FNIT.FNIT_OBS, ";
    $sql .= "FNIS.FNIS_ID, FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_ID, FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_ID, FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_ID, FICT.FICT_DESCRICAO, ";
    $sql .= "FINC.FINC_ID, FINC.FINC_DESCRICAO, ";
    $sql .= "USUA.USUA_ID, USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN {$TAB_SITUACAO}   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN {$TAB_TIPO}       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN {$TAB_GRUPO}      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN {$TAB_CATEGORIA}  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN {$TAB_USUARIO}    ON USUA.USUA_ID = FNIT.USUA_ID ";
    $sql .= "INNER JOIN {$TAB_CARTEIRA}   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN {$TAB_INTEGRANTE} ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNIT.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";
    $sql .= "AND FNIT.FNIT_STATUS = 1 ";
    $sql .= "AND  DATE_FORMAT(FNIT.FNIT_DATA, '%Y') = '$ano'";

    // if( $FNIT_ID ) $sql .= "AND FNIT.FNIT_ID = {$FNIT_ID} ";
    // if( $FNIS_ID ) $sql .= "AND FNIS.FNIS_ID = {$FNIS_ID} ";
    if( $FITP_ID ) $sql .= "AND FITP.FITP_ID = {$FITP_ID} ";
    // if( $FIGP_ID ) $sql .= "AND FIGP.FIGP_ID = {$FIGP_ID} ";
    // if( $FICT_ID ) $sql .= "AND FICT.FICT_ID = {$FICT_ID} ";
    if( $FINC_ID ) $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";

    $sql .= "ORDER BY FNIT.FNIT_DATA DESC";

    // die($sql);
    return DB::select($sql);
  }

  public function analiseAnoConsolidado($get) {
    $USUA_ID = $get['usuario'];
    $ano     = $get['ano'];
    $FINC_ID = $get['FINC_ID'];

    // --

    $TAB_CARTEIRA   = "{$this->DBTables->financaCarteira}   FINC";
    $TAB_CATEGORIA  = "{$this->DBTables->financaCategoria}  FICT";
    $TAB_GRUPO      = "{$this->DBTables->financaGrupo}      FIGP";
    $TAB_INTEGRANTE = "{$this->DBTables->financaIntegrante} FITG";
    $TAB_ITEM       = "{$this->DBTables->financaItem}       FNIT";
    $TAB_SITUACAO   = "{$this->DBTables->financaSituacao}   FNIS";
    $TAB_TIPO       = "{$this->DBTables->financaTipo}       FITP";
    $TAB_USUARIO    = "{$this->DBTables->usuario}           USUA";

    $sql  = "SELECT ";
    $sql .= "sum(FNIT.FNIT_VALOR) FNIT_SOMA, DATE_FORMAT(FNIT.FNIT_DATA, '%m') FNIT_MES, ";
    $sql .= "FNIS.FNIS_ID, FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_ID, FITP.FITP_DESCRICAO, ";
    $sql .= "FINC.FINC_ID, FINC.FINC_DESCRICAO ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN {$TAB_SITUACAO}   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN {$TAB_TIPO}       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN {$TAB_GRUPO}      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN {$TAB_CATEGORIA}  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN {$TAB_USUARIO}    ON USUA.USUA_ID = FNIT.USUA_ID ";
    $sql .= "INNER JOIN {$TAB_CARTEIRA}   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN {$TAB_INTEGRANTE} ON FITG.FINC_ID = FINC.FINC_ID AND FITG.USUA_ID = USUA.USUA_ID ";
    $sql .= "WHERE ( FNIT.USUA_ID = {$USUA_ID} AND USUA.USUA_ID = {$USUA_ID}  AND FITG.USUA_ID = {$USUA_ID}  ) ";
    $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";
    $sql .= "AND  DATE_FORMAT(FNIT.FNIT_DATA, '%Y') = '$ano'";
    $sql .= "AND FNIT.FNIT_STATUS = 1 ";
    $sql .= "GROUP BY  FITP.FITP_DESCRICAO, FNIS.FNIS_ID, DATE_FORMAT(FNIT.FNIT_DATA, '%m') ";
    $sql .= "ORDER BY FNIT.FNIT_DATA ASC";

    // die($sql);
    return DB::select($sql);
  }

  public function historico($get) {
    $USUA_ID = $get['usuario'];
    $FINC_ID = $get['FINC_ID'];
    $dataDe  = $get['dataDe'];
    $dataAte = $get['dataAte'];
    $limit   = $get['limit'];

    // --
    
    $TAB_CARTEIRA   = "{$this->DBTables->financaCarteira}   FINC";
    $TAB_CATEGORIA  = "{$this->DBTables->financaCategoria}  FICT";
    $TAB_GRUPO      = "{$this->DBTables->financaGrupo}      FIGP";
    $TAB_INTEGRANTE = "{$this->DBTables->financaIntegrante} FITG";
    $TAB_ITEM       = "{$this->DBTables->financaItem}       FNIT";
    // $TAB_LISTAFIXA  = "{$this->DBTables->financaListaFixa}  FNLF";
    $TAB_SITUACAO   = "{$this->DBTables->financaSituacao}   FNIS";
    $TAB_TIPO       = "{$this->DBTables->financaTipo}       FITP";
    $TAB_USUARIO    = "{$this->DBTables->usuario}           USUA";


    $sql  = "SELECT ";
    $sql .= "FNIT.*, ";
    $sql .= "FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_DESCRICAO, ";
    $sql .= "FINC.FINC_DESCRICAO, ";
    $sql .= "USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN $TAB_SITUACAO   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN $TAB_TIPO       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN $TAB_GRUPO      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN $TAB_CATEGORIA  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN $TAB_CARTEIRA   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON FITG.FINC_ID = FINC.FINC_ID ";
    $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = FITG.USUA_ID ";
    $sql .= "WHERE FITG.USUA_ID = {$USUA_ID} ";
    $sql .= "  AND ( 
              DATE_FORMAT(FNIT.FNIT_DATA, '%Y-%m-%d') >= '$dataDe' AND 
              DATE_FORMAT(FNIT.FNIT_DATA, '%Y-%m-%d') <= '$dataAte'
            )";
    $sql .= "AND FNIT.FNIT_STATUS = 1 ";
    $sql .= "AND FINC.FINC_ID = {$FINC_ID} ";
    $sql .= "ORDER BY FNIT.FNIT_ID DESC ";
    $sql .= "LIMIT {$limit} ";

    // die($sql);
    return DB::select($sql);
  }
  
  public function movimentacao($get) {
    $USUA_ID = $get['usuario'];
    $FINC_ID = $get['FINC_ID'];
    $dataDe  = $get['dataDe'];
    $dataAte = $get['dataAte'];
    $limit   = $get['limit'];

    // --
    
    $TAB_CARTEIRA   = "{$this->DBTables->financaCarteira}   FINC";
    $TAB_CATEGORIA  = "{$this->DBTables->financaCategoria}  FICT";
    $TAB_GRUPO      = "{$this->DBTables->financaGrupo}      FIGP";
    $TAB_INTEGRANTE = "{$this->DBTables->financaIntegrante} FITG";
    $TAB_ITEM       = "{$this->DBTables->financaItem}       FNIT";
    // $TAB_LISTAFIXA  = "{$this->DBTables->financaListaFixa}  FNLF";
    $TAB_SITUACAO   = "{$this->DBTables->financaSituacao}   FNIS";
    $TAB_TIPO       = "{$this->DBTables->financaTipo}       FITP";
    $TAB_USUARIO    = "{$this->DBTables->usuario}           USUA";


    $sql  = "SELECT ";
    $sql .= "FNIT.*, ";
    $sql .= "FNIS.FNIS_DESCRICAO, ";
    $sql .= "FITP.FITP_DESCRICAO, ";
    $sql .= "FIGP.FIGP_DESCRICAO, ";
    $sql .= "FICT.FICT_DESCRICAO, ";
    $sql .= "FINC.FINC_DESCRICAO, ";
    $sql .= "USUA.USUA_NOME ";
    $sql .= "FROM {$TAB_ITEM} ";
    $sql .= "INNER JOIN $TAB_SITUACAO   ON FNIS.FNIS_ID = FNIT.FNIS_ID ";
    $sql .= "INNER JOIN $TAB_TIPO       ON FITP.FITP_ID = FNIT.FITP_ID ";
    $sql .= "INNER JOIN $TAB_GRUPO      ON FIGP.FIGP_ID = FNIT.FIGP_ID ";
    $sql .= "INNER JOIN $TAB_CATEGORIA  ON FICT.FICT_ID = FNIT.FICT_ID ";
    $sql .= "INNER JOIN $TAB_CARTEIRA   ON FINC.FINC_ID = FNIT.FINC_ID ";
    $sql .= "INNER JOIN $TAB_INTEGRANTE ON FITG.FINC_ID = FINC.FINC_ID ";
    $sql .= "INNER JOIN $TAB_USUARIO    ON USUA.USUA_ID = FITG.USUA_ID ";
    $sql .= "WHERE FITG.USUA_ID = {$USUA_ID} ";  
    $sql .= "  AND FNIT.FNIT_STATUS = 1 ";
    $sql .= "  AND FINC.FINC_ID = {$FINC_ID} ";
    $sql .= "  AND ( 
                    DATE_FORMAT(FNIT.FNIT_DATA, '%Y-%m-%d') >= '$dataDe' AND 
                    DATE_FORMAT(FNIT.FNIT_DATA, '%Y-%m-%d') <= '$dataAte'
                   )";
    $sql .= "ORDER BY FNIT.FNIT_DATA DESC ";
    $sql .= "LIMIT {$limit} ";

    // die($sql);
    return DB::select($sql);
  }
}