<template>
  <TemplateDefault title="Ordem">

    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar as ordens!"/>
    
    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabOrdemAdm'"                  >Ordem</a>
          <a class="nav-item nav-link       " data-toggle="tab" @click="tab = 'TabOrdemItems'" v-if="INOD_ID" >Items</a>
        </div>
      </template>
      <!-- <template v-slot:btn></template> -->
    </PageNav>

    <PageSection>
      <div class="tab-content">
        <keep-alive>
          <component v-bind:is="tab" :setOrdem="setOrdem" :INOD_ID="INOD_ID" ></component>
        </keep-alive>
      </div>
    </PageSection>

  </TemplateDefault>
</template>

<script>
  
  export default {
    components: { 
      TabOrdemAdm:   () => import('@/pages/Investimento/components/Tabs_OrdemAdm_adm'),
      TabOrdemItems: () => import('@/pages/Investimento/components/Tabs_OrdemAdm_items'),
    },

    data() {
      return {
        INOD_ID: false,
        tab: 'TabOrdemAdm',
      }
    },

    mounted () {
      if(this.$route.params.INOD_ID){
        this.INOD_ID = this.$route.params.INOD_ID
      }
    },

    methods: {
      setOrdem(INOD_ID){
        this.INOD_ID = INOD_ID;
      }
    },
  }
</script>