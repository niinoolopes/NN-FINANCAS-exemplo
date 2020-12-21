<template>
  <section>

    <DateLimit :campos="campos" :setCampos="setCampos" :buscar="getDados" />

    <!-- 
    <PageContentNav>
      <template v-slot:menu></template>
      <template v-slot:btn></template>
    </PageContentNav> 
    -->

    <TableExtrato :time="time" :items="itemsTable" :setItem="setItem" />

  </section>
</template>

<script>
  import service from '@/service.js'
  import {mapState} from 'vuex';

  export default {
    props:['COCT', 'setItem'],

    components: { 
      DateLimit:    () => import('@/components/PageContent_DateLimit'),
      TableExtrato: () => import('@/pages/Cofre/components/Table_extrato'), 
    },

    computed: { ...mapState(['cofre','periodo']) },
    
    data() {
      return {
        time: false,
        items: [],
        itemsTable: [],
        campos: {
          dataDe: '',
          dataAte: '',
          limit: '',
        },
      }
    },

    mounted () {
      if( !this.$store.getters.C_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.items      = []
        this.itemsTable = []
        this.time = false;

        // setTimeout(() => {

          var endPoint = "";
          endPoint += "historico";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&COCT_ID=${this.$store.getters.C_COCT_ID}`;
          if(this.campos.dataDe)  endPoint += `&dataDe=${this.campos.dataDe}`;
          if(this.campos.dataAte) endPoint += `&dataAte=${this.campos.dataAte}`;
          if(this.campos.limit)   endPoint += `&limit=${this.campos.limit}`;
          
          service.cofre.busca(endPoint).then( ({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              var {items, itemsFiltro, dataDe, dataAte, limit } = data
              items = service.cofre.formatItemsToTable( items )
              console.log('fazer ainda ', itemsFiltro);
              this.items       = items
              this.itemsTable  = items
              this.campos.dataDe  = dataDe
              this.campos.dataAte = dataAte
              this.campos.limit   = limit

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }

            this.time = true;
          });
        // }, service.timeLoading);
      },
      
      setCampos(campos) {
        this.campos = campos
      }

    },

    watch: {
      'cofre.COCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        this.getDados()
      },
      'COCT'(newCOCT, oldCOCT) {
        if(newCOCT == 'getDados'){
          this.getDados()
        }
      }
    },
  }
</script>