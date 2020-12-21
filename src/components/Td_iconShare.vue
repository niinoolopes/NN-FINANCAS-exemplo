<template>
  <div class="d-flex justify-content-center">
    <router-link :to="{name: to, params:params}">
      <i class="text-dark cursor-pointer fas fa-share"></i>
    </router-link>
  </div>
</template>

<script>
  export default {
    props:['to', 'params']
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