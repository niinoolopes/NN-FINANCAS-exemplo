<template>
  <div class="col-12 p-0 mb-1 d-flex flex-wrap">
    <div class="col p-0 pr-1">
      <h5 class="p-0 m-0">{{titulo}}</h5>
    </div>

    <div>
      <slot name="btn"></slot>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      titulo: {
        type: String,
        default: 'Titulo não definido'
      },
    },
  }
</script>