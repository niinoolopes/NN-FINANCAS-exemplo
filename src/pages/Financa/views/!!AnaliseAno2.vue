<template>
  <TemplateDefault title="Análise ano">

    <Alert v-if="$store.getters.F_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar analise por ano!"/>

    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabReceita'">Receita</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabDespesa'">Despesa</a>
        </div>
      </template>

      <!-- <template v-slot:btn></template> -->
    </PageNav>

    <div class="w-100">
      <keep-alive>
        <component v-bind:is="tab"></component>
      </keep-alive>
    </div>
    
  </TemplateDefault>
</template>

<script>

  export default {
    components: { 
      TabReceita: () => import('@/pages/Financa/components/Tabs_AnaliseAno_receita'),
      TabDespesa: () => import('@/pages/Financa/components/Tabs_AnaliseAno_despesa'),
    },

    data() {
      return {
        tab: 'TabReceita',
      }
    },
  }
</script>