<template>
  <section>
    <PageContentNav>
      <!-- <template v-slot:menu></template> -->

      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentNav>

    <TableOrdemAtivo 
      :time="time"
      :setItem="setItem"
      :items="itemsTable"
      :btn="['modal','linkOrdem']"
    />

  </section>
</template>

<script>
  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {
    props:['INIT', 'setItem'],

    computed: { ...mapState(['investimento','periodo']) },

    components: { 
      ButtongetDados:  () => import('@/components/Button_getDados'),
      TableOrdemAtivo: () => import('@/pages/Investimento/components/Table_Ordem_ativo'),
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
          
        var endPoint = '';
        endPoint += 'item-extrato';
        endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
        endPoint += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;
        endPoint += `&INIT_STATUS=1`;
        endPoint += `&orderby=INCR_DESCRICAO:ASC|INTP_DESCRICAO:ASC|INAT_DESCRICAO:ASC|INAV_CODIGO:ASC|INOD_DATA:DESC`;
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
      },

    },
    
    watch: {
      'investimento.INCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        this.getDados()
      },
      'INIT'(valueNew, msgOld) {

        if(valueNew.msg == 'update'){
          this.itemsTable = this.itemsTable.map( ii => {
            if(ii.INIT_ID == valueNew.value.INIT_ID) {
              return valueNew.value
            }
            return ii
          })
        }

        if(valueNew.msg == 'add'){
          this.itemsTable.push(valueNew.value)
        }

      },
    },
  }
</script>