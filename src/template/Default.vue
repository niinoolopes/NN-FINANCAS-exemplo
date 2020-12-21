<template>
  <main class="min-vh-100 col-md-9 col-lg-10 px-2 px-md-3 py-3 py-md-4">

    <Title :time="loading" :title="title"/>

    <transition mode="out-in" name="fade_breadcrumb">
      <div v-if="loading" key="loading"></div>
      <Breadcrumb v-else key="breadcrumb" :link="link"/>
    </transition>

    <transition mode="out-in" name="fade_content">
      <div v-if="loading" key="loading"></div>
      <section v-else key="content" class="d-flex flex-wrap">
        <slot></slot>
      </section>
    </transition>

  </main>
</template>

<script>
  import Title from '@/template/Default_title.vue';
  import Breadcrumb from '@/template/Default_breadcrumb.vue';
  import BreadcrumbList from '@/Breadcrumb.js'
  
  import {mapState} from 'vuex';

  export default {
    props: ['title'],

    computed: { ...mapState(['login']) },

    components: {
      Title,
      Breadcrumb,
    },

    data() {
      return {
        loading: true,
        link: [],
      }
    },
    
    created () {
      this.link = BreadcrumbList[this.$route.name]
    },

    mounted () {
      setTimeout( () => { this.loading = false } , 500 )
    },

  }
</script>

<style scoped>
  main {
    overflow-y: auto;
    max-height: 100vh;
  }

  

  /* TRANSACAO LOADING */
  .fade_breadcrumb-enter, .fade_breadcrumb-leave-to{ 
    opacity: 0;
    transition: all 0.3s;
  }
  .fade_breadcrumb-enter-active, .fade_breadcrumb-leave-active{ 
    opacity: 0;
    transition: all 0.3s;
  } 


  .fade_content-enter-active, .fade_content-leave-active {
    opacity: 0;
    transition: all 0.3s;
  }
  .fade_content-enter,  .fade_content-leave-to{ 
    opacity: 0;
    transition: all 0.3s;
    transform: translate3d( 0, -5px, 0);
  }
</style>