<template>
  <nav v-if="link" aria-label="breadcrumb">
    <ol class="breadcrumb py-2">

      <li v-for="(link,i) in link" :key="i" class="breadcrumb-item d-flex align-items-center">
        <router-link v-if="link.to" class="text-info" :to="link.to">{{link.text}}</router-link>
        <a v-else class="text-muted active">{{link.text}}</a>
      </li>

    </ol>
  </nav>
</template>

<script>
  export default {
    props: ['link'],
  }
</script>

<style scoped>
  ol {
    min-height: 37px;
  }

  a{
    font-size: 14px;
  }

  .breadcrumb-item + .breadcrumb-item::before{
    margin-top: -5px;
  }
</style>