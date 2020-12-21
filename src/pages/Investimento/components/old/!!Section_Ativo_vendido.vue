<template>
  <section>
    <PageContentTitle titulo="Ativos Presentes">
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentTitle>

    <TableAtivo :items="itemsTable" :time="time" />

  </section>
</template>

<script>
  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {
    computed: { ...mapState(['investimento','periodo']) },

    components: { 
      ButtongetDados: () => import('@/components/Button_getDados'),
      TableAtivo:     () => import('@/pages/Investimento/components/Table_Ativo'),
    },

    data() {
      return {
        itemsTable: [],
        time: false,
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
        this.itemsTable = []

        setTimeout( () => {
          
          var endPoint = '';
          endPoint += 'carteira-ativo';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&dataAte=${this.$store.getters.Periodo}`;
          endPoint += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;

          service.invest.busca(endPoint).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              // var {ITEMS_PRESENTE, ITEMS_VENDIDO} = data
              var {ITEMS_VENDIDO} = data
              this.itemsTable = ITEMS_VENDIDO
              
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