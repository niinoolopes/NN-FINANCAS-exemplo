<template>
  <section class="d-flex flex-wrap">
    <div v-for="(texto, i) in labels" :key="i" class="col-6 col-lg-3 p-0 p-sm-1 mb-2 mb-sm-1 mb-md-0">
      <div class="card shadow-sm">

        <p class="font-weight-bold bg-info text-white m-0 py-1 pl-2 d-flex justify-content-between align-items-center">{{texto}}</p>

        <p class="d-block text-right font-weight-normal pr-2 m-0" 
          :style="{fontSize: `${sizeValor}rem`}"
          :class="time ? '' : 'opacity-fetch-active'">
          <span v-if="time">{{valores[i] | vReal}}</span>
          <span v-else class="opacity-fetch" >...</span>
        </p>
        
      </div>
    </div>
  </section>
</template>

<script>
  export default {
    props: ['time', 'labels', 'valores'],

    data() {
      return {
        sizeValor: '2',
      }
    },
    
    mounted () {
      this.sizeValor = window.innerWidth > 500 ? '2' : '1.5'
    },
  }
</script>