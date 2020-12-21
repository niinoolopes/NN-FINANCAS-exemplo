<template>
  <TemplateDefault title="Extrato" >

    <Alert v-if="$store.getters.C_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar o extrato!"/>

    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabExtratoMes'"         >Mês</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabExtratoMovimentacao'">Movimentação</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabExtratoHistorico'"   >Histórico</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabExtratoInativo'"     >Inativo</a>
        </div>
      </template>

      <!-- <template v-slot:btn>
        <button
          type="button"
          class="btn-hover btn btn-sm btn-outline-info py-0 mr-2"
          data-toggle="modal"
          data-target="#FinancaModal-Item"
          @click="setItem('novo')"><i class="fas fa-plus"></i></button>
      </template> -->

    </PageNav>

    <PageSection>
      <keep-alive>
        <component v-bind:is="tab" :COCT="COCT" :setItem="setItem"></component>
      </keep-alive>
    </PageSection>

    <ModalCofreItem :COCT="COCT" :setItem="setItem"/>

  </TemplateDefault>
</template>

<script>
  export default {

    components: { 
      TabExtratoMes:          () => import('@/pages/Cofre/components/Tabs_Extrato_mes'),
      TabExtratoMovimentacao: () => import('@/pages/Cofre/components/Tabs_Extrato_movimentacao'),
      TabExtratoHistorico:    () => import('@/pages/Cofre/components/Tabs_Extrato_historico'),
      TabExtratoInativo:      () => import('@/pages/Cofre/components/Tabs_Extrato_inativos'),

      ModalCofreItem:    () => import('@/pages/Cofre/components/Modal_Form_item'),
    },

    data() {
      return {
        tab: 'TabExtratoMes',
        COCT: 'init'
      }
    },

    methods: {
      setItem(COCT) {
        setTimeout( () => this.COCT = ''  , 250)
        setTimeout( () => this.COCT = COCT, 250)
      }
    },
  }
</script>
