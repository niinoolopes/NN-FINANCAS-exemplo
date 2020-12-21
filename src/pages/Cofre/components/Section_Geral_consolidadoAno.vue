<template>
  <section>

    <PageContentTitle :titulo='titulo'>
      <template v-slot:btn>
        <ButtongetDados 
          v-if='typeof getDados == "function"'
          :getDados='getDados' border='0px' />
      </template>
    </PageContentTitle>

    <GraficoLine 
      :arrLabels="arrMeses"
      :arrDados="dados" 
      color="cofre"
      :style="{height: '225px'}"
    />

  </section>
</template>

<script>
  import service from '@/service.js'

  export default {
    props: ['titulo','getDados', 'dados'],

    components: {
      ButtongetDados: () => import('@/components/Button_getDados'),
      GraficoLine:    () => import('@/components/Grafico/Line-generico'),
    },

    data() {
      return {
        arrMeses: []
      }
    },

    created () {
      this.arrMeses = service.arrMeses;
    },
  }
</script>