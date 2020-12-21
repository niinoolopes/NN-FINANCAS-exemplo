<template>
  <section>
    <PageContentTitle titulo='Saldo mês'>
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' border='0px' />
      </template>
    </PageContentTitle>

    <Cards :time="time" :labels="labels" :valores="valores" />
  </section>
</template>

<script>
  export default {
    props:['getDados', 'time', 'labels', 'valores'],

    components: {
      ButtongetDados: () => import('@/components/Button_getDados'),
      Cards:          () => import('@/components/Cards')
    },
    
  }
</script>