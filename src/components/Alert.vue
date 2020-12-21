<template>
  <div class="w-100 alert alert-dismissible fade show" role="alert" :class="`alert-${tipo}`">
    
    <strong>Alerta: </strong> {{texto}}

    <button v-if="btn" type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>

  </div>
</template>

<script>
  export default {
    // props: ['texto','tipo'],

    props: {
      texto: {
        type: String,
        default: 'texto não definido'
      },
      tipo: {
        type: String,
        default: 'success'
      },
      btn: {
        btn: String,
        default: false
      },
    },
  }
</script>

<style scoped>

</style>