<template>
  <TemplateDefault title="Extrato operações" >
    
    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar os rendimentos!"/>

    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabOperacaoMes' ">Mês</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabMovimentacao'">Movimentação</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabHistorico'   ">Histórico</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabInativos'    ">Inativo</a>
        </div>
      </template>

      <!-- <template v-slot:btn></template> -->
    </PageNav>

    <PageSection>
      <keep-alive>
        <component v-bind:is="tab" :setItem="setItem" :INIT="itemModal"></component>
      </keep-alive>
    </PageSection>


    <ModalOrdemAtivo
      :INIT='itemModal'
      :setItem="setItem"
    />
 
  </TemplateDefault>
</template>

<script>
  export default {
    components: {
      TabOperacaoMes:  () => import('@/pages/Investimento/components/Tabs_ExtratoOperacao_mes'),
      TabMovimentacao: () => import('@/pages/Investimento/components/Tabs_ExtratoOperacao_movimentacao'),
      TabHistorico:    () => import('@/pages/Investimento/components/Tabs_ExtratoOperacao_historico'),
      TabInativos:     () => import('@/pages/Investimento/components/Tabs_ExtratoOperacao_inativo'),
      ModalOrdemAtivo: () => import('@/pages/Investimento/components/Modal_Ordem_ativo')
    },

    data() {
      return {
        tab: 'TabOperacaoMes',
        itemModal: {msg: 'init', value: false},
      }
    },

    methods: {
      setItem(INAR) {
        this.itemModal = INAR
      }
    },
  }
</script>