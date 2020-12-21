<template>
  <div class="d-flex justify-content-center">
    <button 
      class="bg-transparent border-0"
      @click="trash(ID)"
    >
      <i class="text-black-50 far fa-trash-alt"></i>
    </button>
  </div>
</template>

<script>
  export default {
    props:['ID', 'trash']
  }
</script>

<style scoped>
div {
  min-width: 25px;
}
div button i{
  font-size: 16px;
}
</style>