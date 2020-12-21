<template>
  <section>

    <DateLimit :campos="campos" :setCampos="setCampos" :buscar="getDados" />

    <!--
    <PageContentNav>
      <template v-slot:menu></template>
      <template v-slot:btn> </template> 
    </PageContentNav>
    -->

    <TableRendimento :items="itemsTable" :setItem="setItem" :time="time" />

  </section>
</template>

<script>
  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {
    props:['setItem'],

    computed: { ...mapState(['investimento','periodo']) },

    components: { 
      DateLimit:       () => import('@/components/PageContent_DateLimit'),
      TableRendimento: () => import('@/pages/Investimento/components/Table_Rendimento'),
    },

    data() {
      return {
        itemsTable: [],
        time: false,
        campos: {
          dataDe: '',
          dataAte: '',
          limit: '',
        },
      }
    },

    mounted () {
      if( this.$store.getters.I_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.time = false
        this.itemsTable = [];

        setTimeout( () => {

          var endPoint = '';
          endPoint += 'rendimentos';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;
          endPoint += `&INAR_STATUS=1`;
          endPoint += `&orderby=INAR_DATA:DESC`;
          endPoint += `&dataDe=${this.campos.dataDe}`;
          endPoint += `&dataAte=${this.campos.dataAte}`;
          endPoint += `&limit=${this.campos.limit}`;

          service.invest.busca(endPoint).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              var {items, dataDe, dataAte, limit} = data
              this.itemsTable     = items
              this.campos.limit   = limit
              this.campos.dataDe  = dataDe
              this.campos.dataAte = dataAte
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
      
      setCampos(campos) {
        this.campos = campos
      }
    },
    
    watch: {
      'investimento.INCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        this.getDados()
      }
    },
  }
</script>