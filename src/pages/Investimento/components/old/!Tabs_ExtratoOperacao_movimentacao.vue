<template>
  <section>

    <DateLimit :campos="campos" :setCampos="setCampos" :buscar="getDados" />

    <!--
    <PageContentNav>
      <template v-slot:menu></template>
      <template v-slot:btn> </template> 
    </PageContentNav>
    -->

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
      DateLimit:       () => import('@/components/PageContent_DateLimit'),
      TableOrdemAtivo: () => import('@/pages/Investimento/components/Table_Ordem_ativo'),
    },

    data() {
      return {
        // items: [],
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

        var endPoint = '';
        endPoint += 'item-extrato';
        endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
        endPoint += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;
        endPoint += `&INIT_STATUS=1`;
        endPoint += `&orderby=INOD_DATA:DESC|INOD_ID:DESC|INCR_DESCRICAO:ASC|INTP_DESCRICAO:ASC|INAT_DESCRICAO:ASC|INAV_CODIGO:ASC`;
        if(this.campos.dataDe)  endPoint += `&dataDe=${this.campos.dataDe}`;
        if(this.campos.dataAte) endPoint += `&dataAte=${this.campos.dataAte}`;
        if(this.campos.limit)   endPoint += `&limit=${this.campos.limit}`;

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