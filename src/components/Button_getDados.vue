<template>
  <button
    type="button"
    class="div-btn py-0 btn-hover btn btn-sm btn-outline-info"
    :style="styles"
    @click="getDados"
  >
    <i class="fas fa-sync"></i>
  </button>
</template>

<script>
  export default {
    props:['disabled', 'getDados', 'border'],

    data() {
      return {
        styles: {
          pointerEvents: 'all',
          borderWidth: '',
        }
      }
    },

    mounted () {
      let pointerEvents = 'all'
      if(this.disabled === undefined) pointerEvents = 'all'
      if(this.disabled === false    ) pointerEvents = 'all'
      if(this.disabled === true     ) pointerEvents = 'none'
      this.styles.pointerEvents = pointerEvents

      if(this.border !== undefined) this.styles.borderWidth = this.border
    },
  }
</script>