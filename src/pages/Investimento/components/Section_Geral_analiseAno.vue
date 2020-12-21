<template>
  <section>
    <PageContentTitle titulo='Analise ano' >
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' border='0px' />
      </template>
    </PageContentTitle>

    <PageNav>
      <template v-slot:menu>
        <button class="btn btn-sm btn-outline-info py-0 mr-1" @click='clickAnaliseAno("carteiraAno")  '>Carteira</button>
        <button class="btn btn-sm btn-outline-info py-0 mr-1" @click='clickAnaliseAno("aporte")    '>Aportes</button>
        <button class="btn btn-sm btn-outline-info py-0 mr-1" @click='clickAnaliseAno("rendimento")'>Rendimentos</button>
      </template> 
      <template v-slot:btn>
      </template>
    </PageNav> 

    <GraficoLine
      color='investimento'
      :arrLabels="arrMeses"
      :arrDados="dados" 
      :style="{height: '200px'}"
    />
  </section>
</template>

<script>
  import service from "@/service.js"

  export default {
    props:['getDados','arrDados'],

    components: {
      ButtongetDados: () => import('@/components/Button_getDados'),
      GraficoLine:    () => import('@/components/Grafico/Line-generico'),
    },
    
    data() {
      return {
        dados: [],
      }
    },

    created () {
      this.arrMeses = service.arrMeses
      this.dados = this.arrDados['carteiraAno']

    },

    methods: {
      clickAnaliseAno(tipo){
        this.dados = this.arrDados[tipo]
      },
    },
  }
</script>