<template>
  <PageSection>

    <PageContentTitle :titulo="titulo">
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentTitle>

    <FiltroItem v-if='items != `init` && items.length' :itemsFiltro='itemsFiltro' :setFiltro='setFiltro' />

    <component v-bind:is="componentTable" :time="time" :items="itemsTable"></component>

  </PageSection>
</template>

<script>
  import service from '@/service.js'

  export default {
    props:['modo','tipo'],

    components: {
      ButtongetDados:     () => import('@/components/Button_getDados'),
      TableRendaFixa:     () => import('@/pages/Investimento/components/Table_Carteira_rendaFixa'),
      CardsRendaFixa:     () => import('@/pages/Investimento/components/Cards_Carteira_rendaFixa'),
      TableRendaVariavel: () => import('@/pages/Investimento/components/Table_Carteira_rendaVariavel'),
      CardsRendaVariavel: () => import('@/pages/Investimento/components/Cards_Carteira_rendaVariavel'),
      FiltroItem:         () => import('@/pages/Investimento/components/Section_filtro_item'),
    },

    data() {
      return {
        componentTable: '',
        time: false,
        items: [],
        itemsTable: [],
        INTP_ID: '',
        titulo: '',
        itemsFiltro: [],
      }
    },

    created () {
      this.setModo()
      this.titulo         = (this.tipo == 'renda-fixa') ? 'Renda Fixa'     : 'Renda variável';
      this.INTP_ID        = (this.tipo == 'renda-fixa') ? 1                : 2;
    },

    mounted () {
      if( this.$store.getters.I_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.time = false;
        this.itemsTable = 'init'

        setTimeout( () => {

          var endPoint = '';
          endPoint += 'carteira-composicao';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;
          endPoint += `&INTP_ID=${this.INTP_ID}`;
          endPoint += `&dataAte=${this.$store.getters.Periodo}`;

          service.invest.busca(endPoint).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              const {items, itemsFiltro} = data
              this.items = items
              this.itemsTable = items.filter( e => e.COTAS > 0)
              this.itemsFiltro = itemsFiltro
            }
            else if(STATUS == 'error'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
            this.time = true
          })
        }, service.timeLoading)
      },

      setModo(){
        if(this.modo == 'table'){
          this.componentTable = (this.tipo == 'renda-fixa') ? 'TableRendaFixa' : 'TableRendaVariavel';
        }

        if(this.modo == 'grid'){
          this.componentTable = (this.tipo == 'renda-fixa') ? 'CardsRendaFixa' : 'CardsRendaVariavel';
        }
      },
      
      setFiltro(campos) {
        this.itemsTable = service.invest.filtroItem(campos, this.items)
      },
    },

    watch: {
      '$store.getters.I_INCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      },
      'modo'(newValue, oldValue) {
        this.setModo()
      },
    },
  }
</script>