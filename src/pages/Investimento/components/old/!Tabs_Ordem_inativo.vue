<template>
  <section>

    <FiltroOrdem :itemsFiltro='itemsFiltro' :setFiltro='setFiltro' :getDados='getDados'/>

    <TableOrdem :items='itemsTable' :time='time' /> 

  </section>
</template>

<script>
  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {
    computed: { ...mapState(['investimento','periodo']) },

    components: {
      TableOrdem:         () => import('@/pages/Investimento/components/Table_Ordem'),
      FiltroOrdem:        () => import('@/pages/Investimento/components/Section_filtro_ordem'),
    },
    
    data() {
      return {
        time: false,
        items: [],
        itemsTable: [],
        itemsFiltro: [],
        camposFiltro: 'init',
      }
    },

    mounted () {
      if( this.$store.getters.I_CarteiraPainel ){
        setTimeout(() => {
          this.getDados()
        }, 150);
      }
    },

    methods: {
      getDados(){
        this.time = false
        this.itemsTable = [];

        setTimeout( () => {

          var endPoint = '';
          endPoint += 'ordem';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;
          endPoint += `&INOD_STATUS=0`;

          service.invest.busca(endPoint).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              var {items, itemsFiltro} = data
              this.items = items
              this.itemsTable = items
              this.itemsFiltro = itemsFiltro
              this.setFiltro(this.camposFiltro)
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
      
      setFiltro(arrFiltro) {
        if( arrFiltro != 'init') {
          this.camposFiltro = arrFiltro
          this.itemsTable = service.invest.filtroOrdem(arrFiltro, this.items)
        }
      },
    },

    watch: {
      'investimento'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        if(newValue == 'getDados'){
          this.getDados()
        } 
      },
    },

  }
</script>