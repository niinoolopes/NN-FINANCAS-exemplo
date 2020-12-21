<template>
  <div class="d-flex justify-content-center">
    <button 
      class="bg-transparent border-0"
      data-toggle="modal" 
      :data-target="`#${target}`"
      @click="setItem(ID)"
    >
      <i class="text-black-50 far fa-edit"></i>
    </button>
  </div>
</template>

<script>
  export default {
    props:['target', 'ID', 'setItem']
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