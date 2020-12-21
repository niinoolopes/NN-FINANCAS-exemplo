<template>
  <span v-if="this.$store.state.login" id="menu" class="d-md-none" @click="toggleMenu">
    <i id="menu-icon" class="fas fa-bars"></i>
  </span>
</template>

<script>
  export default {
    methods: {
      toggleMenu() {
        document.getElementById("sidenav").classList.toggle('sidenav--active')
      }
    },
  }
</script>

<style scoped>
 #menu {
  position: fixed;
  
  top: 10px;
  right: 10px;
  z-index: 10;

  display: flex;
  justify-content: center;
  align-items: center;

  width: 30px;
  height: 30px;
  cursor: pointer;
 }
 #menu-icon{
  font-size: 22px;
 }
</style>