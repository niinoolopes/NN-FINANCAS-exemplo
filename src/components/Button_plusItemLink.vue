<template>
  <router-link 
    class="div-btn py-0 btn-hover btn btn-sm btn-outline-info" 
    :to="{name: to}"
    :style="styles"
  >
    <i class="fas fa-plus"></i>
  </router-link>
</template>

<script>
  export default {
    props:['disabled', 'to'],
    
    data() {
      return {
        styles: {
          paddingTop: '3px',
          pointerEvents: 'all',
        },
        disabledValue: false
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