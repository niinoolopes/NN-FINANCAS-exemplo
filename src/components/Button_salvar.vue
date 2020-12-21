<template>
  <button 
    type="button" 
    class="div-btn btn-hover btn btn-sm btn-outline-info py-0 my-0 d-flex justify-content-center align-items-center" 
    @click="salvar"
    :disabled="!disabled"
  >
    <i class="far fa-save"></i>
  </button>
</template>

<script>
  export default {
    props:['salvar', 'disabled']
  }
</script>

<style scoped>
button {
  font-size: 17px;
}
</style>