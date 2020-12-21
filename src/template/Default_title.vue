<template>
  <header v-if="title">

    <transition appear mode="out-in" name="fade_title">

      <LoadingTitle v-if="fade" key="LoadingTitle" />

      <h1 v-else key="h1" 
        :style="{fontSize: `${sizeValor}rem`}"
        class="m-0">{{title}}</h1>

    </transition>
    
    <hr class="my-1">

  </header>
</template>

<script>
  export default {
    props: ['time','title'],
    
    components: {
      LoadingTitle: () => import('@/components/Loading/LoadingTitle.vue'),
    },

    data() {
      return {
        fade: true,
        sizeValor: '2',
      }
    },

    created () {
      this.sizeValor = window.innerWidth > 500 ? '2.5' : '1.75'
      this.fade = this.time;
    },

    watch: {
      time(newValue, oldValue) {
        this.fade = this.time;
      }
    },
  }
</script>

<style scoped>

  .fade_title-enter, .fade_title-leave-to{
    opacity: 0;
    transition: all 0.3s;
  }

  .fade_title-enter-active, .fade_title-leave-active{
    opacity: 0;
    transition: all 0.3s;
  }

</style>