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
      Filtro:       () => import('@/pages/Financa/components/Section_Filtro_table'),
      TableExtrato: () => import("@/pages/Financa/components/Table_Extrato"),
    },

    data() {
      return {
        time: false,
        items: [],
        itemsTable: [],
        itemsFiltro: [],
      };
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.itemsTable = [];
        this.items    = [];
        this.time     = false;

        setTimeout(() => {

          var endPoint = "";
          endPoint += "mes-extrato";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&mes=${this.$store.getters.Periodo}`;
          endPoint += `&FINC_ID=${this.$store.getters.F_FINC_ID}`;

          service.finc.busca(endPoint).then(({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              var {items, itemsFiltro} = data
              items = service.finc.formatItemsToTable( items )

              this.items       = items;
              this.itemsTable  = items.filter( i => i.FNIT_ID != 'novo');
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