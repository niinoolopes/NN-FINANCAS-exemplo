<template>
  <PageSection class="opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">

    <PageContentTitle titulo='Consolidado por Mês' >
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentTitle>

    <GraficoLine 
      :arrLabels="arrMeses"
      :arrDados="arrDados" 
      color="financa"
      :style="{height: '225px'}"
    />

  </PageSection>
</template>

<script>
  import service from "@/service.js"

  export default {

    components: { 
      ButtongetDados: () => import('@/components/Button_getDados'),
      GraficoLine:    () => import('@/components/Grafico/Line-generico')
    },
  
    data() {
      return {
        time: false,
        arrMeses: [],
        arrDados: [
          {label: 'RECEITA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'DESPESA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'SOBRA',    valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'ESTIMADO', valores:[0,0,0,0,0,0,0,0,0,0,0,0]}
        ]
      }
    },

    created () {
      this.arrMeses = service.arrMeses;
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados(){
        this.arrDados = [
          {label: 'RECEITA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'DESPESA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'SOBRA',    valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'ESTIMADO', valores:[0,0,0,0,0,0,0,0,0,0,0,0]}
        ]
        this.time = false

        setTimeout( () => {

          var endPoint = '';
          endPoint += 'analise-ano-consolidado';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&mes=${this.$store.getters.Periodo}`;
          endPoint += `&FINC_ID=${this.$store.getters.F_FINC_ID}`;
          
          service.finc.busca(endPoint).then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              const {RECEITA, DESPESA, SOBRA, ESTIMADO} = data;
            
              this.arrDados[0].valores = RECEITA
              this.arrDados[1].valores = DESPESA
              this.arrDados[2].valores = SOBRA
              this.arrDados[3].valores = ESTIMADO

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
      }
    },

  }
</script>