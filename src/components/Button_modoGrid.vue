<template>
  <button 
      type="button" 
      class="div-btn btn-hover btn btn-sm btn-outline-info pb-0" 
      @click="setModo(modo)"
    >
    <i class="fas fa-th-large"></i>
  </button>
</template>

<script>
  export default {
    props:['setModo','modo']
  }
</script>