<template>
  <div id="sidenav" class="min-vh-100 col-md-3 col-lg-2 p-0 d-flex flex-column align-content-center shadow" >
    <Header />
    <Menu v-if="$store.state.login" />
    <Footer v-if="$store.state.login" />
  </div>
</template>

<script>
  import Header from './Sidenav_header'
  import Menu   from './Sidenav_menu'
  import Footer from './Sidenav_footer'

  export default {
    name: 'Sidnav',

    components: { Header, Menu, Footer },
  }
</script>

<style scoped>
  #sidenav{
    background: linear-gradient(150deg, var(--gray-dark) 0%, var(--info) 100%);
    border: 2px solid var(--gray-dark);
  }

  @media screen and (max-width: 765px){
    #sidenav{
      transition: all 0.3s;
      position: absolute;
      z-index: 1;
      top: 0;
      left: -100%;
      right: 0;
    }
    .sidenav--active{
      left: 0 !important;
    }
  }
  
  footer > a{
    opacity: 0.5;
  }

  footer > a:hover {
    background: rgba(255,255,255,0.15);
    opacity: 1;
  }
</style>