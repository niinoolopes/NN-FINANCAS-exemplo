<template>
  <div class="modal fade" :id="idModal">
    <div class="modal-dialog" role="document" :class="modal">
      <div class="modal-content">
        <div class="modal-body p-2 pt-3 opacity-fetch" :class="timeModal ? '' : 'opacity-fetch-active'">
          <form class="d-flex flex-wrap" v-on:submit.prevent="salvar">
            <slot></slot>
          </form>
        </div>

        <div class="modal-footer p-2">
            <ButtonSalvar  v-if="typeof(salvar)  == 'function'" :disabled="timeModal" :salvar="salvar" />
            <ButtonDeletar v-if="typeof(deletar) == 'function'" :disabled="timeModal" :deletar="deletar" :ID="idItem" />
            <ButtonFecharModal :id='idModal' />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props:['modal', 'idModal', 'timeModal', 'idItem', 'salvar', 'deletar'],

    components: {
      ButtonSalvar:      () => import('@/components/Button_salvar'),
      ButtonDeletar:     () => import('@/components/Button_deletar'),
      ButtonFecharModal: () => import('@/components/Button_fecharModal'),
    },
  }
</script>