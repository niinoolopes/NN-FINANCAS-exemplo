<template>
  <TemplateDefault title="Cotação">
    
    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar as ordens!"/>
    
    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'tabListagem'">Listagem</a>
          <a class="nav-item nav-link       " data-toggle="tab" @click="tab = 'tabSugestao'">Sugestão</a>
        </div>
      </template>
      <!-- <template v-slot:btn></template> -->
    </PageNav>

    <PageSection>
      <div class="tab-content">
        <keep-alive>
          <component v-bind:is="tab"></component>
        </keep-alive>
      </div>
    </PageSection>

  </TemplateDefault>
</template>

<script>
  export default {
    components: { 
      TabListagem: () => import('@/pages/Investimento/components/Tabs_Cotacao_listagem'),
      TabSugestao: () => import('@/pages/Investimento/components/Tabs_Cotacao_sugestao'),
    },

    data() {
      return {
        tab: 'tabListagem',
      }
    },
  }
</script>