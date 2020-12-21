<?php

namespace App\Model;

class DBTables {

    public $usuario           = 'api_nn__usuario';                  // 

    
    public $financaCarteira   = 'api_nn__financa_carteira';         // FINC_ID
    public $financaIntegrante = 'api_nn__financa_integrante';       // FITG_ID
    public $financaTipo       = 'api_nn__financa_tipo';             // FITP_ID
    public $financaGrupo      = 'api_nn__financa_grupo';            // FIGP_ID
    public $financaCategoria  = 'api_nn__financa_categoria';        // FICT_ID
    public $financaItem       = 'api_nn__financa_item';             // FNIT_ID
    public $financaSituacao   = 'api_nn__financa_situacao';         // FNIS_ID
    public $financaListaFixa  = 'api_nn__financa_lista_fixa';       // FNLF_ID


    public $cofreTipo             = 'api_nn__cofre_tipo';                    // COTP_ID, COTP_DESCRICAO
    public $cofreIntegrante       = 'api_nn__cofre_integrante';              // COTG_ID, COCT_ID, USUA_ID
    public $cofreCarteira         = 'api_nn__cofre_carteira';                // COCT_ID, COCT_DESCRICAO, COCT_STATUS, COCT_PAINEL
    public $cofreItem             = 'api_nn__cofre_item';                    // COIT_ID, COIT_VALOR, COIT_DATA, COIT_OBS, COIT_PROPOSITO, COIT_STATUS, COTP_ID, COCT_ID, USUA_ID


    public $InvestAtivo           = 'api_nn__investimento_ativo';            // ok INAV - descrição, tipo, liquidez,
    public $InvestAtivoCotacao    = 'api_nn__investimento_ativo_cotacao';    // ok INAC - cotação de ativo renda vatiavel
    public $InvestAtivoRendimento = 'api_nn__investimento_ativo_rendimento'; // ok INAR - rendimentos por ativo
    public $InvestAtivoSplit      = 'api_nn__investimento_ativo_split';      // ok INAS - CDB, Tesouro, Fii, Ação
    public $InvestAtivoTipo       = 'api_nn__investimento_ativo_tipo';       // ok INAT - CDB, Tesouro, Fii, Ação
    public $InvestCarteira        = 'api_nn__investimento_carteira';         // ok INCT - carteira de investimentos
    public $InvestCarteiraConsolidado = 'api_nn__investimento_carteira_consolidado'; // ok INCTC - carteira de investimentos
    public $InvestIntegrante      = 'api_nn__investimento_integrante';       // ok INTG
    public $InvestCorretora       = 'api_nn__investimento_corretora';        // ok INCR - esaynvest, rico
    public $InvestTipo            = 'api_nn__investimento_tipo';             // ok INTP - Renda Fixa, Renda Variavel
    public $InvestOrdemTipo       = 'api_nn__investimento_ordem_tipo';       // ok INOT - compra, venda
    public $InvestOrdem           = 'api_nn__investimento_ordem';            // INOD - data, taxas, ativos
    public $InvestTaxas           = 'api_nn__investimento_taxa';             // INTX - 
    public $InvestItem            = 'api_nn__investimento_item';             // INIT - listagem de item

    public $relatorio             = 'api_nn__relatorio';                    // RLTR - listagem de item
}
 