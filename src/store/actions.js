  import service from '@/service.js'
  // import api from '@/api.js'

export default {

  SetData({commit}, data){
    const {PERIODO, USUARIO, FINANCA, COFRE, INVESTIMENTO} = data
    commit('SET_PERIODO', PERIODO);
    commit('SET_USUARIO', USUARIO);


    commit('FINANCA_ID',        FINANCA.FINC_ID);
    commit('FINANCA_CARTEIRA',  FINANCA.CARTEIRA);
    commit('FINANCA_GRUPO',     FINANCA.GRUPO);
    commit('FINANCA_CATEGORIA', FINANCA.CATEGORIA);
    commit('FINANCA_SITUACAO',  FINANCA.SITUACAO);
    commit('FINANCA_TIPO',      FINANCA.TIPO);


    commit('COFRE_ID',        COFRE.COCT_ID);
    commit('COFRE_CARTEIRA',  COFRE.CARTEIRA);
    commit('COFRE_TIPO',      COFRE.TIPO);
    commit('COFRE_PROPOSITO', COFRE.PROPOSITO);
    
    
    commit('INVESTIMENTO_ID',         INVESTIMENTO.INCT_ID);
    commit('INVESTIMENTO_CARTEIRA',   INVESTIMENTO.CARTEIRA);
    commit('INVESTIMENTO_CORRETORA',  INVESTIMENTO.CORRETORA);
    commit('INVESTIMENTO_TIPO',       INVESTIMENTO.TIPO);
    commit('INVESTIMENTO_ATIVO',      INVESTIMENTO.ATIVO);
    commit('INVESTIMENTO_ATIVO_TIPO', INVESTIMENTO.ATIVO_TIPO);

    
    setTimeout(() => {
      commit('SET_LOGIN', true)
    }, service.timeLoading);
  },


  // APOS REFATORAR APAGAR OS METODOS ABAIXOS

  



  /* FINANÇA */

  F_Carteiras({commit,state}){
    let option = {}
    option.USUA_ID = state.usuario.USUA_ID

    service.config.financ.carteira.get(option).then( ({data}) => {
      commit('FINANCA_CARTEIRA', data);
    })
  },
  F_Grupos({commit,state}){
    let option = {}
    option.USUA_ID = state.usuario.USUA_ID
    option.FINC_ID = state.financa.FINC_ID

    service.config.financ.grupo.get(option).then( ({data}) => {
      commit('FINANCA_GRUPO', data);
    })
  },
  F_Categorias({commit,state}){
    let option = {}
    option.USUA_ID = state.usuario.USUA_ID
    option.FINC_ID = state.financa.FINC_ID

    service.config.financ.categoria.get(option).then( ({data}) => {
      commit('FINANCA_CATEGORIA', data);
    })
  },




  // v1
  // FinancaCarteira({commit, state}){
  //   api.get(`financa/carteira?usuario=${state.usuario.USUA_ID}&grupos=true&categorias=true&status=1`)

  //   .then( FINC_CART => {
  //     if(FINC_CART.STATUS === 'success'){
        
  //       commit('FINANCA_CARTEIRAS', FINC_CART.data);
        
  //       let PAINEL_FINC = FINC_CART.data.filter( ({FINC_PAINEL}) => FINC_PAINEL == 1)
  //       if(PAINEL_FINC.length) 
  //       commit('FINANCA_ID', PAINEL_FINC[0].FINC_ID);

  //       const data = new Date()
  //       const mes = `${data.getMonth()}`.length == 1 ? `0${data.getMonth() +1}` : data.getMonth() + 1;
  //       commit('FINANCA_PERIODO', `${data.getFullYear()}-${mes}`);
  //     }
  //   })
    
  //   .catch((error) =>  alert("Erro ao buscar 'Financa: Carteiras'") );
  // },
  
  // // v1
  // FinancaTipo({commit}){
  //   api.get('financa/tipo?status=1')
    
  //   .then( TIPO => {
  //     commit('FINANCA_TIPO', TIPO );
  //   })
    
  //   .catch((error) => alert("Erro ao buscar 'Financa: Tipos'") );
  // },
  
  // // v1
  // FinancaSituacao({commit}){
  //   api.get('financa/situacao?status=1')

  //   .then( SITUACAO => {
  //     commit('FINANCA_SITUACAO', SITUACAO );
  //   })
    
  //   .catch((error) => alert("Erro ao buscar 'Financa: Situações'") );
  // },

  // // v1
  // FinancaConsolidadoMes({commit, state}){
  //   const FINC_ID = state.financa.FINC_ID
  //   const mes = state.financa.periodo

  //   api.get(`financa/item/mes?carteira=${FINC_ID}&mes=${mes}&tipo=consolidado`)

  //   .then( MES => {
  //       commit('FINANCA_CONSOLIDADO_MES', MES.data);
  //   })

  //   .catch((error) => alert("Erro ao buscar 'Financa: Dados Consolidados'") );
  // },

  // v1
  // FinancaConsolidadoAno({commit, state}){
  //   const FINC_ID = state.financa.FINC_ID
  //   const periodo = state.financa.periodo

  //   api.get(`financa/item/ano?carteira=${FINC_ID}&tipo=consolidadoPorTipo&ano=${periodo}`)
    
  //   .then( ANO => {
  //     if(ANO.STATUS === 'success') {
  //       var data = ANO.data
  //       data.RECEITA.map( (valor, i) => { data.RECEITA[i] = Number(valor) })
  //       data.DESPESA.map( (valor, i) => { data.DESPESA[i] = Number(valor) })

  //       commit('FINANCA_CONSOLIDADO_ANO', ANO.data);
  //     }
  //   })

  //   .catch((error) => alert("Erro ao buscar 'Valores para grafico'") );
  // },
}