<template>
  <section>
    
    <SectionAnalise tipo='Grupo' FITP_ID="1" />

    <SectionAnalise tipo='Categoria' FITP_ID="1" />

  </section>
</template>

<script>
  export default {
    components: { 
      SectionAnalise: () => import('@/pages/Financa/components/Section_AnaliseAno'),
    },
  }
</script>
