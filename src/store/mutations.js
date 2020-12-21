export default {

  /* GERAL */
  RESET_STATE(state){
    state.loading = 0,
    state.login = false;
    state.usuario = {};
    state.financa = {};
  },
  SET_MESSAGE(state, payload){
    state.message = payload
  },
  
  SET_PERIODO(state, payload) {
    state.periodo = payload
  },


  /* LOGIN */
  SET_LOGIN(state, payload) {
    state.login = payload
  },

  
  /* USUARIO */
  SET_USUARIO(state, payload) {
    state.usuario = payload
  },


  /* FINANCA */
  // FINANCA_PERIODO(state, payload) {
  //   state.financa.periodo = payload
  // },
  FINANCA_ID(state, payload) {
    state.financa.FINC_ID = payload
  },
  FINANCA_CARTEIRA(state, payload) {
    state.financa.carteiras = payload
  },
  FINANCA_GRUPO(state, payload) {
    state.financa.grupo = payload
  },
  FINANCA_CATEGORIA(state, payload) {
    state.financa.categoria = payload
  },
  FINANCA_SITUACAO(state, payload) {
    state.financa.situacoes = payload
  },
  FINANCA_TIPO(state, payload) {
    state.financa.tipos = payload
  },
  // FINANCA_CONSOLIDADO_MES(state, payload) {
  //   state.financa.consolidadoMes = payload
  // },
  // FINANCA_CONSOLIDADO_ANO(state, payload) {
  //   state.financa.consolidadoAno = payload
  // },

  // FINANCA_ITEM(state, payload) {
  //   state.financa.FNIT_ID = payload
  // }


  /* COFRE */
  COFRE_ID(state, payload) {
    state.cofre.COCT_ID = payload
  },
  COFRE_CARTEIRA(state, payload) {
    state.cofre.carteira = payload
  },
  COFRE_TIPO(state, payload) {
    state.cofre.tipo = payload
  },
  COFRE_PROPOSITO(state, payload) {
    state.cofre.proposito = payload
  },
  

  /* INVESTIMENTO */
  INVESTIMENTO_ID(state, payload) {
    state.investimento.INCT_ID = payload
  },
  INVESTIMENTO_CARTEIRA(state, payload) {
    state.investimento.carteira = payload
  },
  INVESTIMENTO_CORRETORA(state, payload) {
    state.investimento.corretora = payload
  },
  INVESTIMENTO_TIPO(state, payload) {
    state.investimento.tipo = payload
  },
  INVESTIMENTO_ATIVO(state, payload) {
    state.investimento.ativo = payload
  },
  INVESTIMENTO_ATIVO_TIPO(state, payload) {
    state.investimento.ativoTipo = payload
  },
}
// 'GRUPO'      => $this->grupo->all(),
// 'CATEGORIA'  => $this->categoria->all(),