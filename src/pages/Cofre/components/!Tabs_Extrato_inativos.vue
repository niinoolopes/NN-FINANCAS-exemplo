<template>
  <section>

    <PageContentNav>
      <!-- <template v-slot:menu></template> -->
        
      <template v-slot:btn>
        <button 
          type="button" 
          class="div-btn btn-hover btn btn-sm btn-outline-info py-0"
          @click="getDados"><i class="fas fa-sync"></i></button>
      </template>
    </PageContentNav>

    <TableExtrato :time="time" :items="registros.itemsTable" :setItem="setItem" />

  </section>
</template>

<script>
  import service from '@/service.js'
  import {mapState} from 'vuex';

  export default {
    props:['COCT', 'setItem'],

    components: { 
      TableExtrato: () => import('@/pages/Cofre/components/Table_extrato'), 
    },

    computed: { ...mapState(['cofre','periodo']) },
    
    data() {
      return {
        registros:{
          items: [],
          itemsTable: [],
        },
        itemModal: 'init',
        time: false,
      }
    },

    mounted () {
      if( !this.$store.getters.C_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.registros.items      = []
        this.registros.itemsTable = []
        this.time = false;

        setTimeout(() => {

          var endPoint = "";
          endPoint += "inativo";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&COCT_ID=${this.$store.getters.C_COCT_ID}`;
          
          service.cofre.busca(endPoint).then( ({ STATUS, data, msg }) => {
            if (STATUS == "success") {

              var {items, itemsFiltro } = data
              items = service.cofre.formatItemsToTable( items )
              console.log('fazer ainda ', itemsFiltro);
              this.registros.items       = items
              this.registros.itemsTable  = items

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