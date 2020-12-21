<template>
  <TemplateDefault title="Config / Investimento">
  
    <PageNav>
        <template v-slot:menu>
          <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabCarteira'"  >Carteira</a>
            <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabCorretora'" >Corretora</a>
            <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabAtivo'"     >Ativo</a>
            <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabAtivoTipo'" >Tipo de Ativo</a>
            <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabTipo'"      >Tipo de Investimento</a>
          </div>
        </template>

        <template v-slot:btn> </template>
    </PageNav>

    <PageSection>
      <keep-alive>
        <component v-bind:is="tab" ></component>
      </keep-alive>
    </PageSection>
    
  </TemplateDefault> 
</template>

<script>

  import TabCarteira  from '@/pages/Config/components/tab_ConfigInvestimento_carteira'
  import TabCorretora from '@/pages/Config/components/tab_ConfigInvestimento_corretora'
  import TabAtivo     from '@/pages/Config/components/tab_ConfigInvestimento_ativo'
  import TabAtivoTipo from '@/pages/Config/components/tab_ConfigInvestimento_ativoTipo'
  import TabTipo      from '@/pages/Config/components/tab_ConfigInvestimento_tipo'

  export default {

    components: {
      TabAtivo,
      TabAtivoTipo,
      TabCarteira,
      TabCorretora,
      TabTipo,
    },
    
    data() {
      return {
        tab: 'TabCarteira'
      }
    },
  }
</script>