<template>
  <TemplateDefault title="Cadastrar">

    <Alert v-if="$store.getters.F_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel"/>

    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabForm'    ">Cadastrar</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabSugestao'">Sugestão</a>
        </div>
      </template>
      <!-- <template v-slot:btn></template> -->
    </PageNav>

    <PageSection>
      <keep-alive>
        <component v-bind:is="tab" :setItem="setItem" :FNIT_ID="FNIT_ID"></component>
      </keep-alive>
    </PageSection>

  </TemplateDefault>
</template>

<script>
  export default {
    components: { 
      TabForm:     () => import('@/pages/Financa/components/Tabs_Cadastrar_form'),
      TabSugestao: () => import('@/pages/Financa/components/Tabs_Cadastrar_sugestao'),
    },
    
    data() {
      return {
        tab: 'TabForm',
        FNIT_ID: 'init',
      }
    },
    
    methods: {
      setItem(FNIT_ID){
        this.FNIT_ID = FNIT_ID
        
        document.querySelector('.nav-item').click();
      }
    }
  }
</script>