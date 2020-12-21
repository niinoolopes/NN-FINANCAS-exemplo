<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

use App\Model\Financa\Item       as FinancaItemModel;
use App\Model\Financa\Grupo      as FinancaGrupoModel;
use App\Model\Financa\Categoria  as FinancaCategoriaModel;
use App\Model\Financa\ListaFixa  as FinancaListaFixaModel;
use App\Model\Financa\Carteira   as FinancaCarteiraModel;
use App\Model\Financa\Integrante as FinancaIntegranteModel;
use App\Model\Financa\Situacao   as FinancaSituacaoModel;
use App\Model\Financa\Tipo       as FinancaTipoModel;

use App\Model\Investimento\AtivoTipo as I_AtivoTipo;
use App\Model\Investimento\Tipo      as I_Tipo;
use App\Model\Investimento\Corretora as I_Corretora;
use App\Model\Investimento\OrdemTipo as I_OrdemTipo;

use App\Model\Cofre\Item       as C_Item;
use App\Model\Cofre\Tipo       as C_Tipo;
use App\Model\Cofre\Carteira   as C_Carteira;
use App\Model\Cofre\Integrante as C_Integrante;

class DBQuerys extends Model 
{

  public function criaTabelas() {
    $SQLs = [];
    
    // $SQLs[] = "ALTER TABLE `api_financa`.`api_nn__financa_carteira` RENAME TO  `api_financa`.`api_nn__financa_carteira-2` ";
    // $SQLs[] = "ALTER TABLE `api_financa`.`api_nn__financa_categoria` RENAME TO  `api_financa`.`api_nn__financa_categoria-2` ";
    // $SQLs[] = "ALTER TABLE `api_financa`.`api_nn__financa_grupo` RENAME TO  `api_financa`.`api_nn__financa_grupo-2` ";
    // $SQLs[] = "ALTER TABLE `api_financa`.`api_nn__financa_integrante` RENAME TO  `api_financa`.`api_nn__financa_integrante-2` ";
    // $SQLs[] = "ALTER TABLE `api_financa`.`api_nn__financa_item` RENAME TO  `api_financa`.`api_nn__financa_item-2` ";
    // $SQLs[] = "ALTER TABLE `api_financa`.`api_nn__financa_lista_fixa` RENAME TO  `api_financa`.`api_nn__financa_lista_fixa-2` ";
    // $SQLs[] = "ALTER TABLE `api_financa`.`api_nn__financa_situacao` RENAME TO  `api_financa`.`api_nn__financa_situacao-2` ";
    // $SQLs[] = "ALTER TABLE `api_financa`.`api_nn__financa_tipo` RENAME TO  `api_financa`.`api_nn__financa_tipo-2` ";

    // $SQLs[] = "drop table IF EXISTS  `api_nn__financa_carteira`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__financa_categoria`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__financa_grupo`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__financa_integrante`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__financa_item`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__financa_lista_fixa`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__financa_situacao`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__financa_tipo`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_ativo`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_ativo_cotacao`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_ativo_rendimento`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_ativo_split`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_ativo_tipo`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_carteira`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_corretora`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_integrante`;";
    // $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_item`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_ordem`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_ordem_tipo`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_taxa`;";
    $SQLs[] = "drop table IF EXISTS  `api_nn__investimento_tipo`;";
    // $SQLs[] = "TRUNCATE `api_nn__usuario`;";

    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__financa_carteira` (
    //     `FINC_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //     `FINC_DESCRICAO` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FINC_PAINEL` int(11) NOT NULL,
    //     `FINC_STATUS` int(11) NOT NULL,
    //     PRIMARY KEY (`FINC_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    // ";
    
    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__financa_categoria` (
    //     `FICT_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //     `FICT_DESCRICAO` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FICT_STATUS` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FICT_OBS` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    //     `FICT_SLUG` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    //     `FICT_ADD_COFRE` int(11) DEFAULT NULL,
    //     `FIGP_ID` int(11) NOT NULL,
    //     PRIMARY KEY (`FICT_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    // ";
    
    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__financa_grupo` (
    //     `FIGP_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //     `FIGP_DESCRICAO` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FIGP_SLUG` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FIGP_STATUS` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FITP_ID` int(10) unsigned NOT NULL,
    //     `FINC_ID` int(10) unsigned NOT NULL,
    //     PRIMARY KEY (`FIGP_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    // ";
    
    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__financa_integrante` (
    //     `FITG_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //     `FINC_ID` int(11) NOT NULL,
    //     `USUA_ID` int(11) NOT NULL,
    //     PRIMARY KEY (`FITG_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    // ";
    
    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__financa_item` (
    //     `FNIT_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //     `FNIT_VALOR` decimal(10,2) NOT NULL,
    //     `FNIT_DATA` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FNIT_OBS` text COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FNIT_STATUS` int(11) NOT NULL,
    //     `FNIS_ID` int(11) DEFAULT NULL,
    //     `FITP_ID` int(11) DEFAULT NULL,
    //     `FIGP_ID` int(11) DEFAULT NULL,
    //     `FICT_ID` int(11) DEFAULT NULL,
    //     `FINC_ID` int(11) DEFAULT NULL,
    //     `USUA_ID` int(11) DEFAULT NULL,
    //     PRIMARY KEY (`FNIT_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    // ";
    
    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__financa_lista_fixa` (
    //     `FNLF_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //     `FNIT_VALOR` decimal(10,2) NOT NULL,
    //     `FNIT_DATA` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FNIT_OBS` text COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FNIT_STATUS` int(11) NOT NULL,
    //     `FNIS_ID` int(11) NOT NULL,
    //     `FITP_ID` int(11) NOT NULL,
    //     `FIGP_ID` int(11) NOT NULL,
    //     `FICT_ID` int(11) NOT NULL,
    //     `FINC_ID` int(11) NOT NULL,
    //     `USUA_ID` int(11) NOT NULL,
    //     PRIMARY KEY (`FNLF_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    // ";
    
    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__financa_situacao` (
    //     `FNIS_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //     `FNIS_DESCRICAO` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FNIS_STATUS` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     PRIMARY KEY (`FNIS_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    // ";
    
    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__financa_tipo` (
    //     `FITP_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //     `FITP_DESCRICAO` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `FITP_STATUS` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     PRIMARY KEY (`FITP_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    // ";
    
    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_ativo` (
        `INAV_ID`        int(11) NOT NULL AUTO_INCREMENT,
        `INAV_CODIGO`    varchar(50) DEFAULT NULL,
        `INAV_DESCRICAO` varchar(50) DEFAULT NULL,
        `INAV_CPNJ`      varchar(50) DEFAULT NULL,
        `INAV_SITE`      varchar(100) DEFAULT NULL,
        `INAV_LIQUIDEZ`  varchar(4) DEFAULT 'não',
        `INAV_VENC`      date DEFAULT NULL,
        `INAV_STATUS`    int(1) DEFAULT 1,
        `INAV_DETALHE`   text DEFAULT NULL,
        `INAT_ID`        int(11) NOT NULL,
        `USUA_ID`        int(11) NOT NULL,
        PRIMARY KEY (`INAV_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    
    ";

    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_ativo_rendimento` (
        `INAR_ID`     int(11) NOT NULL AUTO_INCREMENT,
        `INAR_TIPO`   varchar(25) NOT NULL,
        `INAR_VALOR`  decimal(10,2) DEFAULT NULL,
        `INAR_DATA`   date NOT NULL,
        `INAR_STATUS` int(1) NOT NULL DEFAULT 1,
        `INAV_ID`     int(11) NOT NULL,
        `INCR_ID`     int(11) NOT NULL,
        `INCT_ID`     int(11) NOT NULL,
        PRIMARY KEY (`INAR_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";

    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_ativo_cotacao` (
        `INAC_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INAC_VALOR` decimal(10,2) NOT NULL,
        `INAC_DATA` date NOT NULL,
        `INAC_STATUS` int(1) NOT NULL,
        `INAV_ID` int(11) NOT NULL,
        `USUA_ID` int(11) NOT NULL,
        PRIMARY KEY (`INAC_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";

    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_ativo_split` (
        `INAS_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INAS_TIPO` varchar(1) NOT NULL,
        `INAS_QUANTIDADE` decimal(10,2) DEFAULT NULL,
        `INAS_DATA` date NOT NULL,
        `INAS_STATUS` int(1) NOT NULL DEFAULT 1,
        `INAV_ID` int(11) NOT NULL,
        `USUA_ID` int(11) NOT NULL,
        PRIMARY KEY (`INAS_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";

    
    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_ativo_tipo` (
        `INAT_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INAT_DESCRICAO` varchar(50) DEFAULT NULL,
        `INAT_STATUS` int(1) NOT NULL DEFAULT 1,
        `INTP_ID` int(1) DEFAULT NULL,
        `USUA_ID` int(11) NOT NULL,
        PRIMARY KEY (`INAT_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";
    
    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_carteira` (
        `INCT_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INCT_DESCRICAO` varchar(100) DEFAULT NULL,
        `INCT_PAINEL` int(11) DEFAULT 0,
        `INCT_STATUS` int(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`INCT_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";

    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_corretora` (
        `INCR_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INCR_DESCRICAO` varchar(100) DEFAULT NULL,
        `INCR_CPNJ` varchar(45) DEFAULT NULL,
        `INCR_SITE` varchar(100) DEFAULT NULL,
        `INCR_STATUS` int(1) DEFAULT 1,
        `USUA_ID` int(11) NOT NULL,
        PRIMARY KEY (`INCR_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";
    
    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_integrante` (
        `INTG_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INCT_ID` int(11) NOT NULL,
        `USUA_ID` int(11) NOT NULL,
        PRIMARY KEY (`INTG_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";
    
    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_item` (
        `INIT_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INIT_NEGOCIACAO` varchar(50) DEFAULT NULL,
        `INIT_CV` varchar(10) DEFAULT NULL,
        `INIT_MERCADO` varchar(50) DEFAULT NULL,
        `INIT_DC` varchar(1) DEFAULT NULL,
        `INIT_COTAS` float(10,2) DEFAULT NULL,
        `INIT_PRECO_UNICO` float(10,2) DEFAULT NULL,
        `INIT_PRECO_TOTAL` float(10,2) DEFAULT NULL,
        `INOD_ID` int(11) DEFAULT NULL,
        `INIT_STATUS` int(11) DEFAULT NULL,
        `INAV_ID` int(11) DEFAULT NULL,
        PRIMARY KEY (`INIT_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";

    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_ordem` (
        `INOD_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INOD_DESCRICAO` varchar(50) DEFAULT NULL,
        `INOD_DATA` varchar(12) DEFAULT NULL,
        `INOD_STATUS` int(1) NOT NULL DEFAULT 1,
        `INOT_ID` int(11) NOT NULL,
        `INTX_ID` int(11) NOT NULL,
        `INCR_ID` int(11) NOT NULL,
        `INCT_ID` int(11) NOT NULL,
        PRIMARY KEY (`INOD_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";
    
    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_ordem_tipo` (
        `INOT_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INOT_DESCRICAO` varchar(50) DEFAULT NULL,
        `INOT_STATUS` int(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`INOT_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";
    
    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_taxa` (
        `INTX_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INTX_VALOR_LIQUIDO_OPERACOES` decimal(10,2) DEFAULT NULL,
        `INTX_TAXA_LIQUIDACAO` decimal(10,2) DEFAULT NULL,
        `INTX_TAXA_REGISTRO` decimal(10,2) DEFAULT NULL,
        `INTX_TAXA_TERMO_OPERACOES` decimal(10,2) DEFAULT NULL,
        `INTX_TAXA_ANA` decimal(10,2) DEFAULT NULL,
        `INTX_EMOLUMENTOS` decimal(10,2) DEFAULT NULL,
        `INTX_TAXA_OPERACIONAL` decimal(10,2) DEFAULT NULL,
        `INTX_EXECUCAO` decimal(10,2) DEFAULT NULL,
        `INTX_TAXA_CUSTODIA` decimal(10,2) DEFAULT NULL,
        `INTX_IMPOSTOS` decimal(10,2) DEFAULT NULL,
        `INTX_IRRF_OPERACOES` decimal(10,2) DEFAULT NULL,
        `INTX_OUTRO` decimal(10,2) DEFAULT NULL,
        `INTX_STATUS` int(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`INTX_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='INTX_IMPOSTOS';
    ";
    
    $SQLs[] = "
      CREATE TABLE IF NOT EXISTS `api_nn__investimento_tipo` (
        `INTP_ID` int(11) NOT NULL AUTO_INCREMENT,
        `INTP_DESCRICAO` varchar(50) DEFAULT NULL,
        `INTP_STATUS` int(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`INTP_ID`)
      ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    ";
    
    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__usuario` (
    //     `USUA_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //     `USUA_NOME` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `USUA_EMAIL` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `USUA_SENHA` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    //     `api_token` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    //     PRIMARY KEY (`USUA_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    // ";
    
    // $SQLs[] = "
    // //   CREATE TABLE IF NOT EXISTS `api_nn__cofre_tipo` (
    // //     `COTP_ID` int(11) NOT NULL AUTO_INCREMENT,
    // //     `COTP_DESCRICAO` varchar(100) NOT NULL,
    // //     `COTP_STATUS` int(11) NOT NULL,
    // //     PRIMARY KEY (`COTP_ID`)
    // //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    // // ";

    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__cofre_item` (
    //     `COIT_ID` int(11) NOT NULL AUTO_INCREMENT,
    //     `COIT_VALOR` decimal(10,2) NOT NULL,
    //     `COIT_DATA` date NOT NULL,
    //     `COIT_OBS` varchar(100) NOT NULL,
    //     `COIT_PROPOSITO` varchar(100) NOT NULL,
    //     `COIT_STATUS` int(11) NOT NULL,
    //     `COTP_ID` int(11) NOT NULL,
    //     `COCT_ID` varchar(45) NOT NULL,
    //     `USUA_ID` int(11) NOT NULL,
    //     PRIMARY KEY (`COIT_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    // ";

    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__cofre_integrante` (
    //     `COTG_ID` int(11) NOT NULL AUTO_INCREMENT,
    //     `COCT_ID` int(11) NOT NULL,
    //     `USUA_ID` int(11) NOT NULL,
    //     PRIMARY KEY (`COTG_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    // ";

    // $SQLs[] = "
    //   CREATE TABLE IF NOT EXISTS `api_nn__cofre_carteira` (
    //     `COCT_ID` int(11) NOT NULL AUTO_INCREMENT,
    //     `COCT_DESCRICAO` varchar(100) NOT NULL,
    //     `COCT_STATUS` int(11) NOT NULL,
    //     `COCT_PAINEL` int(11) NOT NULL,
    //     PRIMARY KEY (`COCT_ID`)
    //   ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;
    // ";
    
    foreach ($SQLs as $sql) { DB::select($sql); }
    
  }


  public function gravaValoresIniciais() {
    // TIPO Default
    foreach (['Receita', 'Despesa'] as $text) {
      $tmp = new FinancaTipoModel;

      // consulta para saber se já existe
      $find = $tmp->where('FITP_DESCRICAO',$text)->get()->toArray();
      if( count($find) == 0) {
        // grava quando não existir
        $tmp->FITP_DESCRICAO  = $text;
        $tmp->FITP_STATUS     = 1;
        $tmp->save();
      }

    }
    

    // SITUACAO Default
    foreach (['Pago', 'Pendente', 'Talvez'] as $texto) {
      $tmp = new FinancaSituacaoModel;

      // consulta para saber se já existe
      $find = $tmp->where('FNIS_DESCRICAO',$texto)->get()->toArray();
      if( count($find) == 0) {
        // grava quando não existir
        $tmp->FNIS_DESCRICAO  = $texto;
        $tmp->FNIS_STATUS     = 1;
        $tmp->save();
      }
    }
    

    // 'INVESTIMENTO TIPO' Default
    $textos = [
      'Renda Fixa',
      'Renda Variavel',
    ];
    foreach ($textos as $texto) {
      $tmp = new I_Tipo;

      // consulta para saber se já existe
      $find = $tmp->where('INTP_DESCRICAO',$texto)->get()->toArray();
      if( count($find) == 0) {
        // grava quando não existir
        $tmp->INTP_DESCRICAO = $texto;
        $tmp->INTP_STATUS    = 1;
        $tmp->save();    
      }
    }


    // 'ORDEM TIPO' Default
    $textos = [
      'Compra',
      'Venda',
    ];
    foreach ($textos as $texto) {
      $tmp = new I_OrdemTipo;

      // consulta para saber se já existe
      $find = $tmp->where('INOT_DESCRICAO',$texto)->get()->toArray();
      if( count($find) == 0) {
        // grava quando não existir
        $tmp->INOT_DESCRICAO = $texto;
        $tmp->INOT_STATUS    = 1;
        $tmp->save();    
      }
    }


    // 'ORDEM TIPO' Default
    $textos = [
      'Entrada',
      'Retirada',
    ];
    foreach ($textos as $texto) {
      $tmp = new C_Tipo;

      // consulta para saber se já existe
      $find = $tmp->where('COTP_DESCRICAO',$texto)->get()->toArray();
      if( count($find) == 0) {
        // grava quando não existir
        $tmp->COTP_DESCRICAO = $texto;
        $tmp->COTP_STATUS    = 1;
        $tmp->save();    
      }
    }



    // -- usuario 1


    // 'ATIVO TIPO' Default
    $textos = [
      'Tesouro Direto',
      'CDB',
      'Fundo de Investimento',
      'Fundos Imobiliarios',
      'Ações',
      'ETF',
      'BDR',
    ];

    foreach ($textos as $texto) {
      $tmp = new I_AtivoTipo;

      // consulta para saber se já existe
      $find = $tmp->where('INAT_DESCRICAO',$texto)->get()->toArray();
      if( count($find) == 0) {
        // grava quando não existir
        $tmp->INAT_DESCRICAO = $texto;
        $tmp->INAT_STATUS    = 1;
        $tmp->INTP_ID        = 1;
        $tmp->USUA_ID        = 1;
        $tmp->save();    
      }
    }


    // 'CORRETORA' Default
    $textos = [
      'RICO',
      'Easynvest',
      'Clear',
      'Modal +',
    ];
    foreach ($textos as $texto) {
      $tmp = new I_Corretora;

      // consulta para saber se já existe
      $find = $tmp->where('INCR_DESCRICAO',$texto)->get()->toArray();
      if( count($find) == 0) {
        // grava quando não existir
        $tmp->INCR_DESCRICAO = $texto;
        $tmp->INCR_CPNJ      = '000000-0001';
        $tmp->INCR_SITE      = 'site.com';
        $tmp->INCR_STATUS    = 1;
        $tmp->USUA_ID        = 1;
        $tmp->save();    
      }
    }
  }


  public function migrarDados()
  {
    // DB::select("TRUNCATE `api_nn__financa_grupo`;");
    // DB::select("TRUNCATE `api_nn__financa_categoria`;");

    // DB::select("TRUNCATE api_nn__cofre_integrante;");
    // DB::select("TRUNCATE api_nn__cofre_carteira;");
    // DB::select("TRUNCATE api_nn__cofre_item;");

    // DB::select("TRUNCATE `api_nn__financa_integrante`;");
    // DB::select("TRUNCATE `api_nn__financa_carteira`;");
    // DB::select("TRUNCATE `api_nn__financa_item`;");

    // DB::select("TRUNCATE `api_nn__financa_lista_fixa`;");

    // --

    // $db_in = 'rendimentos_dev.';
    $db_in = '';

    // $this->dadosFinanca($db_in);
    // $this->dadosCofre($db_in);
    // $this->dadosListaFixa($db_in);
    // $this->dadosGrupo($db_in);
    // $this->dadosCategoria($db_in);

  }

  private function dadosFinanca($db_in) {

    $carteira = DB::select("SELECT * FROM {$db_in}financa c inner join {$db_in}carteira_participante i on c.CRTR_ID = i.CRTR_ID;");
    foreach ($carteira as $item) {
      $new_carteira = new FinancaCarteiraModel;
      // $find = $new_carteira->where('FINC_DESCRICAO',$item->FINC_DESCRICAO)->get()->toArray();

      // if( count($find) == 0) {
        $new_carteira->FINC_DESCRICAO  = $item->FINC_DESCRICAO;
        $new_carteira->FINC_PAINEL     = $item->CTPT_VIEW == 1 ? 1 : 0;
        $new_carteira->FINC_STATUS     = 1;
        $new_carteira->save();
  
        $new_integrante = new FinancaIntegranteModel;
        $new_integrante->FINC_ID = $new_carteira->FINC_ID; 
        $new_integrante->USUA_ID = $item->USUA_ID;
        $new_integrante->save(); 
      // }
    }

    
    $items = DB::select("SELECT * FROM {$db_in}financa_item_1 order by FNIT_ID ASC;");
    foreach ($items as $key => $item) {

      $tmp = new FinancaItemModel;
      
      // $find = $tmp
      //             ->where('FIGP_ID', $item->FNIT_CATEGORIA)
      //             ->where('FIGP_ID', $item->FNIT_GASTO)
      //             ->where('FNIT_STATUS', $item->FNIT_STATUS)
      //             ->where('FNIT_DATA', $item->FNIT_DATA)
      //             ->where('FNIT_VALOR', $item->FNIT_VALOR)
      //             ->get()->toArray();
        
      // if( count($find) == 0) {
        if($item->FNIT_SITUACAO == 'pago')      $situacao = 1;
        if($item->FNIT_SITUACAO == 'pendente')  $situacao = 2;
        if($item->FNIT_SITUACAO == 'talvez')    $situacao = 3;
        if($item->FNIT_TIPO == 'receita')       $tipo = 1;
        if($item->FNIT_TIPO == 'gasto')         $tipo = 2;
        
        $tmp = new FinancaItemModel;
        $tmp->FNIT_VALOR  = $item->FNIT_VALOR;
        $tmp->FNIT_DATA   = $item->FNIT_DATA;
        $tmp->FNIT_OBS    = $item->FNIT_OBS;
        $tmp->FNIT_STATUS = $item->FNIT_STATUS;
        $tmp->FNIS_ID     = $situacao;
        $tmp->FITP_ID     = $tipo;
        $tmp->FIGP_ID     = $item->FNIT_CATEGORIA;
        $tmp->FICT_ID     = $item->FNIT_GASTO;
        $tmp->FINC_ID     = $item->CRTR_ID;
        $tmp->USUA_ID     = $item->USUA_ID;
  
        $tmp->save();
      // }
    }


  }

  private function dadosCofre($db_in) {

    $arr = DB::select("SELECT * FROM {$db_in}cofre c inner join {$db_in}carteira_participante i on c.CRTR_ID = i.CRTR_ID;");
    foreach ($arr as $item) {
      
      $tmp = new C_Carteira;
      // $find = $tmp->where('COCT_DESCRICAO', $item->COFR_DESCRICAO)->get()->toArray();
      // if( count($find) == 0) {
        $new_carteira = new C_Carteira;
        $new_carteira->COCT_DESCRICAO  = $item->COFR_DESCRICAO;
        $new_carteira->COCT_STATUS     = $item->CTPT_STATUS;
        $new_carteira->COCT_PAINEL     = $item->CTPT_VIEW == 1 ? 1 : 0;
        $new_carteira->save();

        $new_integrante = new C_Integrante;
        $new_integrante->COCT_ID = $new_carteira->COCT_ID; 
        $new_integrante->USUA_ID = $item->USUA_ID;
        $new_integrante->save(); 


        $dados = DB::select("SELECT * FROM {$db_in}cofre_item_{$item->CRTR_ID};");
        foreach ($dados as $key => $value) {
          $new_item = new C_Item;
          
          $find = $new_item
                          ->where('COIT_VALOR',     $value->CFRE_VALOR)
                          ->where('COIT_DATA',      $value->CFRE_OBS)
                          ->where('COIT_PROPOSITO', $value->CFRE_PROPOSITO)
                          ->where('COCT_ID',        $value->CRTR_ID)
                          ->get()->toArray();
                          
          if( count($find) == 0) {
            $new_item->COIT_VALOR     = $value->CFRE_VALOR;
            $new_item->COIT_DATA      = $value->CFRE_DATA;
            $new_item->COIT_OBS       = $value->CFRE_OBS;
            $new_item->COIT_PROPOSITO = $value->CFRE_PROPOSITO;
            $new_item->COIT_STATUS    = $value->CFRE_STATUS;
            $new_item->COTP_ID        = $value->CFRE_TIPO == 'entrada' ? 1 : 2;
            $new_item->COCT_ID        = $value->CRTR_ID;
            $new_item->USUA_ID        = $value->USUA_ID;
            $new_item->save();
            usleep(250000);
          }
        // }
      }
    }
  }

  private function dadosListaFixa($db_in) {

    $arr = DB::select("SELECT * FROM {$db_in}financa_lista_fixa;");
    foreach ($arr as $item) {

      $tmp = new FinancaListaFixaModel;
      $find = $tmp
                  ->where('FIGP_ID', $item->LTFX_CATEGORIA)
                  ->where('FICT_ID', $item->LTFX_GASTO)
                  ->where('FNIT_VALOR', $item->LTFX_VALOR)
                  ->where('FNIT_DATA', $item->LTFX_DATA)
                  ->get()->toArray();
      if( count($find) == 0) {
        if($item->LTFX_SITUACAO == 'pago') $situacao = 1;
        if($item->LTFX_SITUACAO == 'pendente') $situacao = 2;
        if($item->LTFX_SITUACAO == 'talvez') $situacao = 3;

        $new_categoria = new FinancaListaFixaModel;
        $new_categoria->FNIT_VALOR  = $item->LTFX_VALOR;
        $new_categoria->FNIT_DATA   = $item->LTFX_DATA;
        $new_categoria->FNIT_OBS    = $item->LTFX_OBS;
        $new_categoria->FNIT_STATUS = $item->LTFX_STATUS;
        $new_categoria->FNIS_ID     = $situacao;
        $new_categoria->FITP_ID     = $item->LTFX_TIPO == 'receita' ? 1 : 2;
        $new_categoria->FIGP_ID     = $item->LTFX_CATEGORIA;
        $new_categoria->FICT_ID     = $item->LTFX_GASTO;
        $new_categoria->FINC_ID     = $item->CRTR_ID;
        $new_categoria->USUA_ID     = 1;
        $new_categoria->save();
      }
    }
  }

  private function dadosGrupo($db_in) {

    $arr = DB::select("SELECT * FROM {$db_in}financa_categoria;");
    foreach ($arr as $item) {
      $tmp = new FinancaGrupoModel;
      $find = $tmp->where('FIGP_DESCRICAO', $item->FNCT_DESCRICAO)->get()->toArray();
      if( count($find) == 0) {
        $new_grupo = new FinancaGrupoModel;
        $new_grupo->FIGP_DESCRICAO  = $item->FNCT_DESCRICAO;
        $new_grupo->FIGP_SLUG       = '';
        $new_grupo->FIGP_STATUS     = $item->FNCT_STATUS;
        $new_grupo->FITP_ID         = $item->FNCT_TIPO == 'receita' ? 1 : 2;
        $new_grupo->FINC_ID         = $item->CRTR_ID;
        $new_grupo->save();
      }
    }
  }

  private function dadosCategoria($db_in) {

    $arr = DB::select("SELECT * FROM {$db_in}financa_subcategoria;");

    foreach ($arr as $item) {
      // $tmp = new FinancaCategoriaModel;
      // $find = $tmp->where('FICT_DESCRICAO', $item->FSCT_DESCRICAO)->get()->toArray();
      // if( count($find) == 0) {
        $new_categoria = new FinancaCategoriaModel;
        $new_categoria->FICT_DESCRICAO  = $item->FSCT_DESCRICAO;
        $new_categoria->FICT_STATUS     = $item->FSCT_STATUS;
        $new_categoria->FICT_OBS        = $item->FSCT_OBS_RECOMENTADA != '' ? $item->FSCT_OBS_RECOMENTADA : '';
        $new_categoria->FICT_SLUG       = '';
        $new_categoria->FICT_ADD_COFRE  = $item->FSCT_ADD_COFRINHO == 1 ? 1 : 0;
        $new_categoria->FIGP_ID         = $item->FSCT_CATEGORIA;
        $new_categoria->save();
      // }
    }
  }
}