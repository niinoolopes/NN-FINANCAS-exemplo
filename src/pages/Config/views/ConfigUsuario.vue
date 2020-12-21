<template>
  <TemplateDefault title="Config / Usuario">
    <PageNav>
        <template v-slot:menu>
          <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" data-toggle="tab" @click="tab = 'TabDados'">Dados</a>
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
  export default {
    components: { 
      TabDados: () => import('./../components/tab_ConfigUsuario_dados')
    },

    data() {
            return {
        tab: 'TabDados'
      }
    },
  }
</script>