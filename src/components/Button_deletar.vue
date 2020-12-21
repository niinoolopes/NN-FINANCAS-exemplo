<template>
  <button 
    type="button" 
    class="div-btn py-0 btn-hover btn btn-sm btn-outline-danger my-0 d-flex justify-content-center align-items-center" 
    @click="deletar"
    :disabled="!disabled"
  >
    <i class="far fa-trash-alt"></i>
  </button>
</template>

<script>
  export default {
    props:['deletar', 'disabled']
  }
</script>