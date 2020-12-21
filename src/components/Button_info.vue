<template>
  <button
    type="button"
    class="div-btn py-0 btn-hover btn btn-sm btn-outline-info"
    :style="styles"
    @click="click"
  >
    <i class="fas fa-info-circle"></i>
  </button>
</template>

<script>
  export default {
    props:['click', 'disabled', 'border'],

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