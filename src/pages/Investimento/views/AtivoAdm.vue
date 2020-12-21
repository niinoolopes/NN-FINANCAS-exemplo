<template>
  <TemplateDefault title="Ativos">

    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar os rendimentos!"/>

    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'Tabs_AtivoAdm_consolidado'">Consolidado</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'Tabs_AtivoAdm_operacoes'  ">Operações</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'Tabs_AtivoAdm_rendimentos'">Rendimentos</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'Tabs_AtivoAdm_cotacao'    ">Cotação</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'Tabs_AtivoAdm_split'      ">Split</a>
        </div>
      </template>

      <template v-slot:btn></template>
    </PageNav>
    

    <!-- <PageSection> -->
      <keep-alive>
        <component v-bind:is="tab" :INAV_ID="INAV_ID"></component>
      </keep-alive>
    <!-- </PageSection> -->

  </TemplateDefault>
</template>

<script>
  export default {

    components: { 
      Tabs_AtivoAdm_consolidado: () => import('@/pages/Investimento/components/Tabs_AtivoAdm_consolidado'),
      Tabs_AtivoAdm_operacoes:   () => import('@/pages/Investimento/components/Tabs_AtivoAdm_operacoes'),
      Tabs_AtivoAdm_rendimentos: () => import('@/pages/Investimento/components/Tabs_AtivoAdm_rendimentos'),
      Tabs_AtivoAdm_cotacao:     () => import('@/pages/Investimento/components/Tabs_AtivoAdm_cotacao'),
      Tabs_AtivoAdm_split:       () => import('@/pages/Investimento/components/Tabs_AtivoAdm_split'),
    },

    data() {
      return {
        tab: 'Tabs_AtivoAdm_consolidado',
        INAV_ID: this.$route.params.INAV_ID
      }
    },
  }
</script>