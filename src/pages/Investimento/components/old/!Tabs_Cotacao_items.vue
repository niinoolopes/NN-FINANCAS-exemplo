<template>
  <section>
    <DateLimit :campos="campos" :setCampos="setCampos" :buscar="getDados" />

    <!--
    <PageContentNav>
      <template v-slot:menu></template>
      <template v-slot:btn></template>
    </PageContentNav> 
    -->

    <TableCotacao :items='itemsTable' :time="time" :setItem="setItem" />

  </section>
</template>

<script>
  import service from '@/service.js'

  export default {
    props: ['INAC', 'setItem'],
    
    components: { 
      DateLimit:    () => import('@/components/PageContent_DateLimit'),
      TableCotacao: () => import('@/pages/Investimento/components/Table_Cotacao'),
    },

    data() {
      return {
        time: false,
        itemsTable: [],
        campos: {
          limit: '',
          dataDe: '',
          dataAte: '',
          disabled: false,
          btnDados: false,
          btnClear: false,
        },
      }
    },

    created () {
      if( this.$store.getters.I_CarteiraPainel ){
        this.getDados()
      } else {
        this.campos.disabled = true
        this.campos.btnClear = true
        this.campos.btnClear = true
      }
    },

    methods: {
      getDados() {
      this.time = false
      this.ativosCarteira = []

        setTimeout( () => {
          
          var endPoint = '';
          endPoint += 'cotacao';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          if(this.campos.dataDe)  endPoint += `&dataDe=${this.campos.dataDe}`;
          if(this.campos.dataAte) endPoint += `&dataAte=${this.campos.dataAte}`;
          if(this.campos.limit)   endPoint += `&limit=${this.campos.limit}`;

          service.invest.busca(endPoint).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              var {items, dataDe, dataAte, limit} = data
              this.itemsTable = items
              this.campos.limit = limit
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
      },

    },

    watch: {
      'INAC'(newValue, oldValue) {
        if(newValue == 'getDados'){
          this.getDados()
        }
      }
    },

  }
</script>