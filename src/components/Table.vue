<template>
  <section class="border over-x opacity-fetch" :class="timeTable ? '' : 'opacity-fetch-active'">
    <table class="table table-sm">

      <thead>
        <tr>
          <slot name="thead"> </slot>
        </tr>
      </thead>
        
      <tbody v-if="Array.isArray(itemsTable)">

          <template v-if="itemsTable.length > 0">
            <slot name="tbody"></slot>
          </template>

          <template v-else>
            <td :colspan="colspan">Não existe registros</td>
          </template>

      </tbody>

      <tbody v-else>
        <td :colspan="colspan">Buscando dados ...</td>
      </tbody>

    </table>
  </section>
</template>

<script>
  export default {
    props: ['itemsTable', 'timeTable', 'colspan'],
  }
</script>