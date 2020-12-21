export default {
  Periodo:(state, getters) => {
    return state.periodo
  },

  USUA_ID: (state, getters) => {
    return state.usuario.USUA_ID;
  },

  /* FINANCA */
  F_CarteiraPainel: (state, getters) => {
    return state.financa.FINC_ID == 0 ? true : false;
  },
  F_FINC_ID: (state, getters) => {
    return state.financa.FINC_ID
  },
  F_CarteirasAtivas: (state, getters) => {
    return state.financa.carteiras.filter( c => c.FINC_STATUS);
  },
  F_TiposAtivos: (state, getters) => {
    return state.financa.tipos.filter( t => t.FITP_STATUS);
  },
  F_Situacoes: (state, getters) => {
    return state.financa.situacoes;
  },
  F_Grupos: (state, getters) => (FINC_ID = false, FITP_ID = false) => {
    if(FINC_ID === '' || FITP_ID === '') return [];

    let grupo = state.financa.grupo;
    if(FINC_ID) grupo = grupo.filter( g => g.FINC_ID == FINC_ID )
    if(FITP_ID) grupo = grupo.filter( g => g.FITP_ID == FITP_ID )

    return grupo;
  },
  F_GruposAtivos: (state, getters) => (FINC_ID = false, FITP_ID = false) => {
    let grupo = state.financa.grupo;
    if(FINC_ID) grupo = grupo.filter( g => g.FINC_ID == FINC_ID )
    if(FITP_ID) grupo = grupo.filter( g => g.FITP_ID == FITP_ID )
    
    return grupo.filter( g => g.FIGP_STATUS)
  },
  F_Categorias: (state, getters) => (FIGP_ID = false) => {
    let categoria = state.financa.categoria;
    
    if(FIGP_ID) categoria = categoria.filter( g => g.FIGP_ID == FIGP_ID )

    return categoria;
  },
  F_CategoriasAtivas: (state, getters) => (FIGP_ID = false) => {
    if(FIGP_ID === '') return [];

    let categoria = state.financa.categoria
    if(FIGP_ID) categoria = categoria.filter( g => g.FIGP_ID == FIGP_ID )

    return categoria.filter( c => c.FICT_STATUS)
  },
  F_Observacao: (state, getters) => (FICT_ID, FICT_OBS = false) => {
    if(FICT_ID){
      // var FICT_OBS_exist = false;
      let categorias = getters.F_CategoriasAtivas()
      var categoria = categorias.filter(c => c.FICT_ID == FICT_ID)[0]
      var OBS = categoria.FICT_OBS


      // // busca obs do form para ver se já existe      
      if(FICT_OBS) {
        var obs_categoria = categorias.filter(c => c.FICT_OBS == FICT_OBS)
        // quando o texto não existir como OBS retorna o texto
        if(obs_categoria.length == 0){
          OBS = FICT_OBS
        }
      }
      return OBS
    }
    return ''
  },


  /* INVESTIMENTO */
  I_CarteiraPainel: (state, getters) => {
    return state.investimento.INOD_ID == 0 ? false : true;
  },
  I_INCT_ID: (state, getters) => {
    return state.investimento.INCT_ID
  },
  I_CarteirasAtivas: (state, getters) => {
    return state.investimento.carteira.filter( c => c.INCT_STATUS);
  },
  I_CorretorasAtivas: (state, getters) => {
    return state.investimento.corretora.filter( c => c.INCR_STATUS);
  },
  I_Tipos: (state, getters) => {
    return state.investimento.tipo.filter( t => t.INTP_STATUS);
  },
  I_AtivoTipos: (state, getters) => (INTP_ID = false) => {
    let ativoTipos = state.investimento.ativoTipo;

    if(INTP_ID) ativoTipos = ativoTipos.filter( at => at.INTP_ID == INTP_ID)

    return ativoTipos;
  },
  I_AtivoTiposAtivos: (state, getters) => (INTP_ID = false) => {
    let ativoTipos = getters.I_AtivoTipos(INTP_ID);

    return ativoTipos.filter( at => at.INAT_STATUS )
  },
  I_AtivoAtivos: (state, getters) => ({INAT_ID, INTP_ID} = {}) => {
    let ativos = state.investimento.ativo

    if(INAT_ID) ativos = ativos.filter( a => a.INAT_ID == INAT_ID)
    if(INTP_ID) ativos = ativos.filter( a => a.INTP_ID == INTP_ID)

    // return ativos
    return ativos.filter( a => a.INAV_STATUS)
  },


  /* COFRE */
  C_CarteiraPainel: (state, getters) => {
    return state.cofre.COCT_ID == 0 ? true : false;
  },
  C_COCT_ID: (state, getters) => {
    return state.cofre.COCT_ID
  },
  C_CarteirasAtivas: (state, getters) => {
    return state.cofre.carteira.filter( c => c.COCT_STATUS);
  },
  C_Tipos: (state, getters) => {
    return state.cofre.tipo.filter( c => c.COTP_STATUS)
  },



  // -- excluir depois
  
  // F_getGrupo: (state, getters) => ({FINC_ID, FITP_ID}) => {
  //   if(FINC_ID != '' && FITP_ID != ''){

  //     let carteira = getters.F_CarteirasAtivas.filter( c => c.FINC_ID == FINC_ID )[0]

  //     if(carteira.GRUPOS.length){
  //       return carteira.GRUPOS.filter( g => g.FIGP_STATUS && g.FITP_ID == FITP_ID )
  //     }
  //   }
  //   return [];
  // },
  // F_getCategoria: (state, getters) => ({FINC_ID, FITP_ID, FIGP_ID}) => {
  //   if(FINC_ID != '' && FITP_ID != '' && FIGP_ID != ''){

  //     let grupos = getters.F_getGrupo({FINC_ID, FITP_ID})
  //     let grupo  = grupos.filter( g => g.FIGP_ID = FIGP_ID)[0]
  //     if(grupo.CATEGORIAS.length){
  //       return grupo.CATEGORIAS.filter( c => c.FICT_STATUS)
  //     }
  //   }
  //   return []
  // },
  // F_getObservacao: (state, getters) => ({FINC_ID, FITP_ID, FIGP_ID, FICT_ID, FNIT_OBS}) => {
  //   if(FINC_ID != '' && FITP_ID != '' && FIGP_ID != '' && FICT_ID != '' && FNIT_OBS != ''){
    
  //     let categorias = getters.F_getCategoria({FINC_ID,FITP_ID,FIGP_ID})
  //     let categoria  = categorias.filter( c => c.FIGP_ID = FICT_ID)[0]  
      
  //     if(categoria.FICT_OBS !== ''){
  //       return categoria.FICT_OBS
  //     }
  //   }
  //   return '';
  // }
}