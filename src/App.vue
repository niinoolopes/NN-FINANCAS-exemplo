<template>
  <div id="app" class="d-md-flex bg-light">
    <Sidenav />
    <Btn />
    <Message />
    <router-view />
  </div>
</template>

<script>
import Sidenav from '@/template/Sidenav.vue'
import Btn from '@/template/btn.vue'

import service from '@/service.js'
import {mapState} from 'vuex'

export default {

  components: { Sidenav, Btn },

  mounted () {
    this.checkLogin();
  },

  computed: { ...mapState(['login']) },

  methods: {
    checkLogin() {

      if(this.$store.state.login == false){

        var dataStorage = service.token.get()
        var options = {
          mes: dataStorage.mes
        };

        service.login.remake(options).then( ({STATUS, data, msg}) => {

          if(STATUS == 'success'){
            service.token.set({
              token: dataStorage.token,
              mes:   dataStorage.mes,
            })
            this.$store.dispatch("SetData", data)
            
          }
          else if(STATUS == 'erro'){
            this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            service.token.set({token: '', mes: ''})
            this.$store.commit('SET_LOGIN', false);
            
          }
          else if(STATUS == 'token'){
            this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            service.token.set({token: '', mes: ''})

            this.$router.push({name: 'Logout'}).catch(err => { // redirect sem gerar erro no console
              if ( err.name !== 'NavigationDuplicated' && !err.message.includes('Avoided redundant navigation to current location') ) {
                console.log(err);
              }
            })

          }
        })
      }
    },
  },
  
  watch: {
    'login'(newValue, oldValue){

      if( newValue == true) {
        this.$router.push({name: 'Painel'}).catch(err => { // redirect sem gerar erro no console
        // this.$router.push({name: 'InvestimentoOrdem'}).catch(err => { // redirect sem gerar erro no console
          if ( err.name !== 'NavigationDuplicated' && !err.message.includes('Avoided redundant navigation to current location') ) {
            console.log(err);
          }
        })

      } else if( newValue == false ){
        this.$router.push({name: 'Login'}).catch(err => { // redirect sem gerar erro no console
          if ( err.name !== 'NavigationDuplicated' && !err.message.includes('Avoided redundant navigation to current location') ) {
            console.log(err);
          }
        })

      }
    }
  },
}
</script>

<style>
::-webkit-scrollbar {
  width: 4px;
  height: 2px;
}
::-webkit-scrollbar-thumb {
  background: var(--info);
}
::-webkit-scrollbar-track {
  background: transparent;
}
.over-x{
  overflow: hidden;
  overflow-x: auto;
  /* overflow-y: auto; */
}
@media (min-width: 768px) {
  .over-y{
    overflow: hidden;
    overflow-y: auto;
    max-height: 300px;
  }
}

*:focus{
  box-shadow: none !important;
  border-color: var(--info) !important;
}
.cursor-pointer{
  cursor: pointer;
}
.hover-none:hover {
  text-decoration: none;
}



.btn{
  transition: ease 0.3s;
}
.btn-atualizador-fixed {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  width: 32px;
  height: 28px;
}
.btn-atualizador-fixed {
  opacity: .35 !important;
}
.btn-disabled{
  cursor: unset !important;
  opacity: .25 !important;
}
.btn-hover{
  min-height: 26px;
  min-width: 26px;
  opacity: .45 !important;
}
.btn-hover:hover {
  opacity: .85 !important;
}


/* GERAL */
.div-campos {
  width: 100%;
  max-width: 880px;
}
.div-btn {
  min-height: 28px;
  width: 35px
}


/* INPUT */
.input-dataDe, .input-dataAte{
  max-width: 135px;
}
.input-limit  {
  max-width: 60px;
}


/* TABLE */
.table{
  width: max-content !important;
  min-width: 100%;
}
.table a, .table a:hover {
    color: unset;
}

@media (min-width: 768px) {
  .table .th-id,      .table .td-id{
    width: 70px
  }
}

.table .th-valor,   .table .td-valor,
.table .th-data,    .table .td-data,
.table .th-situacao, .table .td-situacao,
.table .th-status,  .table .td-status {
  max-width: 140px;
}


.table td.valor,     .table th.valor {
  min-width: 60px;
}

.table .th-corretora,   .table .td-corretora,
.table .th-liquidez,    .table .td-liquidez,
.table .th-data-venc,   .table .td-data-venc,
.table .th-qnt-cotas,   .table .td-qnt-cotas,
.table .th-total-bruto, .table .td-total-bruto,
.table .th-jscp-mes,    .table .td-jscp-mes,
.table .th-jscp-total,  .table .td-jscp-total {
  min-width: 85px;
}
/*financa*/
.table td.grupo,     .table th.grupo,
/*investimento*/
/* .table .th-tipo,             .table .td-tipo, */
/* .table .th-tipoAtivo,        .table .td-tipoAtivo, */
/* .table .th-ativo,            .table .td-ativo, */
.table .th-total-aplicado,   .table .td-total-aplicado,
.table .th-rendimento-mes,   .table .td-rendimento-mes,
.table .th-rendimento-total, .table .td-rendimento-total,
.table .th-dividendo-mes,    .table .td-dividendo-mes,
.table .th-dividendo-total,  .table .td-dividendo-total,
.table .th-preco-medio,      .table .td-preco-medio {
  min-width: 100px;
}

.table .th-m-60, .table .td-m-60 {
  min-width: 60px;
}
.table .th-m-85, .table .td-m-85 {
  min-width: 85px;
}
.table .th-m-100, .table .td-m-100 {
  min-width: 100px;
}
.table .th-m-120, .table .td-m-120 {
  min-width: 120px;
}

/*financa*/
.table .th-id,      .table .td-id,
.table .th-valor,   .table .td-valor,
/* .table .th-tipo,    .table .td-tipo, */
.table td.grupo,    .table th.grupo,
.table .th-situacao,.table .td-situacao,
.table .th-usuario, .table .td-usuario,
.table .th-status,  .table .td-status,
.table .th-data,    .table .td-data ,
/*investimento*/
.table .th-data-venc,        .table .td-data-venc,
.table .th-qnt-cotas,        .table .td-qnt-cotas,
.table .th-total-bruto,      .table .td-total-bruto,
.table .th-total-aplicado,   .table .td-total-aplicado,
.table .th-rendimento-mes,   .table .td-rendimento-mes,
.table .th-rendimento-total, .table .td-rendimento-total,
.table .th-dividendo-mes,    .table .td-dividendo-mes,
.table .th-dividendo-total,  .table .td-dividendo-total,
.table .th-jscp-mes,         .table .td-jscp-mes,
.table .th-jscp-total,       .table .td-jscp-total,
.table .th-preco-medio,      .table .td-preco-medio{
  text-align: center;
}

th{
  font-size: 14px;
}
th:not('.icon') {
  min-width: 100px;
}
th.icon {
  min-width: 50px;
}
@media screen and (max-width: 765px){
  td.responsivo, th.responsivo {
    display: none;
  }
}
td{
  font-size: 14px;
  /* vertical-align: middle !important; */
}

button:focus {
  border-color: none !important;
  outline: none !important;
}

/* NAV */
.nav .nav-tabs {
  flex-wrap: unset; 
  overflow-x: auto;
}

.nav a {
  opacity: .75 !important;
  transition: ease 0.3s;
  color: rgba(23, 162, 184, 0.50);
}
.nav a:hover {
  color: #fff;
  background: var(--info);
}
.nav a.active {
  color: var(--info) !important;
}

.content-page {
  min-height: 60vh;
}
.opacity-input{
  transition: all ease-in 0.3s;
}
.opacity-input-active{
  opacity: 0.25;
}
.opacity-fetch{
  transition: all ease-in 0.3s;
}
.opacity-fetch-active{
  opacity: 0.25;
}
section .form-control-sm{
  font-size: 12px !important;
}

.nav-tabs{
  border-bottom: none !important;
}
.nav-items .nav-item,
.sub-nav-items .nav-item{
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  opacity: 0.25;  
}
.nav-items .nav-item.active,
.sub-nav-items .nav-item.active{
  border-color: var(--info) !important;
}
</style>
