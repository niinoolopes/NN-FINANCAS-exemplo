<template>
  <button 
    type="button" 
    class="div-btn py-0 btn-hover btn btn-sm btn-outline-info" 
    :style="styles"
    @click="limpar()"
  >
    <i class="fas fa-broom"></i>
  </button>
</template>

<script>
  export default {
    props:['disabled', 'limpar'],

    data() {
      return {
        styles: {pointerEvents: 'all'}
      }
    },

    mounted () {
      let pointerEvents = 'all'
      if(this.disabled === undefined) pointerEvents = 'all'
      if(this.disabled === false    ) pointerEvents = 'all'
      if(this.disabled === true     ) pointerEvents = 'none'

      this.styles.pointerEvents = pointerEvents
    },
  }
</script>