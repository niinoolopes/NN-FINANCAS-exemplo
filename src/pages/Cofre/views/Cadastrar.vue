<template>
  <TemplateDefault title="Cadastrar">

    <Alert v-if="$store.getters.C_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel"/>

    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabForm'    ">Cadastrar</a>

        </div>
      </template>
      
      <!-- <template v-slot:btn></template> -->
    </PageNav>

    <PageSection>
      <keep-alive>
        <component v-bind:is="tab"></component>
      </keep-alive>
    </PageSection>
        
  </TemplateDefault>
</template>

<script>
  export default {
    components: { 
      TabForm: ()=> import('@/pages/Cofre/components/Tabs_Cadastrar_form'),
      
    },

    data() {
      return {
        tab: 'TabForm',
      }
    },
    
  }
</script>