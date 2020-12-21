<template>
  <article class="col-12 px-1 px-md-2 py-3 mb-2 mb-lg-3 bg-white shadow" :class="css">
    <slot></slot>
  </article>
</template>

<script>
  export default {
    props: {
      css: {
        type: String,
        default: ''
      },
    },
  }
</script>

<style scoped>
  .article-limit {
    max-height: 50vh;
    overflow: hidden;
    overflow-y: auto;
  }
</style>
