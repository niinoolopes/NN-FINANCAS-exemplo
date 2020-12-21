<template>
  <section>

    <Filtro :itemsFiltro="itemsFiltro" :setFiltro="setFiltro"/>
    
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
  import service from "@/service.js";
  import {mapState} from 'vuex';

  export default {
    props: ['setItem'],

    computed: { ...mapState(['financa','periodo']) },

    components: { 
      DateLimit:    () => import('@/components/PageContent_DateLimit'),
      Filtro:       () => import('@/pages/Financa/components/Section_Filtro_table'),
      TableExtrato: () => import("@/pages/Financa/components/Table_Extrato"),
    },

    data() {
      return {
        time: false,
        items: [],
        itemsTable: [],
        itemsFiltro: [],
        campos: {
          dataDe: '',
          dataAte: '',
          limit: '',
        },
      };
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.itemsTable = []
        this.items      = []
        this.time       = false;

        setTimeout(() => {

          var endPoint = "";
          endPoint += "historico";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&FINC_ID=${this.$store.getters.F_FINC_ID}`;
          if(this.campos.dataDe)  endPoint += `&dataDe=${this.campos.dataDe}`;
          if(this.campos.dataAte) endPoint += `&dataAte=${this.campos.dataAte}`;
          if(this.campos.limit)   endPoint += `&limit=${this.campos.limit}`;

          service.finc.busca(endPoint).then(({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              var {items, itemsFiltro, dataDe, dataAte, limit} = data
              items = service.finc.formatItemsToTable( items )

              this.items       = items
              this.itemsTable  = items
              this.itemsFiltro = itemsFiltro
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
        }, service.timeLoading);
      },
    
      setFiltro(arrFiltro){
        this.itemsTable = service.finc.filtroItems(arrFiltro, this.items)
      },

      setCampos(campos) {
        this.campos = campos
      }

    },
    
    watch: {
      'financa.FINC_ID'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        this.getDados()
      },
    },
  };
</script>