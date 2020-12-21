<template>
  <button
      type="button"
      class="div-btn btn-hover btn btn-sm btn-outline-info py-0 mr-2"
      data-toggle="modal"
      :style="styles"
      :data-target="`#${target}`"
      @click="setItem(ID)"
    >
    <i class="fas fa-plus"></i>
  </button>
</template>

<script>
  export default {
    props:['setItem', 'ID', 'target', 'disabled'],

    data() {
      return {
        styles: {}
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