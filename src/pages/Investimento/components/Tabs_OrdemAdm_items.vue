<template>
  <section>
    
    <PageContentNav>
        <!-- <template v-slot:menu></template> -->

      <template v-slot:btn>
        <ButtonplusItem :setItem='setItem' novo='novo' target='InvestimentoOrdemItem' />
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentNav>

    <FiltroOperacoes :itemsFiltro='itemsFiltro' :setFiltro='setFiltro' />

    <TableOrdemAtivo 
      :time="time" 
      :items="itemsTable" 
      :setItem="setItem"
      :btn="['modal']"
    />

    <ModalOrdemAtivo
      :INOD_ID='INOD_ID'
      :INIT='INIT'
      :setItem="setItem"
    />

  </section>
</template>

<script>
  // import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {
    props: ['INOD_ID'],
    
    components: { 
      ButtonplusItem:  () => import('@/components/Button_plusItemModal'),
      ButtongetDados:  () => import('@/components/Button_getDados'),
      TableOrdemAtivo: () => import('@/pages/Investimento/components/Table_Ordem_ativo'),
      ModalOrdemAtivo: () => import('@/pages/Investimento/components/Modal_Ordem_ativo'),
      FiltroOperacoes: () => import('@/pages/Investimento/components/Section_filtro_operacao'),
    },

    data() {
      return {
        time: false,
        INIT: 'init',
        items: [],
        itemsTable: [],
        itemsFiltro: [],
        camposFiltro: 'init',
      }
    },

    mounted () {
      if(this.INOD_ID && this.INOD_ID != 'novo'){
        this.getDados()
      }
    },

    methods: {
      getDados(){
        this.time = false
        this.itemsTable = [];

        setTimeout( () => {
          
          var url = '';
          url += 'lista'
          url += `?usuario=${this.$store.getters.USUA_ID}`;
          url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`
          url += `&INOD_ID=${this.INOD_ID}`;
          url += `&orderby=INIT_ID:ASC`;
          url += '&retorno=I_itemsOrdem'

          service.busca(url)
          .then( ({STATUS, data, msg}) => {

            if (STATUS == "success") {
              const {items, itemsFiltro} = data.I_itemsOrdem
              this.items       = items
              this.itemsTable  = items
              this.itemsFiltro = itemsFiltro
              this.setFiltro(this.camposFiltro)

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }
            this.time = true
          })
        }, service.timeLoading)
      },
      
      setItem(INIT) {
        this.INIT = INIT 
      },
      
      setFiltro(campos) {
        if( campos != 'init') {
          this.camposFiltro = campos
          this.itemsTable = service.invest.filtroOperacoes(campos, this.items)
        }
      },
    },

    watch: {
      'INOD_ID'(newValue, oldValue) {
        if(newValue == 'getDados'){
          this.getDados()
        } 
      },
      'INIT'(newValue, oldValue) {
        if(newValue == 'getDados'){
          this.getDados()
        } 
      },
    },
  }
</script>