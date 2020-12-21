<template>
  <TemplateDefault title="Config / Finança">



    <PageNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabCarteira'"  >Carteira</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabGrupo'"     >Grupo</a>
          <a class="nav-item nav-link"        data-toggle="tab" @click="tab = 'TabCategoria'" >Categoria</a>
        </div>
      </template>

      <template v-slot:btn> </template>
    </PageNav>

    <PageSection>
      <component v-bind:is="tab" ></component>
    </PageSection>
    
  </TemplateDefault> 
</template>

<script>

  import TabCarteira from './../components/tab_ConfigFinanca_carteira'
  import TabGrupo from './../components/tab_ConfigFinanca_grupo'
  import TabCategoria from './../components/tab_ConfigFinanca_categoria'

  export default {

    components: {  
      TabCarteira,
      TabGrupo,
      TabCategoria 
    },

    data() {
            return {
        tab: 'TabCarteira'
      }
    },
  }
</script>