<template>
  <section>

    <PageContentNav>
      <!-- <template v-slot:menu></template> -->

      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentNav>

    <TableRendimento :items="itemsTable" :setItem="setItem" :time="time" />

  </section>
</template>

<script>
  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {
    props:['INAR', 'setItem'],

    computed: { ...mapState(['investimento','periodo']) },

    components: { 
      ButtongetDados:  () => import('@/components/Button_getDados'),
      TableRendimento: () => import('@/pages/Investimento/components/Table_Rendimento'),
    },

    data() {
      return {
        // items: [],
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
        this.itemsTable = [];
          
        setTimeout( () => {

          var endPoint = '';
          endPoint += 'rendimentos';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;
          endPoint += `&INAR_STATUS=1`;
          endPoint += `&orderby=INAR_DATA:DESC`;
          endPoint += `&data=${this.$store.getters.Periodo}`;

          service.invest.busca(endPoint).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              var {items} = data
              this.itemsTable = items
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
      },
      'INAR'(newValue, oldValue) {
        if(newValue == 'getDados') {
          this.getDados()
        }
      },
    },
  }
</script>