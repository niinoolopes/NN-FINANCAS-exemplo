<template>
  <button 
    type="button" 
    class="div-btn py-0 btn-hover btn btn-sm btn-outline-secondary py-0 my-0 d-flex justify-content-center align-items-center" 
    data-dismiss="modal"
    :id="`${id}-close`"
  >
    <i class="fas fa-times"></i>
  </button>
</template>

<script>
  export default {
    props: {
      id: {
        type: String,
        default: ''
      },
    },
  }
</script>
