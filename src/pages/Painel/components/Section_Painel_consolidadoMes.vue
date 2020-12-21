<template>
  <PageSection class="opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">

    <PageContentTitle titulo='Consolidado do Mês' >
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentTitle>

    <GraficoPie 
      :arrDados="arrDados" 
      :style="{height: '225px'}"
    />

  </PageSection>
</template>

<script>

  import service from "@/service.js"

  export default {

    components: { 
      ButtongetDados: () => import('@/components/Button_getDados'),
      GraficoPie: () => import('@/components/Grafico/Financa/Pie-ConsolidadoMes')
    },
  
    data() {
      return {
        time: false,
        arrDados: {
          labels: ["Receita", "Despesa", "Sobra", "Estipulado"],
          valores: [0,0,0,0]
        }
      }
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados(){
        this.arrDados.valores = [0,0,0,0]
        this.time = false

        setTimeout( () => {

          var endPoint = '';
          endPoint += 'mes-consolidado-valores';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&mes=${this.$store.getters.Periodo}`;
          endPoint += `&FINC_ID=${this.$store.getters.F_FINC_ID}`;

          service.finc.busca(endPoint).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              const {RECEITA, DESPESA, SOBRA, ESTIMADO} = data;

              this.arrDados.valores = [RECEITA, DESPESA, SOBRA, ESTIMADO]
            }
            else if(STATUS == 'erro'){
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

    // watch: {
    //   'arrDados.valores':{
    //     handler(val){
    //       this.renderGrafico()
    //     // do stuff
    //   },
    //   deep: true
    //   }
    // },
  }
</script>