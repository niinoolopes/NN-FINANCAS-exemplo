<template>
  <section>

    <Filtro :itemsFiltro="itemsFiltro" :setFiltro="setFiltro"/>

    <PageContentNav>
      <!-- <template v-slot:menu> </template> -->

      <template v-slot:btn>
        <button
          type="button"
          class="div-btn btn-hover btn btn-sm btn-outline-info py-0 mb-1 mr-1 m-lg-0"
          @click="getDados"><i class="fas fa-sync"></i></button>
      </template>
    </PageContentNav>

    <TableSugestao :time="time" :items="itemsTable" :setItem="setItem"/>

  </section>
</template>

<script>

  import Filtro from '@/pages/Financa/components/Section_Filtro_table'
  import TableSugestao from "../components/Table_Item_sugestao"

  import service from "@/service.js";

  export default {
    props: ['setItem'],

    components: { Filtro, TableSugestao },
    
    data() {
      return {
        time: false,
        itemsFiltro: [],
        items: [],
        itemsTable: [],
      }
    },

    mounted () {
      this.getDados()
    },

    methods: {
      makeUrl() {
        var url = ''
        url += 'lista'
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&FINC_ID=${this.$store.getters.F_FINC_ID}`
        url += `&retorno=F_sugestao`
        return url
      },

      getDados() {
        this.time     = false;
        this.items    = [];
        this.itemsTable = [];

        setTimeout(() => {
          service.busca( this.makeUrl() ).then(({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              var {items,itemsFiltro} = data.F_sugestao
              items = service.finc.formatItemsToTable( items )
              this.items    = items
              this.itemsTable = items;
              this.itemsFiltro = itemsFiltro

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
    },
    
    watch: {
      '$store.getters.F_FINC_ID'(newValue, oldValue) {
        this.getDados()
      },
    },
  }
</script>