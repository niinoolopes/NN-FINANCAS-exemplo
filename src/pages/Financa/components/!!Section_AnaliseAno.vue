<template>
  <PageSection>
    
    <PageContentTitle :titulo="tipo">
      <template v-slot:btn>
        <button 
          type="button" 
          class="btn-hover btn btn-sm btn-outline-info py-0 mr-2" 
          @click="getDados"><i class="fas fa-sync"></i></button>
      </template>
    </PageContentTitle>

    <div class="d-none d-lg-block">
      <!-- <GraficoBarV
        :FITP="FITP_ID"
        :titulo="tipo"
        :labels="registros.items_GRAFICO.labels"
        :valores="registros.items_GRAFICO.valores"
        :style="{
          height: ( registros.items_GRAFICO.labels.length <= 20 ) ? '200px' : '300px'
        }" 
      /> -->
    </div>

    <br>

    <!-- <TableAnalise 
      :time="time" 
      :th="th" 
      :items="registros.items" /> -->

  </PageSection>
</template>

<script>
  const GRAFICO = {
    labels: [],
    valores: [],
  }

  // import GraficoBarV from '@/components/Grafico/Financa/BarVertica'
  // import TableAnalise from "@/pages/Financa/components/Table_AnaliseAno.vue"

  import service from "@/service.js";
  import {mapState} from 'vuex';

  export default {
    props: ['tipo', 'FITP_ID'],

    computed: { ...mapState(['financa','periodo']) },

    // components: { 
    //   TableAnalise, 
    //   GraficoBarV
    // },

    data() {
      return {
        registros:{
          items: [],
          items_GRAFICO: GRAFICO,
        },
        th: [],
        time: false,

      };
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        this.th = service.arrMeses
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.registros.items = [];
        this.registros.items_GRAFICO = GRAFICO;

        this.time = false;

        setTimeout(() => {

          var endPoint = "";
          endPoint += "analise-ano";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&mes=${this.$store.getters.Periodo}`;
          endPoint += `&FINC_ID=${this.$store.getters.F_FINC_ID}`;
          endPoint += `&FITP_ID=${this.FITP_ID}`;

          service.finc.busca(endPoint).then(({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              var {RECEITA, DESPESA} = data

              var res = (this.FITP_ID == 1) ? RECEITA : DESPESA

              if(this.tipo ==  'Grupo') {
                this.registros.items         = res.FIGP
                this.registros.items_GRAFICO = res.FIGP_GRAFICO;

              } 
              else if( this.tipo == 'Categoria') {
                this.registros.items         = res.FICT
                this.registros.items_GRAFICO = res.FICT_GRAFICO;

              }

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
      'financa.FINC_ID'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        this.getDados()
      },
    },
  }
</script>
