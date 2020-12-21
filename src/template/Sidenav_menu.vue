<template>
  <div id="sidenav-menu" class="col p-2">
    
    <nav class="nav d-block">

          <li @click="toggleMenu">
      <router-link :class="cssLink" :to="{name: 'Painel'}">Painel</router-link>
          </li>

      <hr class="border-top-1">

      <div class="form-group mb-1 rounded">
          <input class="form-control form-control-sm border-0 text-white" type="month" v-model="$store.state.periodo">
      </div>

      <hr class="border-top-1">

      <div>
        <h4 :class="cssTitulo">Finança</h4>

        <div class="form-group mb-1 rounded">
          <select class="form-control form-control-sm border-0 text-white" id="FinancaCarteira" v-model="financa.FINC_ID">
            <option v-for="(cart,i) in $store.getters.F_CarteirasAtivas" :key="i" :value="cart.FINC_ID" >{{cart.FINC_DESCRICAO}}</option>
          </select>
        </div>

        <ul class="list-group list-unstyled">
          
          <li>
            <a :class="cssLink" id="dropdownCadastrarFinanca" data-toggle="dropdown" >Cadastrar</a>

            <ul class="dropdown-menu shadow-sm border-2 border-light m-0" aria-labelledby="dropdownCadastrarFinanca">
              <li @click="toggleMenu">
                <router-link :class="cssLink" :to="{name: 'FinancaCadastrar', params: { FITP: 1}}">Cadastrar Receita</router-link>
              </li>
              <li @click="toggleMenu">
                <router-link :class="cssLink" :to="{name: 'FinancaCadastrar', params: { FITP: 2}}">Cadastrar Despesa</router-link>
              </li> 
            </ul>
          </li>

          <li @click="toggleMenu">
            <router-link :class="cssLink" :to="{name: 'FinancaGeral'}">Geral</router-link>
          </li>

          <li @click="toggleMenu">
            <router-link :class="cssLink" :to="{name: 'FinancaExtrato'}">Extrato</router-link>
          </li>

          <li>
            <a :class="cssLink" id="dropdownAnaliseFinanca" data-toggle="dropdown" >Analise</a>

            <ul class="dropdown-menu shadow-sm border-2 border-light m-0" aria-labelledby="dropdownAnaliseFinanca">
              <li @click="toggleMenu">
                <router-link :class="cssLink" :to="{name: 'FinancaAnaliseGrupo'}">Analise Grupo</router-link>
              </li>

              <li @click="toggleMenu">
                <router-link :class="cssLink" :to="{name: 'FinancaAnaliseMes'}">Analise Mês</router-link>
              </li>

              <li @click="toggleMenu">
                <router-link :class="cssLink" :to="{name: 'FinancaAnaliseAno'}">Analise Ano</router-link>
              </li>
            </ul>
          </li>

          <li @click="toggleMenu">
            <router-link :class="cssLink" :to="{name: 'FinancaListaFixa'}">Lista Fixa</router-link>
          </li>
        </ul>
      </div>

      <hr class="border-top-1">
      
      <div>
        <h4 :class="cssTitulo">Cofre</h4>

        <div class="form-group mb-1 rounded">
          <select class="form-control form-control-sm border-0 text-white" id="CofreCarteira" v-model="cofre.COCT_ID">
            <option v-for="(c,i) in $store.getters.C_CarteirasAtivas" :key="i" :value="c.COCT_ID" >{{c.COCT_DESCRICAO}}</option>
          </select>
        </div>

        <ul class="list-group list-unstyled">
          <li>
            <router-link :class="cssLink" :to="{name: 'CofreCadastrar'}">Cadastrar</router-link>
          </li>

          <li>
            <router-link :class="cssLink" :to="{name: 'CofreGeral'}">Geral</router-link>
          </li>

          <li>
            <router-link :class="cssLink" :to="{name: 'CofreExtrato'}">Extrato</router-link>
          </li>
        </ul>
      </div>

      <hr class="border-top-1">

      <div>
        <h4 :class="cssTitulo">Investimento</h4>

        <div class="form-group mb-1 rounded">
          <select class="form-control form-control-sm border-0 text-white" id="FinancaCarteira" v-model="investimento.INCT_ID">
            <option v-for="(c,i) in $store.getters.I_CarteirasAtivas" :key="i" :value="c.INCT_ID" >{{c.INCT_DESCRICAO}}</option>
          </select>
        </div>
        
        <ul class="list-group list-unstyled">
          <li>
            <router-link :class="cssLink" :to="{name: 'InvestimentoGeral'}">Geral</router-link>
          </li>

          <li>
            <router-link :class="cssLink" :to="{name: 'InvestimentoAnaliseCarteira'}">Carteira</router-link>
          </li>

          <li>
            <router-link :class="cssLink" :to="{name: 'InvestimentoAnaliseAtivo'}">Ativos</router-link>
          </li>

          <li>
            <router-link :class="cssLink" :to="{name: 'InvestimentoOrdem'}">Ordem</router-link>
          </li>

          <li>
            <router-link :class="cssLink" :to="{name: 'InvestimentoExtratoOperacao'}">Operação</router-link>
          </li>

          <li>
            <router-link :class="cssLink" :to="{name: 'InvestimentoExtratoRendimento'}">Rendimento</router-link>
          </li>

          <li>
            <router-link :class="cssLink" :to="{name: 'InvestimentoCotacao'}">Cotação</router-link>
          </li>

          <li>
            <router-link :class="cssLink" :to="{name: 'InvestimentoSplitInplit'}">Split / Inplit</router-link>
          </li>
        </ul>
      </div> 

    </nav>

  </div>
</template>

<script>
  import {mapState} from 'vuex';

  export default {
    name: 'Sidnav_menu',

    data() {
      return {
        cssTitulo: 'text-white font-weight-normal mt-2',
        cssLink: 'text-white font-italic d-block w-100 py-0 pl-2 my-1 hover-none cursor-pointer'
      }
    },

    computed: {
      ...mapState(['financa','investimento', 'cofre', 'periodo'])
    },
    
    methods: {
      toggleMenu(){
        const sidenav = document.getElementById("sidenav")
        const sidenavActive = sidenav.classList.contains('sidenav--active')
        if(sidenavActive) sidenav.classList.remove('sidenav--active') 
      },
    },
  }
</script>

<style scoped>
  .form-group{
    border: 1px solid rgba(255,255,255,0.25);
  }
  select, input,
  select:focus, input:focus {
    background: #1e7989;
  }

  #sidenav-menu{
    max-height: 100vh;
    overflow-y: auto;
    box-shadow: inset 0 0 5px 0 var(--gray-dark);
    background: rgba(0, 0, 0, .05);
  }
  .nav a{
    transition: all 0.3s;
  }
  .nav a:hover,
  .nav a.router-link-exact-active{
    background: rgba(255,255,255,0.15)
  }
  .nav a {
    position: relative;
  }
  .nav a:after {
    content: "";
    display: block;
    height: 1px;
    width: 100%;
    background: linear-gradient(90deg, var(--info) 60%, transparent 100%);
    position: absolute;
    bottom: 0px;
  }
  .nav a:hover::after,
  .nav a.router-link-exact-active::after{
    background: transparent;
  }
  .dropdown-menu{
    background: #1e7989;
    width: calc(100% - 16px);
  }
  hr {
    background: rgba(255,255,255, 0.50)
  }
</style>