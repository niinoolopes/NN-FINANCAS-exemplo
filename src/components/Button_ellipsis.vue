<template>
  <button 
    type="button" 
    class="div-btn py-0 btn-hover btn btn-sm btn-outline-info"
    data-toggle="modal" :data-target="`#${target}`" 
    :style="styles"
  >
    <i class="fas fa-ellipsis-h"></i>
  </button>
</template>

<script>
  export default {
    props:['disabled', 'target'],
    
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