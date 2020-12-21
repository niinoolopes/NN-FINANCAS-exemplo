<template>
  <TemplateDefault title="Split / Inplit">
    
    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar as ordens!"/>

    <PageNav>
      <!-- <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabsSplit' ">Split</a>
          <a class="nav-item nav-link       " data-toggle="tab" @click="tab = 'TabsInplit'">Inplit</a>
        </div>
      </template> -->

      <template v-slot:btn>
        <ButtonsetItem :setItem="setItem" ID="novo" target='InvestimentoModal-Split' :disabled='!$store.getters.I_CarteiraPainel' />
      </template>
    </PageNav>

    <PageSection>
      <keep-alive>
        <component v-bind:is="tab" :INAS="itemModal" :setItem="setItem" ></component>
      </keep-alive>
    </PageSection>

    <ModalSplit :INAS="itemModal" :setItem="setItem" />

  </TemplateDefault>
</template>

<script>
  export default {
    
    components: { 
      ButtonsetItem: () => import('@/components/Button_setItem'),
      TabsSplit:     () => import('@/pages/Investimento/components/Tabs_Split_split'),
      TabsInplit:    () => import('@/pages/Investimento/components/Tabs_Split_inplit'),
      ModalSplit:    () => import('@/pages/Investimento/components/Modal_Split'),
    },

    data() {
      return {
        tab: 'TabsSplit',
        itemModal: 'init',
      }
    },

    methods: {
      setItem(INAS) {
        this.itemModal = INAS
      }
    },
  }
</script>