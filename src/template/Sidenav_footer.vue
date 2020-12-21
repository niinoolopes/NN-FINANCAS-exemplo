<template>
    <footer>

      <ul class="list-unstyled m-0 d-flex flex-wrap">
          <router-link  class="col-6 d-flex justify-content-center text-white px-3 py-2 hover-none" 
            :to="{name: 'Config'}"> <i class="fas fa-cog"></i> </router-link>
          <router-link  class="col-6 d-flex justify-content-center text-white px-3 py-2 hover-none" 
            :to="{name: 'Logout'}"> <i class="fas fa-sign-in-alt"></i> </router-link>
      </ul>

      <div class="col-12 m-0">
        <div class="text-white text-center"> @niinoocode </div>
      </div>

    </footer>
</template>

<script>
  export default {
    name: 'Sidnav_menu',
  
  }
</script>

<style scoped>
  footer a{
    opacity: 0.5;
    border: 1px solid rgba(255,255,255, 0.50);
  }
  footer a:hover {
    background: rgba(255,255,255,0.15);
    opacity: 1;
  }
  footer .fas{
    font-size: 24px;
  }
</style>